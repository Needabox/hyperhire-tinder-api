<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RecommendedPeopleRequest;
use App\Http\Resources\RecommendedPeopleResource;
use App\Services\RecommendedPeopleService;
use Illuminate\Http\JsonResponse;

class RecommendedPeopleController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected RecommendedPeopleService $recommendedPeopleService
    ) {
    }

    /**
     * Get recommended people based on location.
     *
     * @OA\Get(
     *     path="/people/recommended",
     *     summary="Get recommended people based on location",
     *     description="Returns a paginated list of recommended people sorted by distance from the provided coordinates",
     *     tags={"People"},
     *     @OA\Parameter(
     *         name="lat",
     *         in="query",
     *         required=true,
     *         description="Latitude for recommendation",
     *         @OA\Schema(type="number", format="float", example=-6.2088)
     *     ),
     *     @OA\Parameter(
     *         name="lng",
     *         in="query",
     *         required=true,
     *         description="Longitude for recommendation",
     *         @OA\Schema(type="number", format="float", example=106.8456)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1, example=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", default=20, example=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=998),
     *                     @OA\Property(property="name", type="string", example="Annalise Schultz"),
     *                     @OA\Property(property="age", type="integer", example=22),
     *                     @OA\Property(
     *                         property="pictures",
     *                         type="array",
     *                         @OA\Items(type="string"),
     *                         example={"https://via.placeholder.com/640x480.png/00dd88?text=people+qui", "https://picsum.photos/640/480?random=656"}
     *                     ),
     *                     @OA\Property(property="distance_km", type="number", format="float", example=1.2)
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="page", type="integer", example=1),
     *                 @OA\Property(property="limit", type="integer", example=2),
     *                 @OA\Property(property="total", type="integer", example=8),
     *                 @OA\Property(property="total_page", type="integer", example=4)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The lat field is required."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\AdditionalProperties(
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * @param  RecommendedPeopleRequest  $request
     * @return JsonResponse
     */
    public function __invoke(RecommendedPeopleRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $users = $this->recommendedPeopleService->getRecommendedPeople(
            latitude: $validated['lat'],
            longitude: $validated['lng'],
            page: $validated['page'],
            limit: $validated['limit']
        );

        return response()->json([
            'items' => RecommendedPeopleResource::collection($users->items()),
            'meta' => [
                'page' => $users->currentPage(),
                'limit' => $users->perPage(),
                'total' => $users->total(),
                'total_page' => $users->lastPage(),
            ],
        ]);
    }
}

