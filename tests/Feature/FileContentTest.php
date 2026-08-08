<?php

namespace Tests\Feature;

use App\Models\FileCategory;
use App\Models\FileItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FileContentTest extends TestCase
{
    use RefreshDatabase;

    protected User $editor;
    protected User $operator;
    protected FileCategory $category;

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

        $this->category = FileCategory::create([
            'name'       => 'Modul Teknis',
            'created_by' => $this->editor->id,
            'updated_by' => $this->editor->id,
        ]);
    }

    public function test_editor_can_create_file_with_upload(): void
    {
        $pdf = UploadedFile::fake()->create('manual.pdf', 512, 'application/pdf');

        $response = $this->actingAs($this->editor)
            ->post(route('cms.files.store'), [
                'title'       => 'Panduan Operasional',
                'category_id' => $this->category->id,
                'description' => 'Panduan penggunaan sistem v2.',
                'status'      => 'published',
                'file'        => $pdf,
            ]);

        $response->assertRedirect(route('cms.files.index'));
        $this->assertDatabaseHas('files', ['title' => 'Panduan Operasional']);

        $fileItem = FileItem::where('title', 'Panduan Operasional')->first();
        $this->assertNotNull($fileItem);
        $this->assertEquals('published', $fileItem->status);
        $this->assertNotNull($fileItem->published_at);
        $this->assertNotEmpty($fileItem->file_path);
        $this->assertNotEmpty($fileItem->mime_type);
        $this->assertGreaterThan(0, $fileItem->file_size);

        Storage::disk('public')->assertExists($fileItem->file_path);
    }

    public function test_editor_can_update_file_with_replacement(): void
    {
        $original = UploadedFile::fake()->create('old.pdf', 256, 'application/pdf');

        $this->actingAs($this->editor)
            ->post(route('cms.files.store'), [
                'title'       => 'To Replace',
                'category_id' => $this->category->id,
                'status'      => 'draft',
                'file'        => $original,
            ]);

        $fileItem = FileItem::where('title', 'To Replace')->first();
        $oldPath = $fileItem->file_path;

        Storage::disk('public')->assertExists($oldPath);

        $replacement = UploadedFile::fake()->create('new.pdf', 128, 'application/pdf');

        $this->actingAs($this->editor)
            ->put(route('cms.files.update', $fileItem), [
                'title'       => 'Replaced File',
                'category_id' => $this->category->id,
                'status'      => 'published',
                'file'        => $replacement,
            ]);

        $fileItem->refresh();
        $this->assertEquals('Replaced File', $fileItem->title);
        $this->assertNotEquals($oldPath, $fileItem->file_path);

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($fileItem->file_path);
    }

    public function test_editor_can_delete_file(): void
    {
        $pdf = UploadedFile::fake()->create('delete-me.pdf', 100, 'application/pdf');

        $this->actingAs($this->editor)
            ->post(route('cms.files.store'), [
                'title'       => 'Delete Me',
                'category_id' => $this->category->id,
                'status'      => 'draft',
                'file'        => $pdf,
            ]);

        $fileItem = FileItem::where('title', 'Delete Me')->first();

        $this->actingAs($this->editor)
            ->delete(route('cms.files.destroy', $fileItem))
            ->assertRedirect(route('cms.files.index'));

        $this->assertSoftDeleted('files', ['id' => $fileItem->id]);
    }

    public function test_operator_is_read_only(): void
    {
        $fileItem = FileItem::create([
            'category_id'   => $this->category->id,
            'title'         => 'Read Only File',
            'description'   => 'Description.',
            'file_path'     => 'files/test.pdf',
            'original_name' => 'test.pdf',
            'mime_type'     => 'application/pdf',
            'file_size'     => 1024,
            'status'        => 'published',
            'published_at'  => now(),
            'created_by'    => $this->editor->id,
            'updated_by'    => $this->editor->id,
        ]);

        // Store a dummy file for download
        Storage::disk('public')->put('files/test.pdf', 'dummy');

        // Operator CAN view index and show
        $this->actingAs($this->operator)
            ->get(route('cms.files.index'))
            ->assertStatus(200);

        $this->actingAs($this->operator)
            ->get(route('cms.files.show', $fileItem))
            ->assertStatus(200);

        // Operator CAN download
        $this->actingAs($this->operator)
            ->get(route('cms.files.download', $fileItem))
            ->assertStatus(200);

        // Operator CANNOT create
        $this->actingAs($this->operator)
            ->get(route('cms.files.create'))
            ->assertStatus(403);

        // Operator CANNOT edit or delete
        $this->actingAs($this->operator)
            ->get(route('cms.files.edit', $fileItem))
            ->assertStatus(403);

        $this->actingAs($this->operator)
            ->delete(route('cms.files.destroy', $fileItem))
            ->assertStatus(403);
    }

    public function test_public_can_view_published_files(): void
    {
        $fileItem = FileItem::create([
            'category_id'   => $this->category->id,
            'title'         => 'Public Manual',
            'description'   => 'Accessible manual.',
            'file_path'     => 'files/public-manual.pdf',
            'original_name' => 'public-manual.pdf',
            'mime_type'     => 'application/pdf',
            'file_size'     => 2048,
            'status'        => 'published',
            'published_at'  => now(),
            'created_by'    => $this->editor->id,
            'updated_by'    => $this->editor->id,
        ]);

        $response = $this->get(route('public.files.index'));
        $response->assertStatus(200);
        $response->assertSee('Public Manual');

        $detailResponse = $this->get(route('public.files.show', $fileItem->slug));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Accessible manual.');
    }

    public function test_public_returns_404_for_draft_files(): void
    {
        $fileItem = FileItem::create([
            'category_id'   => $this->category->id,
            'title'         => 'Draft Only',
            'file_path'     => 'files/draft.pdf',
            'original_name' => 'draft.pdf',
            'mime_type'     => 'application/pdf',
            'file_size'     => 512,
            'status'        => 'draft',
            'created_by'    => $this->editor->id,
            'updated_by'    => $this->editor->id,
        ]);

        $this->get(route('public.files.show', $fileItem->slug))
            ->assertStatus(404);

        $this->get(route('public.files.download', $fileItem->slug))
            ->assertStatus(404);
    }

    public function test_public_download_increments_counter(): void
    {
        $fileItem = FileItem::create([
            'category_id'   => $this->category->id,
            'title'         => 'Counter Test',
            'file_path'     => 'files/counter-test.pdf',
            'original_name' => 'counter-test.pdf',
            'mime_type'     => 'application/pdf',
            'file_size'     => 128,
            'status'        => 'published',
            'published_at'  => now(),
            'created_by'    => $this->editor->id,
            'updated_by'    => $this->editor->id,
        ]);

        Storage::disk('public')->put('files/counter-test.pdf', 'fake pdf content');

        $this->assertEquals(0, $fileItem->downloads_count);

        $this->get(route('public.files.download', $fileItem->slug))
            ->assertStatus(200);

        $fileItem->refresh();
        $this->assertEquals(1, $fileItem->downloads_count);
    }

    public function test_cache_invalidation_on_file_create(): void
    {
        $initialVersion = FileItem::cacheVersion();

        FileItem::create([
            'category_id'   => $this->category->id,
            'title'         => 'Cache Bust File',
            'file_path'     => 'files/cache.pdf',
            'original_name' => 'cache.pdf',
            'mime_type'     => 'application/pdf',
            'file_size'     => 64,
            'status'        => 'published',
            'published_at'  => now(),
            'created_by'    => $this->editor->id,
            'updated_by'    => $this->editor->id,
        ]);

        $newVersion = FileItem::cacheVersion();
        $this->assertGreaterThan($initialVersion, $newVersion);
    }

    public function test_file_category_crud(): void
    {
        // Editor can create category
        $this->actingAs($this->editor)
            ->post(route('cms.file-categories.store'), [
                'name'        => 'Laporan Tahunan',
                'description' => 'Annual reports category.',
            ])
            ->assertRedirect(route('cms.file-categories.index'));

        $this->assertDatabaseHas('file_categories', ['name' => 'Laporan Tahunan']);

        $cat = FileCategory::where('name', 'Laporan Tahunan')->first();

        // Editor can update category
        $this->actingAs($this->editor)
            ->put(route('cms.file-categories.update', $cat), [
                'name'        => 'Laporan Tahunan Updated',
                'description' => 'Updated description.',
            ])
            ->assertRedirect(route('cms.file-categories.index'));

        $this->assertDatabaseHas('file_categories', ['name' => 'Laporan Tahunan Updated']);

        // Editor can delete category
        $this->actingAs($this->editor)
            ->delete(route('cms.file-categories.destroy', $cat))
            ->assertRedirect(route('cms.file-categories.index'));

        $this->assertSoftDeleted('file_categories', ['id' => $cat->id]);

        // Operator CANNOT create category
        $this->actingAs($this->operator)
            ->get(route('cms.file-categories.create'))
            ->assertStatus(403);
    }
}
