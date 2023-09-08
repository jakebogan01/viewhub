<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Comment;

class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $attributes = request()->validate([
            'task_id' => ['required', Rule::exists('tasks', 'id')],
            'body' => 'required|max:255'
        ]);

        $attributes['user_id'] = auth()->id();

        Comment::create($attributes);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Comment $comment, Request $request)
    {
        $attributes = request()->validate([
            'task_id' => ['required', Rule::exists('tasks', 'id')],
            'body' => 'required|max:255'
        ]);

        $attributes['user_id'] = auth()->id();

        $comment->update($attributes);

        return redirect()->back();
    }
}
