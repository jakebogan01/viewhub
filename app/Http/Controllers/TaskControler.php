<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskControler extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // view pagination results
        // return Task::latest()->paginate();

        return Inertia::render('Tasks/Index', [
            'tasks' => Task::query()
                // if you find something for the search input, append to the query
                ->filter(request(['search', 'status']))
                ->latest()
                ->simplePaginate(6)
                ->withQueryString()
                ->through(fn($task) => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'slug' => $task->slug,
                    'description' => $task->description,
                    'tag' => $task->tag->name,
                    'status' => $task->status->name,
                ]),
            // pass the search input to the view
            'filters' => request()->only(['search', 'status']),
            'statuses' => Status::all()->map->only(['id', 'name']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return Inertia::render('Tasks/Show', [
            'task' => [
                'title' => $task->title,
                'description' => $task->description,
                'tag' => $task->tag->name,
                'user' => $task->user->name,
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
}
