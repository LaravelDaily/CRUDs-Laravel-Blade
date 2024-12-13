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

## Features to Pay Attention To

This project goes beyond the default Laravel Breeze setup with the following enhancements.

1. Return Types in the Controller: ex. `public function index(): View`
2. Utilizes Form Request classes for validation, with `$request->validated()` then used in the Controller
3. Includes Factory and Seeder for the `Task` Model
4. Modifies Laravel Breeze Blade component `x-input-label` to add a "required" asterisk parameter
5. Uses "flash" messages in the session to show the result after store/update/delete. Adds a custom Blade component `x-alert-message` for this.
6. Transform the links to look like buttons visually, with the same Tailwind styles as Laravel Breeze components. Adds a custom Blade component `x-link-button` for this.
7. Includes Pest test file `TasksCRUDTest` that has methods to test all Controller routes and also validation of each field.

![](https://laraveldaily.com/uploads/2024/12/crud-breeze-tasks-tests.png)

---

## Found a bug? Got a question/idea? 

Raise a GitHub issue or email `info@laraveldaily.com`. 
