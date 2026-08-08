<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\FileCategory;
use App\Models\FileItem;
use App\Models\InformationCategory;
use App\Models\InformationPost;
use App\Models\RegistrationStep;
use App\Models\Timeline;
use App\Models\User;
use App\Services\DashboardActivityService;
use App\Services\DashboardStatsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
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

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->editor = User::factory()->create();
        $this->editor->assignRole('editor');

        $this->operator = User::factory()->create();
        $this->operator->assignRole('operator');
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_all_cms_roles_can_access_operational_dashboard(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('admin.dashboard'))
            ->assertStatus(200)
            ->assertSee('Dashboard Operasional');

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertStatus(200);

        $this->actingAs($this->editor)
            ->get(route('admin.dashboard'))
            ->assertStatus(200);

        $this->actingAs($this->operator)
            ->get(route('admin.dashboard'))
            ->assertStatus(200);
    }

    public function test_dashboard_stats_service_returns_accurate_metrics(): void
    {
        Cache::flush();

        $infoCat = InformationCategory::create([
            'name'       => 'Berita',
            'slug'       => 'berita',
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        InformationPost::create([
            'category_id'  => $infoCat->id,
            'title'        => 'Information Published',
            'slug'         => 'information-published',
            'content'      => 'Content.',
            'status'       => 'published',
            'views_count'  => 50,
            'created_by'   => $this->admin->id,
            'updated_by'   => $this->admin->id,
        ]);

        InformationPost::create([
            'category_id'  => $infoCat->id,
            'title'        => 'Information Draft',
            'slug'         => 'information-draft',
            'content'      => 'Content.',
            'status'       => 'draft',
            'created_by'   => $this->admin->id,
            'updated_by'   => $this->admin->id,
        ]);

        Announcement::create([
            'title'           => 'Announcement Test',
            'slug'            => 'announcement-test',
            'content'         => 'Content.',
            'downloads_count' => 15,
            'status'          => 'published',
            'created_by'      => $this->admin->id,
            'updated_by'      => $this->admin->id,
        ]);

        $fileCat = FileCategory::create([
            'name'       => 'Dokumen',
            'slug'       => 'dokumen',
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        FileItem::create([
            'category_id'     => $fileCat->id,
            'title'           => 'Document Test',
            'slug'            => 'document-test',
            'file_path'       => 'files/doc.pdf',
            'original_name'   => 'doc.pdf',
            'mime_type'       => 'application/pdf',
            'file_size'       => 1024,
            'downloads_count' => 25,
            'status'          => 'published',
            'created_by'      => $this->admin->id,
            'updated_by'      => $this->admin->id,
        ]);

        Timeline::create([
            'title'       => 'Timeline Item',
            'description' => 'Timeline description.',
            'start_date'  => now()->toDateString(),
            'status'      => 'published',
            'created_by'  => $this->admin->id,
            'updated_by'  => $this->admin->id,
        ]);

        RegistrationStep::create([
            'title'       => 'Step Item',
            'description' => 'Description.',
            'sort_order'  => 1,
            'status'      => 'draft',
            'created_by'  => $this->admin->id,
            'updated_by'  => $this->admin->id,
        ]);

        $statsService = app(DashboardStatsService::class);

        $stats = $statsService->getStats();
        $this->assertEquals(4, $stats['total_users']);
        $this->assertEquals(2, $stats['total_information_posts']);
        $this->assertEquals(1, $stats['total_announcements']);
        $this->assertEquals(1, $stats['total_timelines']);
        $this->assertEquals(1, $stats['total_files']);
        $this->assertEquals(1, $stats['total_registration_steps']);
        $this->assertEquals(25, $stats['total_file_downloads']);
        $this->assertEquals(15, $stats['total_announcement_downloads']);

        $drafts = $statsService->getDraftCounts();
        $this->assertEquals(1, $drafts['draft_information']);
        $this->assertEquals(0, $drafts['draft_announcements']);
        $this->assertEquals(1, $drafts['draft_registration_steps']);

        $health = $statsService->getSystemHealth();
        $this->assertEquals('Connected', $health['db_status']);
        $this->assertNotEmpty($health['laravel_version']);
        $this->assertNotEmpty($health['php_version']);
    }

    public function test_dashboard_activity_service_fetches_latest_activities(): void
    {
        Cache::flush();

        $activityService = app(DashboardActivityService::class);
        $activities = $activityService->getLatestActivities();

        $this->assertArrayHasKey('latest_information', $activities);
        $this->assertArrayHasKey('latest_announcements', $activities);
        $this->assertArrayHasKey('latest_timelines', $activities);
        $this->assertArrayHasKey('latest_files', $activities);
        $this->assertArrayHasKey('latest_registration_steps', $activities);
    }
}
