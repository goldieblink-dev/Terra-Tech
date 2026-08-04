<?php

namespace Tests\Feature;

use App\Models\Timeline;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TimelineTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $editor;
    protected User $operator;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'editor']);
        Role::create(['name' => 'operator']);

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super_admin');

        $this->editor = User::factory()->create();
        $this->editor->assignRole('editor');

        $this->operator = User::factory()->create();
        $this->operator->assignRole('operator');
    }

    public function test_public_can_view_published_timelines_and_details(): void
    {
        $timeline = Timeline::create([
            'title' => 'Launch Phase 1',
            'description' => 'Official launch of Terra Tech.',
            'start_date' => now()->subDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
            'location' => 'Headquarters',
            'color' => '#2563eb',
            'status' => 'published',
            'sort_order' => 1,
            'created_by' => $this->editor->id,
            'updated_by' => $this->editor->id,
        ]);

        $response = $this->get(route('public.timelines.index'));
        $response->assertStatus(200);
        $response->assertSee('Launch Phase 1');
        $response->assertSee('Sedang Berjalan');

        $detailResponse = $this->get(route('public.timelines.show', $timeline));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Official launch of Terra Tech.');
    }

    public function test_public_detail_returns_404_for_draft_or_non_existent(): void
    {
        $draftTimeline = Timeline::create([
            'title' => 'Internal Draft Agenda',
            'description' => 'Not ready for public view.',
            'start_date' => now()->format('Y-m-d'),
            'status' => 'draft',
            'created_by' => $this->editor->id,
            'updated_by' => $this->editor->id,
        ]);

        // Draft returns 404
        $this->get(route('public.timelines.show', $draftTimeline))
            ->assertStatus(404);

        // Non-existent ID returns 404
        $this->get('/timeline/999999')
            ->assertStatus(404);
    }

    public function test_timeline_status_accessor_date_only_logic(): void
    {
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');
        $tomorrow = now()->addDay()->format('Y-m-d');
        $past = now()->subDays(5)->format('Y-m-d');
        $future = now()->addDays(5)->format('Y-m-d');

        // Ongoing case 1: start_date is today, end_date is today
        $t1 = new Timeline(['start_date' => $today, 'end_date' => $today]);
        $this->assertEquals('ongoing', $t1->timeline_status);

        // Ongoing case 2: start_date is yesterday, end_date is today
        $t2 = new Timeline(['start_date' => $yesterday, 'end_date' => $today]);
        $this->assertEquals('ongoing', $t2->timeline_status);

        // Ongoing case 3: start_date is today, end_date is tomorrow
        $t3 = new Timeline(['start_date' => $today, 'end_date' => $tomorrow]);
        $this->assertEquals('ongoing', $t3->timeline_status);

        // Upcoming case: start_date is tomorrow
        $t4 = new Timeline(['start_date' => $tomorrow, 'end_date' => $future]);
        $this->assertEquals('upcoming', $t4->timeline_status);

        // Completed case: end_date is yesterday
        $t5 = new Timeline(['start_date' => $past, 'end_date' => $yesterday]);
        $this->assertEquals('completed', $t5->timeline_status);
    }

    public function test_operator_is_read_only_on_cms(): void
    {
        $timeline = Timeline::create([
            'title' => 'Roadmap 2026',
            'description' => 'Future plans.',
            'start_date' => now()->addDays(10)->format('Y-m-d'),
            'status' => 'published',
            'sort_order' => 0,
            'created_by' => $this->editor->id,
            'updated_by' => $this->editor->id,
        ]);

        // Operator can view index and show
        $this->actingAs($this->operator)
            ->get(route('cms.timelines.index'))
            ->assertStatus(200);

        $this->actingAs($this->operator)
            ->get(route('cms.timelines.show', $timeline))
            ->assertStatus(200);

        // Operator CANNOT create
        $this->actingAs($this->operator)
            ->get(route('cms.timelines.create'))
            ->assertStatus(403);

        $this->actingAs($this->operator)
            ->post(route('cms.timelines.store'), [
                'title' => 'Forbidden Timeline',
                'description' => 'Test forbidden',
                'start_date' => '2026-09-01',
                'status' => 'published',
            ])->assertStatus(403);

        // Operator CANNOT edit or delete
        $this->actingAs($this->operator)
            ->get(route('cms.timelines.edit', $timeline))
            ->assertStatus(403);

        $this->actingAs($this->operator)
            ->delete(route('cms.timelines.destroy', $timeline))
            ->assertStatus(403);
    }

    public function test_editor_can_create_update_and_delete_timeline(): void
    {
        $response = $this->actingAs($this->editor)
            ->post(route('cms.timelines.store'), [
                'title' => 'New Expansion',
                'description' => 'Expanding to new region.',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(30)->format('Y-m-d'),
                'location' => 'Bali Office',
                'color' => '#059669',
                'status' => 'published',
                'sort_order' => 5,
            ]);

        $response->assertRedirect(route('cms.timelines.index'));
        $this->assertDatabaseHas('timelines', ['title' => 'New Expansion']);

        $timeline = Timeline::where('title', 'New Expansion')->first();

        // Update
        $this->actingAs($this->editor)
            ->put(route('cms.timelines.update', $timeline), [
                'title' => 'New Expansion Updated',
                'description' => 'Updated expansion plans.',
                'start_date' => now()->format('Y-m-d'),
                'status' => 'published',
                'sort_order' => 1,
            ])->assertRedirect(route('cms.timelines.index'));

        $this->assertDatabaseHas('timelines', ['title' => 'New Expansion Updated']);

        // Delete
        $this->actingAs($this->editor)
            ->delete(route('cms.timelines.destroy', $timeline))
            ->assertRedirect(route('cms.timelines.index'));

        $this->assertSoftDeleted('timelines', ['id' => $timeline->id]);
    }

    public function test_timeline_cache_invalidation(): void
    {
        $initialVersion = Timeline::cacheVersion();

        $timeline = Timeline::create([
            'title' => 'Cache Test Event',
            'description' => 'Testing versioned cache',
            'start_date' => '2026-10-01',
            'status' => 'published',
            'created_by' => $this->editor->id,
            'updated_by' => $this->editor->id,
        ]);

        $newVersion = Timeline::cacheVersion();
        $this->assertGreaterThan($initialVersion, $newVersion);
    }
}
