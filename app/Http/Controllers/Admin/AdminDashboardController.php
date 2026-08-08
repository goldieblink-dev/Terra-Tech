<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardActivityService;
use App\Services\DashboardStatsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __construct(
        private DashboardStatsService $statsService,
        private DashboardActivityService $activityService,
    ) {}

    public function __invoke(): View
    {
        $this->authorizeAccess();

        $stats      = $this->statsService->getStats();
        $drafts     = $this->statsService->getDraftCounts();
        $topFiles   = $this->statsService->getTopDownloadedFiles();
        $topPosts   = $this->statsService->getTopViewedPosts();
        $health     = $this->statsService->getSystemHealth();
        $activities = $this->activityService->getLatestActivities();

        return view('admin.dashboard.index', compact(
            'stats',
            'drafts',
            'topFiles',
            'topPosts',
            'health',
            'activities',
        ));
    }

    private function authorizeAccess(): void
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole(['super_admin', 'admin', 'editor', 'operator'])) {
            abort(403, 'Akses ditolak.');
        }
    }
}
