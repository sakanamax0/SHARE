<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'comments.user', 'likes'])->latest()->get();

        $formattedPosts = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'user_id' => $post->user_id,
                'content' => $post->content,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
                'user' => $post->user,
                'comments' => $post->comments,
                'likes_count' => $post->likes()->count(),
            ];
        });

        return response()->json($formattedPosts, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string|max:1000',
        ]);

        $post = Post::create([
            'user_id' => $request->user_id,
            'content' => $request->content,
        ]);

        $post->load(['user', 'comments.user', 'likes']);

        return response()->json([
            'message' => '投稿に成功しました',
            'post' => [
                'id' => $post->id,
                'user_id' => $post->user_id,
                'content' => $post->content,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
                'user' => $post->user,
                'comments' => $post->comments,
                'likes_count' => $post->likes()->count(),
            ],
        ], 201);
    }

    public function show($id)
    {
        $post = Post::with([
            'user',
            'comments.user'
        ])
            ->withCount('likes')
            ->findOrFail($id);

        return response()->json([
            'id' => $post->id,
            'user_id' => $post->user_id,
            'content' => $post->content,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
            'user' => $post->user,
            'comments' => $post->comments,
            'likes_count' => $post->likes_count,
        ]);
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => '投稿が見つかりません'
            ], 404);
        }

        $post->delete();

        return response()->json([
            'message' => '投稿を削除しました'
        ], 200);
    }
}
