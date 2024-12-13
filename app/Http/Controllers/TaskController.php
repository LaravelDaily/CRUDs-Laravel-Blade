<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(): View
    {
        $tasks = Task::with('user')->paginate(10);

        return view('tasks.index', compact('tasks'));
    }

    public function create(): View
    {
        $users = User::select(['id', 'name'])->pluck('name', 'id');

        return view('tasks.create', compact('users'));
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        Task::create($request->validated());

        return redirect()->route('tasks.index')
            ->with('message', __('Task created successfully'));
    }

    public function edit(Task $task): View
    {
        $users = User::select(['id', 'name'])->pluck('name', 'id');

        return view('tasks.edit', compact('task', 'users'));
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return redirect()->route('tasks.index')
            ->with('message', __('Task updated successfully'));
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('message', __('Task deleted successfully'));
    }
}
