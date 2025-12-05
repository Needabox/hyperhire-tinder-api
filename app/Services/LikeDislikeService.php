<?php

namespace App\Services;

use App\Models\Dislike;
use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class LikeDislikeService
{
    /**
     * Like a user.
     *
     * @param  int  $userId
     * @param  int  $targetUserId
     * @return void
     *
     * @throws ModelNotFoundException
     */
    public function likeUser(int $userId, int $targetUserId): void
    {
        // Validate that target user exists
        User::findOrFail($targetUserId);

        // Prevent self-like
        if ($userId === $targetUserId) {
            throw new \InvalidArgumentException('Cannot like yourself.');
        }

        DB::transaction(function () use ($userId, $targetUserId) {
            // Check if already liked
            $existingLike = Like::where('user_id', $userId)
                ->where('target_user_id', $targetUserId)
                ->first();

            if ($existingLike) {
                return; // Already liked, do nothing
            }

            // Remove dislike if exists
            Dislike::where('user_id', $userId)
                ->where('target_user_id', $targetUserId)
                ->delete();

            // Create like
            Like::create([
                'user_id' => $userId,
                'target_user_id' => $targetUserId,
            ]);
        });
    }

    /**
     * Dislike a user.
     *
     * @param  int  $userId
     * @param  int  $targetUserId
     * @return void
     *
     * @throws ModelNotFoundException
     */
    public function dislikeUser(int $userId, int $targetUserId): void
    {
        // Validate that target user exists
        User::findOrFail($targetUserId);

        // Prevent self-dislike
        if ($userId === $targetUserId) {
            throw new \InvalidArgumentException('Cannot dislike yourself.');
        }

        DB::transaction(function () use ($userId, $targetUserId) {
            // Check if already disliked
            $existingDislike = Dislike::where('user_id', $userId)
                ->where('target_user_id', $targetUserId)
                ->first();

            if ($existingDislike) {
                return; // Already disliked, do nothing
            }

            // Remove like if exists
            Like::where('user_id', $userId)
                ->where('target_user_id', $targetUserId)
                ->delete();

            // Create dislike
            Dislike::create([
                'user_id' => $userId,
                'target_user_id' => $targetUserId,
            ]);
        });
    }

    /**
     * Get liked people list for a user.
     * Returns list of people who have liked the specified user.
     *
     * @param  int  $userId
     * @param  int  $page
     * @param  int  $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     *
     * @throws ModelNotFoundException
     */
    public function getLikedList(int $userId, int $page = 1, int $limit = 20)
    {
        // Validate that user exists
        User::findOrFail($userId);

        return Like::where('target_user_id', $userId)
            ->with(['user.pictures' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);
    }
}

