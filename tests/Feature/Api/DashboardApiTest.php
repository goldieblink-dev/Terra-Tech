<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $editor;
    protected User $operator;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

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

    public function test_authentication_required_for_dashboard_api(): void
    {
        $this->getJson('/api/v1/cms/dashboard')->assertStatus(401);
        $this->getJson('/api/v1/cms/dashboard/stats')->assertStatus(401);
        $this->getJson('/api/v1/cms/dashboard/drafts')->assertStatus(401);
        $this->getJson('/api/v1/cms/dashboard/activity')->assertStatus(401);
        $this->getJson('/api/v1/cms/dashboard/analytics')->assertStatus(401);
        $this->getJson('/api/v1/cms/dashboard/system-health')->assertStatus(401);
    }

    public function test_all_cms_roles_can_access_dashboard_api(): void
    {
        foreach ([$this->superAdmin, $this->admin, $this->editor, $this->operator] as $user) {
            $token = $user->createToken('token')->plainTextToken;

            $this->withHeader('Authorization', 'Bearer ' . $token)
                ->getJson('/api/v1/cms/dashboard')
                ->assertStatus(200)
                ->assertJson(['success' => true]);
        }
    }

    public function test_dashboard_stats_endpoint(): void
    {
        $token = $this->admin->createToken('token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/cms/dashboard/stats');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Statistik CMS berhasil diambil.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'total_users',
                    'total_information',
                    'total_announcements',
                    'total_timelines',
                    'total_files',
                    'total_registration_steps',
                    'total_file_downloads',
                    'total_announcement_downloads',
                ],
            ]);
    }

    public function test_dashboard_drafts_endpoint(): void
    {
        $token = $this->admin->createToken('token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/cms/dashboard/drafts');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data draft CMS berhasil diambil.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'information',
                    'announcements',
                    'timelines',
                    'files',
                    'registration_steps',
                ],
            ]);
    }

    public function test_dashboard_activity_endpoint_with_limit(): void
    {
        $token = $this->admin->createToken('token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/cms/dashboard/activity?limit=10');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Aktivitas terbaru CMS berhasil diambil.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'latest_information',
                    'latest_announcements',
                    'latest_timelines',
                    'latest_files',
                    'latest_registration_steps',
                ],
            ]);
    }

    public function test_dashboard_analytics_endpoint(): void
    {
        $token = $this->admin->createToken('token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/cms/dashboard/analytics');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Analitik CMS berhasil diambil.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'top_downloaded_files',
                    'top_viewed_posts',
                ],
            ]);
    }

    public function test_dashboard_system_health_endpoint(): void
    {
        $token = $this->admin->createToken('token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/cms/dashboard/system-health');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Status kesehatan sistem berhasil diambil.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'storage_used',
                    'storage_readable',
                    'cache_driver',
                    'db_status',
                    'laravel_version',
                    'php_version',
                ],
            ]);
    }

    public function test_aggregated_dashboard_endpoint(): void
    {
        $token = $this->admin->createToken('token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/cms/dashboard');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Dashboard CMS berhasil diambil.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'stats',
                    'drafts',
                    'analytics',
                    'activity',
                    'system_health',
                ],
            ]);
    }
}
