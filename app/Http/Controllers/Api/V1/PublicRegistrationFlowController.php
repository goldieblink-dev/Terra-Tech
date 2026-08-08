<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\RegistrationStepResource;
use App\Models\RegistrationStep;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicRegistrationFlowController extends Controller
{
    use ApiResponse;

    /**
     * Display listing of published registration flow steps.
     */
    public function index(Request $request): JsonResponse
    {
        $version  = RegistrationStep::cacheVersion();
        $cacheKey = RegistrationStep::CACHE_PREFIX . 'v' . $version . '_api_index'
            . '_pg_' . ($request->get('page', 1));

        $steps = Cache::remember($cacheKey, 3600, function () {
            return RegistrationStep::published()
                ->orderedForPublic()
                ->paginate(10)
                ->withQueryString();
        });

        return $this->successResponse([
            'items'      => RegistrationStepResource::collection($steps),
            'pagination' => [
                'current_page' => $steps->currentPage(),
                'last_page'    => $steps->lastPage(),
                'per_page'     => $steps->perPage(),
                'total'        => $steps->total(),
            ],
        ], 'Daftar alur pendaftaran publik berhasil diambil.');
    }

    /**
     * Display details of a single published registration step by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $step = RegistrationStep::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->successResponse(
            new RegistrationStepResource($step),
            'Detail alur pendaftaran berhasil diambil.'
        );
    }
}
