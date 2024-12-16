<?php

use App\Models\Task;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('index page displays tasks', function () {
    $tasks = Task::factory()->count(3)->create();

    $response = $this->get(route('tasks.index'));

    $response->assertOk();
    foreach ($tasks as $task) {
        $response->assertSee($task->name);
    }
});

test('create page loads successfully', function () {
    $response = $this->get(route('tasks.create'));

    $response->assertOk();
});

test('store task validates and persists data', function () {
    $user = User::factory()->create();
    $taskData = [
        'name' => 'Test Task',
        'description' => 'Test description',
        'user_id' => $user->id,
    ];

    $response = $this->post(route('tasks.store'), $taskData);

    $response->assertRedirect(route('tasks.index'));
    $response->assertSessionHas('message', __('Task created successfully'));

    $this->assertDatabaseHas('tasks', $taskData);
});

test('edit page loads successfully', function () {
    $task = Task::factory()->create();

    $response = $this->get(route('tasks.edit', $task));

    $response->assertOk();
    $response->assertSee($task->name);
});

test('update task validates and persists changes', function () {
    $task = Task::factory()->create();
    $newData = [
        'name' => 'Updated Task',
        'description' => 'Updated description',
        'user_id' => null,
    ];

    $response = $this->put(route('tasks.update', $task), $newData);

    $response->assertRedirect(route('tasks.index'));
    $response->assertSessionHas('message', __('Task updated successfully'));

    $this->assertDatabaseHas('tasks', array_merge(['id' => $task->id], $newData));
});

test('destroy task deletes it from database', function () {
    $task = Task::factory()->create();

    $response = $this->delete(route('tasks.destroy', $task));

    $response->assertRedirect(route('tasks.index'));
    $response->assertSessionHas('message', __('Task deleted successfully'));

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('store task fails validation with missing name', function () {
    $taskData = [
        'description' => 'Test description',
        'user_id' => null,
    ];

    $response = $this->post(route('tasks.store'), $taskData);

    $response->assertSessionHasErrors(['name']);
});

test('store task fails validation with invalid user_id', function () {
    $taskData = [
        'name' => 'Test Task',
        'description' => 'Test description',
        'user_id' => 999, // Non-existent user
    ];

    $response = $this->post(route('tasks.store'), $taskData);

    $response->assertSessionHasErrors(['user_id']);
});

test('update task fails validation with missing description', function () {
    $task = Task::factory()->create();
    $newData = [
        'name' => 'Updated Task',
        'description' => '',
    ];

    $response = $this->put(route('tasks.update', $task), $newData);

    $response->assertSessionHasErrors(['description']);
});
