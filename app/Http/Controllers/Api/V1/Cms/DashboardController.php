<?php

namespace App\Http\Controllers\Api\V1\Cms;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\FileResource;
use App\Http\Resources\Api\V1\InformationResource;
use App\Services\DashboardActivityService;
use App\Services\DashboardStatsService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use ApiResponse;

    public function __construct(
        private DashboardStatsService $statsService,
        private DashboardActivityService $activityService,
    ) {}

    /**
     * Display aggregated CMS dashboard payload.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorizeAccess();

        $limit = max(1, min((int) $request->get('limit', 5), 50));

        $stats = $this->formatStats($this->statsService->getStats());
        $drafts = $this->formatDrafts($this->statsService->getDraftCounts());
        $topFiles = FileResource::collection($this->statsService->getTopDownloadedFiles($limit));
        $topPosts = InformationResource::collection($this->statsService->getTopViewedPosts($limit));
        $health = $this->statsService->getSystemHealth();
        $activities = $this->activityService->getLatestActivities($limit);

        return $this->successResponse([
            'stats'         => $stats,
            'drafts'        => $drafts,
            'analytics'     => [
                'top_downloaded_files' => $topFiles,
                'top_viewed_posts'     => $topPosts,
            ],
            'activity'      => $activities,
            'system_health' => $health,
        ], 'Dashboard CMS berhasil diambil.');
    }

    /**
     * Display content statistics metrics.
     */
    public function stats(): JsonResponse
    {
        $this->authorizeAccess();

        $rawStats = $this->statsService->getStats();

        return $this->successResponse(
            $this->formatStats($rawStats),
            'Statistik CMS berhasil diambil.'
        );
    }

    /**
     * Display draft counts for content modules.
     */
    public function drafts(): JsonResponse
    {
        $this->authorizeAccess();

        $rawDrafts = $this->statsService->getDraftCounts();

        return $this->successResponse(
            $this->formatDrafts($rawDrafts),
            'Data draft CMS berhasil diambil.'
        );
    }

    /**
     * Display latest activities across content modules.
     */
    public function activity(Request $request): JsonResponse
    {
        $this->authorizeAccess();

        $limit = max(1, min((int) $request->get('limit', 5), 50));
        $activities = $this->activityService->getLatestActivities($limit);

        return $this->successResponse(
            $activities,
            'Aktivitas terbaru CMS berhasil diambil.'
        );
    }

    /**
     * Display top analytics (downloaded files & viewed posts).
     */
    public function analytics(Request $request): JsonResponse
    {
        $this->authorizeAccess();

        $limit = max(1, min((int) $request->get('limit', 5), 50));

        $topFiles = FileResource::collection($this->statsService->getTopDownloadedFiles($limit));
        $topPosts = InformationResource::collection($this->statsService->getTopViewedPosts($limit));

        return $this->successResponse([
            'top_downloaded_files' => $topFiles,
            'top_viewed_posts'     => $topPosts,
        ], 'Analitik CMS berhasil diambil.');
    }

    /**
     * Display server and system health checks.
     */
    public function systemHealth(): JsonResponse
    {
        $this->authorizeAccess();

        $health = $this->statsService->getSystemHealth();

        return $this->successResponse(
            $health,
            'Status kesehatan sistem berhasil diambil.'
        );
    }

    private function authorizeAccess(): void
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole(['super_admin', 'admin', 'editor', 'operator'])) {
            abort(403, 'Akses ditolak.');
        }
    }

    private function formatStats(array $raw): array
    {
        return [
            'total_users'                 => $raw['total_users'] ?? 0,
            'total_information'           => $raw['total_information_posts'] ?? 0,
            'total_announcements'         => $raw['total_announcements'] ?? 0,
            'total_timelines'             => $raw['total_timelines'] ?? 0,
            'total_files'                 => $raw['total_files'] ?? 0,
            'total_registration_steps'    => $raw['total_registration_steps'] ?? 0,
            'total_file_downloads'        => $raw['total_file_downloads'] ?? 0,
            'total_announcement_downloads' => $raw['total_announcement_downloads'] ?? 0,
        ];
    }

    private function formatDrafts(array $raw): array
    {
        return [
            'information'        => $raw['draft_information'] ?? 0,
            'announcements'      => $raw['draft_announcements'] ?? 0,
            'timelines'          => $raw['draft_timelines'] ?? 0,
            'files'              => $raw['draft_files'] ?? 0,
            'registration_steps' => $raw['draft_registration_steps'] ?? 0,
        ];
    }
}
