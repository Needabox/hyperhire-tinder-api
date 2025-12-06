<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LikedPersonResource;
use App\Models\User;
use App\Services\LikeDislikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LikeDislikeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected LikeDislikeService $likeDislikeService
    ) {
    }

    /**
     * Like a person.
     *
     * @OA\Post(
     *     path="/people/{user_id}/like",
     *     summary="Like a person",
     *     description="Like a person by their user ID",
     *     tags={"People"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to like",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="X-Device-ID",
     *         in="header",
     *         required=true,
     *         description="Device ID for authentication",
     *         @OA\Schema(type="string", example="a0f07553-e5e0-32d0-b600-1eb557befbdf")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Person liked successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Person liked.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="X-Device-ID header is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found.")
     *         )
     *     )
     * )
     *
     * @param  Request  $request
     * @param  int  $user_id
     * @return JsonResponse
     */
    public function like(Request $request, int $user_id): JsonResponse
    {
        try {
            // Get current user from device_id
            $deviceId = $request->input('device_id');
            $currentUser = User::where('device_id', $deviceId)->firstOrFail();

            $this->likeDislikeService->likeUser($currentUser->id, $user_id);

            return response()->json([
                'message' => 'Person liked.',
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error liking user', [
                'error' => $e->getMessage(),
                'user_id' => $user_id,
                'device_id' => $deviceId ?? null,
            ]);

            return response()->json([
                'message' => 'An error occurred while processing your request.',
            ], 500);
        }
    }

    /**
     * Dislike a person.
     *
     * @OA\Post(
     *     path="/people/{user_id}/dislike",
     *     summary="Dislike a person",
     *     description="Dislike a person by their user ID",
     *     tags={"People"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to dislike",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="X-Device-ID",
     *         in="header",
     *         required=true,
     *         description="Device ID for authentication",
     *         @OA\Schema(type="string", example="a0f07553-e5e0-32d0-b600-1eb557befbdf")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Person disliked successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Person disliked.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="X-Device-ID header is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found.")
     *         )
     *     )
     * )
     *
     * @param  Request  $request
     * @param  int  $user_id
     * @return JsonResponse
     */
    public function dislike(Request $request, int $user_id): JsonResponse
    {
        try {
            // Get current user from device_id
            $deviceId = $request->input('device_id');
            $currentUser = User::where('device_id', $deviceId)->firstOrFail();

            $this->likeDislikeService->dislikeUser($currentUser->id, $user_id);

            return response()->json([
                'message' => 'Person disliked.',
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error disliking user', [
                'error' => $e->getMessage(),
                'user_id' => $user_id,
                'device_id' => $deviceId ?? null,
            ]);

            return response()->json([
                'message' => 'An error occurred while processing your request.',
            ], 500);
        }
    }

    /**
     * Get liked people list.
     *
     * @OA\Get(
     *     path="/people/{user_id}/liked-list",
     *     summary="Get liked people list",
     *     description="Get list of people that have liked the specified user",
     *     tags={"People"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to get liked list",
     *         @OA\Schema(type="integer", example=1)
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
     *                     @OA\Property(property="person_id", type="integer", example=998),
     *                     @OA\Property(property="name", type="string", example="Annalise Schultz"),
     *                     @OA\Property(property="age", type="integer", example=22),
     *                     @OA\Property(
     *                         property="pictures",
     *                         type="array",
     *                         @OA\Items(type="string"),
     *                         example={"https://via.placeholder.com/640x480.png/00dd88?text=people+qui", "https://picsum.photos/640/480?random=656"}
     *                     ),
     *                     @OA\Property(property="liked_at", type="string", example="2025-12-05 08:39:39")
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
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found.")
     *         )
     *     )
     * )
     *
     * @param  Request  $request
     * @param  int  $user_id
     * @return JsonResponse
     */
    public function likedList(Request $request, int $user_id): JsonResponse
    {
        try {
            $page = (int) $request->input('page', 1);
            $limit = (int) $request->input('limit', 20);

            // Validate pagination parameters
            if ($page < 1) {
                $page = 1;
            }
            if ($limit < 1 || $limit > 100) {
                $limit = 20;
            }

            $likes = $this->likeDislikeService->getLikedList($user_id, $page, $limit);

            return response()->json([
                'items' => LikedPersonResource::collection($likes->items()),
                'meta' => [
                    'page' => $likes->currentPage(),
                    'limit' => $likes->perPage(),
                    'total' => $likes->total(),
                    'total_page' => $likes->lastPage(),
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error getting liked list', [
                'error' => $e->getMessage(),
                'user_id' => $user_id,
            ]);

            return response()->json([
                'message' => 'An error occurred while processing your request.',
            ], 500);
        }
    }
}

