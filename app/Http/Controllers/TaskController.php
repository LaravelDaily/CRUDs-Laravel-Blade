<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(): View
    {
        $tasks = Task::paginate(10);

        return view('tasks.index', compact('tasks'));
    }

    public function create(): View
    {
        //
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        //
    }

    public function show(Task $task): View
    {
        //
    }

    public function edit(Task $task): View
    {
        //
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        //
    }

    public function destroy(Task $task): RedirectResponse
    {
        //
    }
}
