<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\StoreAnnouncementRequest;
use App\Http\Requests\Editor\UpdateAnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements in CMS.
     */
    public function index(Request $request): View
    {
        $this->authorizeAccess();

        $query = Announcement::with(['creator'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $announcements = $query->paginate(10)->withQueryString();

        return view('editor.announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create(): View
    {
        $this->authorizeManage();

        return view('editor.announcements.create');
    }

    /**
     * Store a newly created announcement.
     */
    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        $this->authorizeManage();

        DB::transaction(function () use ($request) {
            $attachmentFile = null;
            $attachmentName = null;

            if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
                $file = $request->file('attachment');
                $attachmentName = $file->getClientOriginalName();
                $filename = 'announcement_' . Str::uuid() . '.' . strtolower($file->getClientOriginalExtension());
                $attachmentFile = $file->storeAs('announcements', $filename, 'public');
            }

            $publishedAt = null;
            if ($request->status === 'published') {
                $publishedAt = now();
            }

            Announcement::create([
                'title'           => $request->title,
                'slug'            => Announcement::generateUniqueSlug($request->title),
                'content'         => $request->content,
                'attachment_file' => $attachmentFile,
                'attachment_name' => $attachmentName,
                'priority'        => $request->priority,
                'status'          => $request->status,
                'published_at'    => $publishedAt,
                'created_by'      => Auth::id(),
                'updated_by'      => Auth::id(),
            ]);
        });

        Log::info('announcement_created', [
            'title'      => $request->title,
            'priority'   => $request->priority,
            'status'     => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('cms.announcements.index')
            ->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    /**
     * Show a single announcement detail in CMS.
     */
    public function show(Announcement $announcement): View
    {
        $this->authorizeAccess();

        $announcement->load('creator', 'updater');

        return view('editor.announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing an existing announcement.
     */
    public function edit(Announcement $announcement): View
    {
        $this->authorizeManage();

        return view('editor.announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified announcement.
     */
    public function update(UpdateAnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        $this->authorizeManage();

        DB::transaction(function () use ($request, $announcement) {
            $attachmentFile = $announcement->attachment_file;
            $attachmentName = $announcement->attachment_name;

            if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
                // Delete previous file if exists
                if ($attachmentFile && Storage::disk('public')->exists($attachmentFile)) {
                    Storage::disk('public')->delete($attachmentFile);
                }

                $file = $request->file('attachment');
                $attachmentName = $file->getClientOriginalName();
                $filename = 'announcement_' . Str::uuid() . '.' . strtolower($file->getClientOriginalExtension());
                $attachmentFile = $file->storeAs('announcements', $filename, 'public');
            }

            // Manage published_at timestamp transitions
            $publishedAt = $announcement->published_at;
            if ($request->status === 'published' && $announcement->status === 'draft') {
                $publishedAt = now();
            } elseif ($request->status === 'draft') {
                $publishedAt = null;
            }

            $announcement->update([
                'title'           => $request->title,
                'slug'            => Announcement::generateUniqueSlug($request->title, $announcement->id),
                'content'         => $request->content,
                'attachment_file' => $attachmentFile,
                'attachment_name' => $attachmentName,
                'priority'        => $request->priority,
                'status'          => $request->status,
                'published_at'    => $publishedAt,
                'updated_by'      => Auth::id(),
            ]);
        });

        Log::info('announcement_updated', [
            'id'         => $announcement->id,
            'priority'   => $request->priority,
            'status'     => $request->status,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('cms.announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Soft delete the specified announcement.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        $this->authorizeManage();

        $announcement->delete();

        Log::info('announcement_deleted', [
            'id'         => $announcement->id,
            'deleted_by' => Auth::id(),
        ]);

        return redirect()->route('cms.announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * Securely download the attachment file for CMS users (including Operator).
     */
    public function download(Announcement $announcement): StreamedResponse
    {
        $this->authorizeAccess();

        if (!$announcement->attachment_file || !Storage::disk('public')->exists($announcement->attachment_file)) {
            abort(404, 'Berkas lampiran tidak ditemukan.');
        }

        // Increment downloads_count without firing model events
        Announcement::withoutEvents(function () use ($announcement) {
            $announcement->increment('downloads_count');
        });

        $downloadName = $announcement->attachment_name ?: basename($announcement->attachment_file);

        return Storage::disk('public')->download($announcement->attachment_file, $downloadName);
    }

    /**
     * Allow super_admin, admin, editor, and operator to view listings & details.
     */
    private function authorizeAccess(): void
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole(['super_admin', 'admin', 'editor', 'operator'])) {
            abort(403, 'Akses ditolak.');
        }
    }

    /**
     * Restricted to super_admin, admin, and editor only.
     */
    private function authorizeManage(): void
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole(['super_admin', 'admin', 'editor'])) {
            abort(403, 'Anda tidak memiliki izin untuk mengelola pengumuman.');
        }
    }
}
