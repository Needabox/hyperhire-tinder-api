<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
     *         @OA\Schema(type="string", example="device-12345")
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
     *         @OA\Schema(type="string", example="device-12345")
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
}

