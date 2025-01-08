# Laravel CRUD: Breeze Blade Version

This project is a simple Laravel CRUD for Tasks model built on top of Laravel Breeze starter kit Blade version.

![](https://laraveldaily.com/uploads/2024/12/crud-breeze-tasks.png)

---

## Installation

Follow these steps to set up the project locally:

1. Clone the repository:
   ```bash
   git clone https://github.com/LaravelDaily/CRUDs-Laravel-Blade.git project
   cd project
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install && npm run build
   ```

3. Copy the `.env` file and configure your environment variables:
   ```bash
   cp .env.example .env
   ```

4. Generate the application key:
   ```bash
   php artisan key:generate
   ```

5. Set up the database:
    - Update `.env` with your database credentials.
    - Run migrations and seed the database, repo includes fake tasks:
      ```bash
      php artisan migrate --seed
      ```

6. If you use Laravel Herd/Valet, access the application at `http://project.test`.

7. Log in with credentials: `test@example.com` and `password`.  

---

## How it Works?

This project takes Laravel Breeze as a base system and adds the Task CRUD on top of it. Here are the main changes:

For this, we will start with simple Database table and Model:

**Migration**
```php
Schema::create('tasks', function (Blueprint $table) {
   $table->id();
   $table->string('name');
   $table->string('description');
   $table->foreignId('user_id')->nullable()->constrained();
   $table->timestamps();
});
```

**app/Models/Task.php**
```php
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

Then, we will create the Factory and Seeder to have some fake data in the database:

**database/factories/TaskFactory.php**
```php
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->text(20),
            'description' => fake()->text(200),
        ];
    }
}
```

**database/seeders/TaskSeeder.php**
```php
public function run(): void
{
  Task::factory(50)->create();
}
```

**database/seeders/DatabaseSeeder.php**
```php
public function run(): void
{
// User::factory(10)->create();

User::factory()->create([
   'name' => 'Test User',
   'email' => 'test@example.com',
]);

$this->call(TaskSeeder::class);// [tl! ++]
}
```

![](https://laraveldaily.com/uploads/2025/01/database-example.png)

And now, we will create a Controller with all the CRUD methods:

**app/Http/Controllers/TaskController.php**
```php
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
```
 
In there, you should note a few things:
1. Return Types in the Controller: ex. `public function index(): View`
2. Utilizes Form Request classes for validation, with `$request->validated()` then used in the Controller

Next, we will create the Blade views for the CRUD:

**resources/views/tasks/index.blade.php**
```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tasks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-hidden overflow-x-auto p-6 bg-white border-b border-gray-200">
                    <div class="min-w-full align-middle">
                        <x-link-button href="{{ route('tasks.create') }}">{{ __('Add New Task') }}</x-link-button>

                        <table class="min-w-full divide-y divide-gray-200 border mt-4">
                            <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left">
                                    <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">{{ __('Name') }}</span>
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left">
                                    <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">{{ __('Description') }}</span>
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left">
                                    <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">{{ __('Assigned to') }}</span>
                                </th>
                                <th class="px-6 py-3 bg-gray-50">

                                </th>
                            </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200 divide-solid">
                            @foreach($tasks as $task)
                                <tr class="bg-white">
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                        {{ $task->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                        {{ str($task->description)->limit(50) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                        {{ $task->user?->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                        <x-link-button href="{{ route('tasks.edit', $task) }}">{{ __('Edit') }}</x-link-button>
                                        <form action="{{ route('tasks.destroy', $task) }}"
                                              class="inline-block"
                                              method="POST"
                                              onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button>{{ __('Delete') }}</x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        {{ $tasks->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

And of course, Create form:

**resources/views/tasks/create.blade.php**
```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Task') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-hidden overflow-x-auto p-6 bg-white border-b border-gray-200">
                    <div class="min-w-full align-middle">
                        <form method="POST" action="{{ route('tasks.store') }}">
                            @csrf

                            <!-- Name -->
                            <div>
                                <x-input-label for="name" :value="__('Name')" required />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div class="mt-4">
                                <x-input-label for="description" :value="__('Description')" required />
                                <x-text-input id="description" class="block mt-1 w-full" type="text" name="description" :value="old('description')" required />
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <!-- Assigned to user -->
                            <div class="mt-4">
                                <x-input-label for="user_id" :value="__('Assigned to')" />
                                <select name="user_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- {{ __('please choose the option') }} --</option>
                                    @foreach ($users as $id => $name)
                                        <option value="{{ $id }}" @selected($id === old('user_id'))>{{ $name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-primary-button>
                                    {{ __('Save') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

For this, we modified Laravel Breeze Blade component `x-input-label` to add a "required" asterisk parameter.

![](https://laraveldaily.com/uploads/2025/01/form-modification-for-required-star.png)

We also have added Flash messages to our Controller:

```php
// ...

return redirect()->route('tasks.index')
            ->with('message', __('Task created successfully'));

// ...
```

Let's display them in our View:

**resources/views/tasks/index.blade.php**
```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tasks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-hidden overflow-x-auto p-6 bg-white border-b border-gray-200">
                    <div class="min-w-full align-middle">
                        <x-alert-message />{{-- [tl! ++] --}}

                        <x-link-button href="{{ route('tasks.create') }}">{{ __('Add New Task') }}</x-link-button>
                        
                        {{-- ... --}}
                    </div>

                    <div class="mt-2">
                        {{ $tasks->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

![](https://laraveldaily.com/uploads/2025/01/flash-message.png)

And lastly, we will add the tests for the CRUD:

**tests/Feature/TasksCRUDTest.php**
```php
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
```

![](https://laraveldaily.com/uploads/2024/12/crud-breeze-tasks-tests.png)
