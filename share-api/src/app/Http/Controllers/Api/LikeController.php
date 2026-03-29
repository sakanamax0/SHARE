<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $like = Like::where('post_id', $request->post_id)
            ->where('user_id', $request->user_id)
            ->first();

        if ($like) {
            $like->delete();

            $likesCount = Like::where('post_id', $request->post_id)->count();

            return response()->json([
                'message' => 'いいねを解除しました',
                'liked' => false,
                'likes_count' => $likesCount,
            ], 200);
        }

        Like::create([
            'post_id' => $request->post_id,
            'user_id' => $request->user_id,
        ]);

        $likesCount = Like::where('post_id', $request->post_id)->count();

        return response()->json([
            'message' => 'いいねしました',
            'liked' => true,
            'likes_count' => $likesCount,
        ], 201);
    }
}
