<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Status;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TaskLikes;
use App\Models\TemporaryImage;
use App\Models\User;
use App\Notifications\TaskLiked;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
                // if you find something for the search input, append to the query
                ->filter(request(['search', 'status', 'tag']))
                ->simplePaginate(6)
                ->withQueryString()
                ->through(fn($task) => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'slug' => $task->slug,
                    'description' => $task->description,
                    'priority' => $task->priority,
                    'tag' => $task->tag->name,
                    'status' => $task->status->name,
                    'likes' => $task->likes->count(),
                    'owner_id' => $task->user->id,
                ]),
            'count' => Task::count(),
            // pass the search input to the view
            'filters' => request()->only(['search', 'status', 'tag', 'sortby', 'date', 'liked', 'priority']),
            'tags' => Tag::all(),
            'user' => Auth::user()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // removes all images from the tmpimages folder
        File::cleanDirectory(public_path() . '/tmpimages');

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

        if ($request->due_date !== null) {
            $attributes['due_date'] = $this->validateDueDate();
        }
        $attributes['user_id'] = auth()->id();
        $attributes['status_id'] = 1;
        $attributes['slug'] = Str::slug($attributes['title']) . '-' . $this->randomThreeDigitID();

        $task = Task::create($attributes);

        $temporaryImages = TemporaryImage::whereIn('folder', $request->images)->get();
        foreach($temporaryImages as $temporaryImage) {
            File::copyDirectory(public_path() . '/tmpimages', public_path() . '/images/tasks');
            Image::create([
                'task_id' => $task->id,
                'name' => $temporaryImage->file,
                'path' => $temporaryImage->folder . '/' . $temporaryImage->file,
            ]);
            File::deleteDirectory(public_path('tmpimages/' . $temporaryImage->folder));
            $temporaryImage->delete();
        }

        return redirect('/dashboard/tasks/' . $attributes['slug'])->with('message', 'Task created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return Inertia::render('Dashboard/Show', [
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'slug' => $task->slug,
                'description' => $task->description,
                'priority' => $task->priority,
                'due_date' => $task->due_date ? $task->due_date->format('m/d/y') : null,
                'tag' => $task->tag->name,
                'user' => $task->user->name,
                'likes' => $task->likes->count(),
                'owner_id' => $task->user->id,
                'images' => $task->images->map(function($image) {
                    return [
                        'id' => $image->id,
                        'name' => $image->name,
                        'path' => $image->path,
                    ];
                }),
                // add comments with pagination
                'comments' => $task->comments()
                    ->simplePaginate(5)
                    ->withQueryString()
                    ->through(fn($comment) => [
                        'id' => $comment->id,
                        'body' => $comment->body,
                        'user' => $comment->user->name,
                        'created_at' => $comment->created_at->format('F j, Y, g:i a'),
                ]),
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        // removes all images from the tmpimages folder
        File::cleanDirectory(public_path() . '/tmpimages');

        return Inertia::render('Dashboard/Edit', [
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'priority' => $task->priority,
                'due_date' => $task->due_date,
                'tag' => $task->tag->id,
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
        $attributes = $this->validateTask($task);
        if ($request->due_date !== null) {
            $attributes['due_date'] = $this->validateDueDate();
        } else {
            $attributes['due_date'] = null;
        }
        $attributes['status_id'] = $task->status_id;
        $attributes['slug'] = Str::slug($attributes['title']) . '-' . $this->randomThreeDigitID();

        $task->update($attributes);

        $temporaryImages = TemporaryImage::whereIn('folder', $request->images)->get();
        foreach($temporaryImages as $temporaryImage) {
            File::copyDirectory(public_path() . '/tmpimages', public_path() . '/images/tasks');
            Image::create([
                'task_id' => $task->id,
                'name' => $temporaryImage->file,
                'path' => $temporaryImage->folder . '/' . $temporaryImage->file,
            ]);
            File::deleteDirectory(public_path('tmpimages/' . $temporaryImage->folder));
            $temporaryImage->delete();
        }

        return redirect('/dashboard/tasks/' . $task->slug)->with('message', 'Task updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $images = Image::where('task_id', $task->id)->get();
        foreach($images as $image) {
            File::delete(public_path('images/tasks/' . $image->path));
            $image->delete();
        }

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
     * @param Task $task
     * @return RedirectResponse
     */
    public function toggle(Task $task) :RedirectResponse
    {
        $owner_id = request('user');
        $record = TaskLikes::where('user_id', auth()->user()->id)->where('task_id', $task->id)->first();

        if (auth()->user()->id !== $owner_id && !$record) {
            $notifiedUser = User::find($owner_id);
            $notifiedUser->notify(new TaskLiked(auth()->user(), $task->slug));
        }

        $task->likes()->toggle(auth()->user()->id);
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
            'priority' => 'required|integer|between:0,1',
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
