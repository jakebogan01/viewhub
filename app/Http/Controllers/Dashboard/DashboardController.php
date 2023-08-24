<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Status;
use App\Models\Tag;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

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
                    'tag' => $task->tag->name,
                    'status' => $task->status->name,
                    'likes' => $task->likes->count(),
                ]),
            'count' => Task::count(),
            // pass the search input to the view
            'filters' => request()->only(['search', 'status', 'tag', 'sortby']),
            'tags' => Tag::all(),
            'user' => Auth::user()
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
    public function store()
    {
        $attributes = request()->validate([
            'title' => 'required',
            'description' => 'required',
            'status_id' => ['required', Rule::exists('statuses', 'id')],
            'tag_id' => ['required', Rule::exists('tags', 'id')],
        ]);

        $attributes['user_id'] = auth()->id();
        $attributes['slug'] = Str::slug($attributes['title']);

        Task::create($attributes);

        return redirect('/dashboard/tasks/' . $attributes['slug']);
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
                'description' => $task->description,
                'tag' => $task->tag->name,
                'user' => $task->user->name,
                'likes' => $task->likes->count(),
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function toggle(Task $task)
    {
        $task->likes()->toggle(auth()->user()->id);
        return redirect()->back();
    }
}
