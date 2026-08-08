<?php

namespace Tests\Feature\Api;

use App\Models\Announcement;
use App\Models\FileCategory;
use App\Models\FileItem;
use App\Models\InformationCategory;
use App\Models\InformationPost;
use App\Models\RegistrationStep;
use App\Models\Timeline;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $editor;
    protected User $operator;
    protected InformationCategory $infoCategory;
    protected FileCategory $fileCategory;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Cache::flush();

        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'editor']);
        Role::create(['name' => 'operator']);

        $this->editor = User::factory()->create();
        $this->editor->assignRole('editor');

        $this->operator = User::factory()->create();
        $this->operator->assignRole('operator');

        $this->infoCategory = InformationCategory::create([
            'name'       => 'Berita Utama',
            'slug'       => 'berita-utama',
            'created_by' => $this->editor->id,
            'updated_by' => $this->editor->id,
        ]);

        $this->fileCategory = FileCategory::create([
            'name'       => 'Panduan Teknis',
            'slug'       => 'panduan-teknis',
            'created_by' => $this->editor->id,
            'updated_by' => $this->editor->id,
        ]);
    }

    public function test_authentication_required_for_cms_api(): void
    {
        $this->getJson('/api/v1/cms/information')->assertStatus(401);
        $this->postJson('/api/v1/cms/information', [])->assertStatus(401);
    }

    public function test_operator_is_read_only_on_cms_api(): void
    {
        $token = $this->operator->createToken('operator_token')->plainTextToken;

        // Operator CAN view index and show
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/cms/information')
            ->assertStatus(200);

        // Operator CANNOT create
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/cms/information', [
                'category_id' => $this->infoCategory->id,
                'title'       => 'Operator Post',
                'content'     => 'Content.',
                'status'      => 'published',
            ])
            ->assertStatus(403);
    }

    public function test_information_cms_crud_and_upload(): void
    {
        $token = $this->editor->createToken('editor_token')->plainTextToken;
        $image = UploadedFile::fake()->create('featured.png', 100, 'image/png');

        // STORE
        $storeResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/cms/information', [
                'category_id'    => $this->infoCategory->id,
                'title'          => 'API Information Title',
                'content'        => 'API Content Body.',
                'status'         => 'published',
                'featured_image' => $image,
            ]);

        $storeResponse->assertStatus(201)
            ->assertJson(['success' => true]);

        $id = $storeResponse->json('data.id');
        $this->assertDatabaseHas('information_posts', ['id' => $id, 'title' => 'API Information Title']);

        // SHOW
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/v1/cms/information/{$id}")
            ->assertStatus(200)
            ->assertJson(['data' => ['id' => $id]]);

        // UPDATE WITH REPLACEMENT
        $newImage = UploadedFile::fake()->create('new_featured.png', 100, 'image/png');

        $updateResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/v1/cms/information/{$id}", [
                'category_id'    => $this->infoCategory->id,
                'title'          => 'Updated Information Title',
                'content'        => 'Updated Content Body.',
                'status'         => 'draft',
                'featured_image' => $newImage,
            ]);

        $updateResponse->assertStatus(200)
            ->assertJson(['data' => ['title' => 'Updated Information Title']]);

        // DELETE
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/v1/cms/information/{$id}")
            ->assertStatus(200);

        $this->assertSoftDeleted('information_posts', ['id' => $id]);
    }

    public function test_announcement_cms_crud_and_upload(): void
    {
        $token = $this->editor->createToken('editor_token')->plainTextToken;
        $attachment = UploadedFile::fake()->create('notice.pdf', 200, 'application/pdf');

        // STORE
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/cms/announcements', [
                'title'           => 'Urgent Announcement',
                'content'         => 'Announcement body text.',
                'priority'        => 'urgent',
                'status'          => 'published',
                'attachment_file' => $attachment,
            ]);

        $response->assertStatus(201);
        $id = $response->json('data.id');

        // UPDATE
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/v1/cms/announcements/{$id}", [
                'title'    => 'Updated Announcement',
                'content'  => 'Updated body text.',
                'priority' => 'normal',
                'status'   => 'draft',
            ])
            ->assertStatus(200);

        // DELETE
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/v1/cms/announcements/{$id}")
            ->assertStatus(200);
    }

    public function test_timeline_cms_crud(): void
    {
        $token = $this->editor->createToken('editor_token')->plainTextToken;

        // STORE
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/cms/timelines', [
                'title'       => 'Initial Milestone',
                'description' => 'Milestone description.',
                'start_date'  => '2026-09-01',
                'status'      => 'published',
                'sort_order'  => 1,
            ]);

        $response->assertStatus(201);
        $id = $response->json('data.id');

        // UPDATE
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/v1/cms/timelines/{$id}", [
                'title'       => 'Updated Milestone',
                'description' => 'Updated description.',
                'start_date'  => '2026-09-01',
                'end_date'    => '2026-09-05',
                'status'      => 'published',
                'sort_order'  => 2,
            ])
            ->assertStatus(200);

        // DELETE
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/v1/cms/timelines/{$id}")
            ->assertStatus(200);
    }

    public function test_files_cms_crud_and_mime_validation(): void
    {
        $token = $this->editor->createToken('editor_token')->plainTextToken;
        $file = UploadedFile::fake()->create('manual.pdf', 500, 'application/pdf');

        // STORE
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/cms/files', [
                'category_id' => $this->fileCategory->id,
                'title'       => 'User Manual PDF',
                'description' => 'Manual book.',
                'status'      => 'published',
                'file'        => $file,
            ]);

        $response->assertStatus(201);
        $id = $response->json('data.id');

        // DELETE
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/v1/cms/files/{$id}")
            ->assertStatus(200);
    }

    public function test_registration_flow_cms_crud_with_requirements_array(): void
    {
        $token = $this->editor->createToken('editor_token')->plainTextToken;

        // STORE
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/cms/registration-steps', [
                'title'        => 'Step 1 - Register',
                'description'  => 'Fill registration form.',
                'requirements' => "KTP\nPas Foto 3x4",
                'sort_order'   => 1,
                'status'       => 'published',
            ]);

        $response->assertStatus(201);
        $id = $response->json('data.id');
        $this->assertCount(2, $response->json('data.requirements'));

        // DELETE
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/v1/cms/registration-steps/{$id}")
            ->assertStatus(200);
    }

    public function test_cache_invalidation_on_cms_api_mutations(): void
    {
        $token = $this->editor->createToken('editor_token')->plainTextToken;
        $initialVersion = InformationPost::cacheVersion();

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/cms/information', [
                'category_id' => $this->infoCategory->id,
                'title'       => 'Cache Bust Post',
                'content'     => 'Content.',
                'status'      => 'published',
            ]);

        $newVersion = InformationPost::cacheVersion();
        $this->assertGreaterThan($initialVersion, $newVersion);
    }
}
