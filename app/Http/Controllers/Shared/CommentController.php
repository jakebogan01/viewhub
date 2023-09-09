<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Reply;
use App\Notifications\CommentReceived;
use App\Notifications\CommentReplyReceived;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Comment;

class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function storeComment(Request $request)
    {
        $attributes = request()->validate([
            'task_id' => ['required', Rule::exists('tasks', 'id')],
            'body' => 'required'
        ]);

        $attributes['user_id'] = auth()->id();

        $comment = Comment::create($attributes);

        $comment->task->user->notify(new CommentReceived(auth()->user(), $comment->task->slug, $comment->id));

        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeReply(Request $request)
    {
        $attributes = request()->validate([
            'comment_id' => ['required', Rule::exists('comments', 'id')],
            'recipient_id' => ['required', Rule::exists('users', 'id')],
            'body' => 'required'
        ]);

        $attributes['user_id'] = auth()->id();

        $reply = Reply::create($attributes);

        $reply->comment->user->notify(new CommentReplyReceived(auth()->user(), $reply->comment->task->slug, $reply->id));

        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeCommentReply(Request $request)
    {
        $attributes = request()->validate([
            'comment_id' => ['required', Rule::exists('comments', 'id')],
            'recipient_id' => ['required', Rule::exists('users', 'id')],
            'body' => 'required'
        ]);

        $attributes['user_id'] = auth()->id();

        $reply = Reply::create($attributes);

        $reply->recipient->notify(new CommentReplyReceived(auth()->user(), $reply->comment->task->slug, $reply->id));

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyComment(Comment $comment)
    {
        $comment->delete();
        $comment->replies()->delete();
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyReply(Reply $reply)
    {
        $reply->delete();
        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateComment(Comment $comment, Request $request)
    {
        $attributes = request()->validate([
            'task_id' => ['required', Rule::exists('tasks', 'id')],
            'body' => 'required'
        ]);

        $attributes['user_id'] = auth()->id();

        $comment->update($attributes);

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateReply(Reply $reply, Request $request)
    {
        $attributes = request()->validate([
            'comment_id' => ['required', Rule::exists('comments', 'id')],
            'recipient_id' => ['required', Rule::exists('users', 'id')],
            'body' => 'required'
        ]);

        $attributes['user_id'] = auth()->id();

        $reply->update($attributes);

        return redirect()->back();
    }
}
