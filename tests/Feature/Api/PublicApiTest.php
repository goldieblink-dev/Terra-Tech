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
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        Role::create(['name' => 'admin']);
        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
    }

    public function test_information_public_api_listing_and_filtering(): void
    {
        $cat = InformationCategory::create([
            'name'       => 'Teknologi',
            'slug'       => 'teknologi',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        InformationPost::create([
            'category_id'  => $cat->id,
            'title'        => 'Inovasi AI Terra',
            'slug'         => 'inovasi-ai-terra',
            'content'      => 'Konten teknologi AI.',
            'status'       => 'published',
            'published_at' => now(),
            'created_by'   => $this->user->id,
            'updated_by'   => $this->user->id,
        ]);

        InformationPost::create([
            'category_id'  => $cat->id,
            'title'        => 'Draft Info',
            'slug'         => 'draft-info',
            'content'      => 'Konten draft.',
            'status'       => 'draft',
            'created_by'   => $this->user->id,
            'updated_by'   => $this->user->id,
        ]);

        $response = $this->getJson('/api/v1/information?category=teknologi&search=Inovasi');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Daftar informasi publik berhasil diambil.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'items' => [
                        '*' => ['id', 'title', 'slug', 'status', 'category'],
                    ],
                    'pagination' => ['current_page', 'last_page', 'per_page', 'total'],
                ],
            ]);

        $this->assertCount(1, $response->json('data.items'));
        $this->assertEquals('inovasi-ai-terra', $response->json('data.items.0.slug'));

        // Detail endpoint
        $detailResponse = $this->getJson('/api/v1/information/inovasi-ai-terra');
        $detailResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        // Draft returns 404
        $this->getJson('/api/v1/information/draft-info')
            ->assertStatus(404);
    }

    public function test_announcement_public_api_listing_and_priority_filter(): void
    {
        Announcement::create([
            'title'        => 'Pengumuman Penting',
            'slug'         => 'pengumuman-penting',
            'content'      => 'Konten pengumuman urgent.',
            'priority'     => 'urgent',
            'status'       => 'published',
            'published_at' => now(),
            'created_by'   => $this->user->id,
            'updated_by'   => $this->user->id,
        ]);

        Announcement::create([
            'title'        => 'Pengumuman Draft',
            'slug'         => 'pengumuman-draft',
            'content'      => 'Konten draft.',
            'priority'     => 'normal',
            'status'       => 'draft',
            'created_by'   => $this->user->id,
            'updated_by'   => $this->user->id,
        ]);

        $response = $this->getJson('/api/v1/announcements?priority=urgent');
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertCount(1, $response->json('data.items'));

        // Detail endpoint
        $this->getJson('/api/v1/announcements/pengumuman-penting')
            ->assertStatus(200);

        // Draft returns 404
        $this->getJson('/api/v1/announcements/pengumuman-draft')
            ->assertStatus(404);
    }

    public function test_timeline_public_api_listing_and_show(): void
    {
        $timeline = Timeline::create([
            'title'       => 'Agenda Utama',
            'description' => 'Deskripsi agenda.',
            'start_date'  => now()->toDateString(),
            'status'      => 'published',
            'created_by'  => $this->user->id,
            'updated_by'  => $this->user->id,
        ]);

        $draft = Timeline::create([
            'title'       => 'Agenda Draft',
            'description' => 'Deskripsi draft.',
            'start_date'  => now()->toDateString(),
            'status'      => 'draft',
            'created_by'  => $this->user->id,
            'updated_by'  => $this->user->id,
        ]);

        $response = $this->getJson('/api/v1/timelines');
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertCount(1, $response->json('data.items'));

        // Show published by ID
        $this->getJson("/api/v1/timelines/{$timeline->id}")
            ->assertStatus(200);

        // Draft by ID returns 404
        $this->getJson("/api/v1/timelines/{$draft->id}")
            ->assertStatus(404);
    }

    public function test_files_public_api_listing_and_show(): void
    {
        $cat = FileCategory::create([
            'name'       => 'Modul Teknis',
            'slug'       => 'modul-teknis',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $file = FileItem::create([
            'category_id'     => $cat->id,
            'title'           => 'Panduan Sistem PDF',
            'slug'            => 'panduan-sistem-pdf',
            'file_path'       => 'files/panduan.pdf',
            'original_name'   => 'panduan.pdf',
            'mime_type'       => 'application/pdf',
            'file_size'       => 1024,
            'status'          => 'published',
            'published_at'    => now(),
            'created_by'      => $this->user->id,
            'updated_by'      => $this->user->id,
        ]);

        $response = $this->getJson('/api/v1/files?category=modul-teknis&search=Panduan');
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertCount(1, $response->json('data.items'));

        // Detail endpoint
        $this->getJson('/api/v1/files/panduan-sistem-pdf')
            ->assertStatus(200);
    }

    public function test_registration_flow_public_api_listing_and_show(): void
    {
        $step = RegistrationStep::create([
            'title'        => 'Langkah 1: Akun',
            'slug'         => 'langkah-1-akun',
            'description'  => 'Buat akun baru.',
            'requirements' => ['KTP', 'Email'],
            'sort_order'   => 1,
            'status'       => 'published',
            'created_by'   => $this->user->id,
            'updated_by'   => $this->user->id,
        ]);

        $response = $this->getJson('/api/v1/registration-flow');
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertCount(1, $response->json('data.items'));
        $this->assertEquals(['KTP', 'Email'], $response->json('data.items.0.requirements'));

        // Detail endpoint
        $this->getJson('/api/v1/registration-flow/langkah-1-akun')
            ->assertStatus(200);
    }

    public function test_cache_invalidation_affects_public_api(): void
    {
        $cat = InformationCategory::create([
            'name'       => 'General',
            'slug'       => 'general',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        // First call caches 0 posts
        $res1 = $this->getJson('/api/v1/information');
        $this->assertCount(0, $res1->json('data.items'));

        // Create new published post (which increments cache version via model event)
        InformationPost::create([
            'category_id'  => $cat->id,
            'title'        => 'New Cache Post',
            'slug'         => 'new-cache-post',
            'content'      => 'Content.',
            'status'       => 'published',
            'published_at' => now(),
            'created_by'   => $this->user->id,
            'updated_by'   => $this->user->id,
        ]);

        // Second call fetches updated cache version with 1 post
        $res2 = $this->getJson('/api/v1/information');
        $this->assertCount(1, $res2->json('data.items'));
    }
}
