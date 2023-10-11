<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Project;
use App\Models\Status;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TemporaryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // view pagination results
        // return Task::latest()->paginate();

        return Inertia::render('Dashboard/Index', [
            'tasks' => Task::query()
                ->filter(request(['search', 'status', 'tag']))
                ->simplePaginate(6)
                ->withQueryString()
                ->through(fn($task) => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'slug' => $task->slug,
                    'description' => $task->description,
                    'tag' => $task->tag->name,
                    'status' => $task->status->name,
                    'likes' => $task->likes->count(),
                    'comment_count' => $task->comments->count(),
                    'owner_id' => $task->user->id,
                ]),
            'count' => Task::count(),
            'filters' => request()->only(['search', 'status', 'tag', 'sortby', 'liked', 'commented']),
            'tags' => Tag::has('tasks')->get(),
            'statuses' => Status::all()->map(function($status) {
                return [
                    'id' => $status->id,
                    'name' => $status->name,
                    'count' => $status->tasks->count(),
                ];
            }),
            'user' => Auth::user()
        ]);
    }

    public function allTasks()
    {
        return Inertia::render('Dashboard/AllTasks', [
            'tasks' => Task::
                simplePaginate(6)
                ->withQueryString()
                ->through(fn($task) => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'slug' => $task->slug,
                    'description' => $task->description,
                    'tag' => $task->tag->name,
                    'user' => $task->user->name,
                    'user_id' => $task->user->id,
                    'likes' => $task->likes->count(),
                    'comment_count' => $task->comments->count(),
                    'owner_id' => $task->user->id,
                ]),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Dashboard/Create', [
            'tags' => Tag::all(),
            'statuses' => Status::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $attributes = $this->validateTask();

        $attributes['user_id'] = auth()->id();
        $attributes['status_id'] = 1;
        $attributes['slug'] = Str::slug($attributes['title']) . '-' . $this->randomThreeDigitID();

        $task = Task::create($attributes);

        $temporaryImages = TemporaryImage::whereIn('folder', $request->images)->get();
        foreach($temporaryImages as $temporaryImage) {
            File::copyDirectory(public_path() . '/tmpimages', public_path() . '/images');
            Image::create([
                'task_id' => $task->id,
                'name' => $temporaryImage->file,
                'path' => $temporaryImage->folder . '/' . $temporaryImage->file,
            ]);
            File::cleanDirectory(public_path() . '/tmpimages/user' . auth()->user()->id);
            $temporaryImage->delete();
        }

        return redirect('/dashboard/tasks/' . $attributes['slug'])->with('message', 'Task created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return Inertia::render('Dashboard/Show', [
            'client_d' => auth()->user()->id,
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'slug' => $task->slug,
                'description' => $task->description,
                'tag' => $task->tag->name,
                'user' => $task->user->name,
                'user_id' => $task->user->id,
                'likes' => $task->likes->count(),
                'comment_count' => $task->comments->count(),
                'owner_id' => $task->user->id,
                'images' => $task->images->map(function($image) {
                    return [
                        'id' => $image->id,
                        'name' => $image->name,
                        'path' => $image->path,
                    ];
                }),
                'comments' => $task->comments()
                    ->orderByDesc('created_at')
                    ->simplePaginate(5)
                    ->withQueryString()
                    ->through(fn($comment) => [
                        'id' => $comment->id,
                        'body' => $comment->body,
                        'user' => $comment->user->only('id', 'name', 'username', 'avatar'),
                        'default_avatar' => $comment->user->getAvatar(),
                        'created_at' => $comment->created_at
                            ->setTimezone(auth()->user()->timezone)
                            ->format('F j, Y, g:i a'),
                        'replies' => $comment->replies
                            ->map(fn($reply) => [
                                'id' => $reply->id,
                                'body' => $reply->body,
                                'user_id' => $reply->user->id,
                                'username' => $reply->user->username,
                                'default_avatar' => $reply->user->getAvatar(),
                                'user_avatar' => $reply->user->avatar,
                                'recipient' => $reply->recipient->username,
                                'created_at' => $reply->created_at
                                    ->setTimezone(auth()->user()->timezone)
                                    ->format('F j, Y, g:i a'),
                            ])->sortByDesc('created_at')->reverse()->values(),
                    ])
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        if ($task->user_id !== auth()->user()->id) {
            abort(403);
        }

        return Inertia::render('Dashboard/Edit', [
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'slug' => $task->slug,
                'description' => $task->description,
                'priority' => $task->priority,
                'due_date' => $task->due_date,
                'tag' => $task->tag->id,
                'user_id' => $task->user->id,
                'images' => $task->images->map(function($image) {
                    return [
                        'id' => $image->id,
                        'name' => $image->name,
                        'path' => $image->path,
                    ];
                }),
            ],
            'tags' => Tag::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Task $task, Request $request)
    {
        if ($task->user_id !== auth()->user()->id) {
            abort(403);
        }

        $attributes = $this->validateTask($task);
        $attributes['status_id'] = $task->status_id;
        $attributes['slug'] = Str::slug($attributes['title']) . '-' . $this->randomThreeDigitID();

        $task->update($attributes);

        $temporaryImages = TemporaryImage::whereIn('folder', $request->images)->get();
        foreach($temporaryImages as $temporaryImage) {
            File::copyDirectory(public_path() . '/tmpimages', public_path() . '/images');
            Image::create([
                'task_id' => $task->id,
                'name' => $temporaryImage->file,
                'path' => $temporaryImage->folder . '/' . $temporaryImage->file,
            ]);
            File::cleanDirectory(public_path() . '/tmpimages/user' . auth()->user()->id);
            $temporaryImage->delete();
        }

        return redirect('/dashboard/tasks/' . $task->slug)->with('message', 'Task updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->user()->id) {
            abort(403);
        }

        $images = Image::where('task_id', $task->id)->get();
        foreach($images as $image) {
            File::delete(public_path('images/' . $image->path));
            $image->delete();
        }

        $task->comments->each(function($comment) {
            $comment->delete();
        });
        $task->delete();

        return to_route('dashboard.index')->with('message', 'Task deleted successfully!');
    }

    /**
     * Updates dark mode.
     */
    public function enableDarkMode()
    {
        $attributes = request()->validate([
            'dark_mode' => 'required|boolean',
        ]);

        if ($attributes['dark_mode']) {
            $attributes['dark_mode'] = 1;
        } else {
            $attributes['dark_mode'] = 0;
        }

        auth()->user()->update($attributes);

        return redirect()->back();
    }

    /**
     * @param Task|null $task
     * @return array
     */
    protected function validateTask(?Task $task = null): array
    {
        $task ??= new Task();
        return request()->validate([
            'title' => 'required',
            'description' => 'required',
            'tag_id' => ['required', Rule::exists('tags', 'id')],
        ]);
    }

    protected function randomThreeDigitID() {
        return rand(100, 999);
    }

    private function validateDueDate()
    {
        return request()->validate([
            'due_date' => 'date|after_or_equal:today',
        ])['due_date'];
    }
}
