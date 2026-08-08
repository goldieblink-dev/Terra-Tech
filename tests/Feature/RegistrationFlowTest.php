<?php

namespace Tests\Feature;

use App\Models\RegistrationStep;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $editor;
    protected User $operator;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'editor']);
        Role::create(['name' => 'operator']);

        $this->editor = User::factory()->create();
        $this->editor->assignRole('editor');

        $this->operator = User::factory()->create();
        $this->operator->assignRole('operator');
    }

    public function test_editor_can_create_registration_step_with_image(): void
    {
        $image = UploadedFile::fake()->create('step1.png', 100, 'image/png');

        $response = $this->actingAs($this->editor)
            ->post(route('cms.registration-steps.store'), [
                'title'              => 'Langkah 1 - Akun Baru',
                'description'        => 'Buat akun di portal resmi.',
                'requirements'       => "KTP\nPas Foto 3x4\nEmail Aktif",
                'icon'               => 'user-plus',
                'sort_order'         => 1,
                'status'             => 'published',
                'illustration_image' => $image,
            ]);

        $response->assertRedirect(route('cms.registration-steps.index'));
        $this->assertDatabaseHas('registration_steps', ['title' => 'Langkah 1 - Akun Baru']);

        $step = RegistrationStep::where('title', 'Langkah 1 - Akun Baru')->first();
        $this->assertNotNull($step);
        $this->assertCount(3, $step->requirements);
        $this->assertEquals(['KTP', 'Pas Foto 3x4', 'Email Aktif'], $step->requirements);
        $this->assertNotEmpty($step->illustration_image);

        Storage::disk('public')->assertExists($step->illustration_image);
    }

    public function test_editor_can_update_step_and_replace_image(): void
    {
        $oldImage = UploadedFile::fake()->create('old.png', 100, 'image/png');

        $this->actingAs($this->editor)
            ->post(route('cms.registration-steps.store'), [
                'title'              => 'Original Step',
                'description'        => 'Original description.',
                'sort_order'         => 1,
                'status'             => 'draft',
                'illustration_image' => $oldImage,
            ]);

        $step = RegistrationStep::where('title', 'Original Step')->first();
        $oldPath = $step->illustration_image;
        Storage::disk('public')->assertExists($oldPath);

        $newImage = UploadedFile::fake()->create('new.png', 100, 'image/png');

        $this->actingAs($this->editor)
            ->put(route('cms.registration-steps.update', $step), [
                'title'              => 'Updated Step Title',
                'description'        => 'Updated description.',
                'sort_order'         => 2,
                'status'             => 'published',
                'illustration_image' => $newImage,
            ]);

        $step->refresh();
        $this->assertEquals('Updated Step Title', $step->title);
        $this->assertNotEquals($oldPath, $step->illustration_image);

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($step->illustration_image);
    }

    public function test_editor_can_delete_step(): void
    {
        $step = RegistrationStep::create([
            'title'       => 'Step To Delete',
            'description' => 'To be deleted.',
            'sort_order'  => 5,
            'status'      => 'draft',
            'created_by'  => $this->editor->id,
            'updated_by'  => $this->editor->id,
        ]);

        $this->actingAs($this->editor)
            ->delete(route('cms.registration-steps.destroy', $step))
            ->assertRedirect(route('cms.registration-steps.index'));

        $this->assertSoftDeleted('registration_steps', ['id' => $step->id]);
    }

    public function test_operator_is_read_only_on_cms(): void
    {
        $step = RegistrationStep::create([
            'title'       => 'Operator View Step',
            'description' => 'Step description.',
            'sort_order'  => 1,
            'status'      => 'published',
            'created_by'  => $this->editor->id,
            'updated_by'  => $this->editor->id,
        ]);

        // Operator CAN view index and show
        $this->actingAs($this->operator)
            ->get(route('cms.registration-steps.index'))
            ->assertStatus(200);

        $this->actingAs($this->operator)
            ->get(route('cms.registration-steps.show', $step))
            ->assertStatus(200);

        // Operator CANNOT create, edit, or delete
        $this->actingAs($this->operator)
            ->get(route('cms.registration-steps.create'))
            ->assertStatus(403);

        $this->actingAs($this->operator)
            ->get(route('cms.registration-steps.edit', $step))
            ->assertStatus(403);

        $this->actingAs($this->operator)
            ->delete(route('cms.registration-steps.destroy', $step))
            ->assertStatus(403);
    }

    public function test_public_can_view_published_steps_ordered_by_sort_order_then_created_at(): void
    {
        $step2 = RegistrationStep::create([
            'title'       => 'Second Step Title',
            'description' => 'Second step desc.',
            'sort_order'  => 2,
            'status'      => 'published',
            'created_by'  => $this->editor->id,
            'updated_by'  => $this->editor->id,
        ]);

        $step1 = RegistrationStep::create([
            'title'        => 'First Step Title',
            'description'  => 'First step desc.',
            'requirements' => ['KTP', 'Pas Foto'],
            'sort_order'   => 1,
            'status'       => 'published',
            'created_by'   => $this->editor->id,
            'updated_by'   => $this->editor->id,
        ]);

        $response = $this->get(route('public.registration_flow.index'));
        $response->assertStatus(200);
        $response->assertSeeInOrder(['First Step Title', 'Second Step Title']);
        $response->assertSee('KTP');
        $response->assertSee('Pas Foto');

        $detailResponse = $this->get(route('public.registration_flow.show', $step1->slug));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('First step desc.');
    }

    public function test_public_returns_404_for_draft_steps(): void
    {
        $draft = RegistrationStep::create([
            'title'       => 'Draft Step',
            'description' => 'Draft description.',
            'sort_order'  => 1,
            'status'      => 'draft',
            'created_by'  => $this->editor->id,
            'updated_by'  => $this->editor->id,
        ]);

        $this->get(route('public.registration_flow.show', $draft->slug))
            ->assertStatus(404);
    }

    public function test_cache_invalidation_on_step_change(): void
    {
        $initialVersion = RegistrationStep::cacheVersion();

        RegistrationStep::create([
            'title'       => 'Cache Bust Step',
            'description' => 'Bust cache.',
            'sort_order'  => 1,
            'status'      => 'published',
            'created_by'  => $this->editor->id,
            'updated_by'  => $this->editor->id,
        ]);

        $newVersion = RegistrationStep::cacheVersion();
        $this->assertGreaterThan($initialVersion, $newVersion);
    }
}
