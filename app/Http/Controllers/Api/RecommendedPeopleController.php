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
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", default=20, example=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=10),
     *                     @OA\Property(property="name", type="string", example="Alicia"),
     *                     @OA\Property(property="age", type="integer", example=23),
     *                     @OA\Property(
     *                         property="pictures",
     *                         type="array",
     *                         @OA\Items(type="string"),
     *                         example={"https://cdn.app.com/1.jpg", "https://cdn.app.com/2.jpg"}
     *                     ),
     *                     @OA\Property(property="distance_km", type="number", format="float", example=1.2)
     *                 )
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
            perPage: $validated['per_page']
        );

        return response()->json([
            'current_page' => $users->currentPage(),
            'data' => RecommendedPeopleResource::collection($users->items()),
        ]);
    }
}

