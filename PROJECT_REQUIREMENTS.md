# Task of the Day — Countries Capital Quiz (Laravel)

**Level:** Beginner → Intermediate
**Estimated time:** ~4 hours (setup + core feature)
**Stack:** Laravel 11 · Blade · Bootstrap 5

---

## 1. Goal

Build a small web application called **"Guess the Capital"**.

The app shows the user a random **country** and **3 possible capital cities**. The user
picks one and submits. The app tells them whether they were **right or wrong**, and if
wrong, shows the **correct capital**. A button lets them try a **new question**. The loop
continues until the user leaves.

You will get the country/capital data from a **free public API**, not from a database you
build yourself.

> **Data source (external API):**
> `https://countriesnow.space/api/v0.1/countries/capital`
> It returns a JSON object with a `data` array; each item has a `name` (country) and a
> `capital`. Open it in your browser first to see the shape of the response.

---

## 2. Learning objectives

By finishing this task you should be comfortable with:

- Installing PHP, Composer and a fresh Laravel project from zero.
- Routing (`web.php`), controllers, and Blade views + layouts.
- Calling an external API with Laravel's HTTP client.
- Caching an API response so you don't hit the external service on every request.
- Using the **session** to remember state between two requests.
- **Form Request** validation.
- (Stretch) Exposing your own JSON API endpoint protected with **Sanctum** tokens.

---

## 3. Environment setup (install everything from scratch)

Use as it was already setup.

### 3.1 Install PHP + Composer

- **Ubuntu/Debian:** `sudo apt install php php-cli php-sqlite3 php-mbstring php-xml unzip`, then install Composer from <https://getcomposer.org/download/>


Verify:

```bash
php -v
composer -V
```

### 3.2 Create a new Laravel project

```bash
composer create-project laravel/laravel countries_quiz
cd countries_quiz
```

### 3.3 Configure the environment

Laravel copies `.env.example` to `.env` automatically. Confirm these values in `.env`
(SQLite keeps setup simple — no DB server required):

```env
DB_CONNECTION=sqlite

SESSION_DRIVER=database

CACHE_STORE=database
CACHE_PREFIX=quiz_
```

Then create the SQLite file and run migrations:

```bash
php artisan key:generate
php artisan migrate
```

### 3.4 Run it

```bash
php artisan serve
```

Open <https://rik.hemsbase.com> — you should see the default Laravel welcome page. Now you
are ready to build.

---

## 4. Functional requirements (the "must-haves")

1. **A user interface** that lets the user play the quiz.
2. On each load, display **one random country** with **3 capital options** — exactly one
   is correct, the other two are random (and different) capitals.
3. The user must be able to **select one option** (selection is required).
4. On **submit**, show feedback:
   - If correct → a clear "Correct!" message.
   - If wrong → a "not quite right" message **and the correct capital**.
5. A **button/link to start a new question**.
6. The app behaves like a **loop** — the user can keep playing until they choose to stop.

---

## 5. Technical requirements

- **Backend:** Laravel. Frontend with **Blade** (use Bootstrap 5 via CDN for styling — no
  build step required).
- **Routes** (`routes/web.php`):
  - `GET  /`            → show a new question (name it `index`)
  - `POST /post-answer` → check the submitted answer (name it `post.answer`)
- **Controller:** one controller (e.g. `QuizController`) with a method for showing a
  question and a method for grading the answer.
- **External API + caching:** fetch the country list with Laravel's HTTP client and
  **cache it for ~1 hour** so the external API isn't called on every page load.
  Use `Cache::remember('country_response', 3600, fn () => ...)`.
- **Session:** when you show a question, store the correct capital in the session; when the
  answer is posted, compare against the session value. Handle the case where the session
  has expired gracefully.
- **Validation:** create a **Form Request** to validate the posted form
  (`country` and `capital` are both required strings).
- **Views:** a small layout split into header/footer plus an `index` and a `result` view.

### Suggested scaffolding commands

```bash
php artisan make:controller QuizController
php artisan make:request AnswerValidationRequest

mkdir -p resources/views/layout
touch resources/views/index.blade.php
touch resources/views/result.blade.php
touch resources/views/layout/header.blade.php
touch resources/views/layout/footer.blade.php
```

Target view structure:

```
resources/views
├── index.blade.php      # the question + options form
├── result.blade.php     # correct / incorrect feedback + "New Question"
└── layout
    ├── header.blade.php  # <head>, Bootstrap CDN, opening tags
    └── footer.blade.php  # footer + closing tags
```

Reference for Form Requests:
<https://laravel.com/docs/11.x/validation#creating-form-requests>

Expected validation rules:

```php
public function rules(): array
{
    return [
        'country' => 'required|string|min:1',
        'capital' => 'required|string|min:1',
    ];
}
```

---

## 6. Stretch goals (optional, if time allows)

Do these only after the core quiz works.

1. **Expose your own JSON API endpoint.** Add `GET /api/new-question` that returns a
   question as JSON (`{ success, data }`). Route it in `routes/api.php`.
   > In Laravel 11, if `routes/api.php` doesn't exist yet, run `php artisan install:api`
   > first — it wires up the API routes and installs Sanctum.
2. **Protect the endpoint with Sanctum** (`->middleware('auth:sanctum')`), so only a caller
   with a valid token can use it.
3. **Mint a token from the CLI.** Write an artisan console command (e.g.
   `php artisan app:obtain-token {email}`) that finds a user and prints a personal access
   token. Seed a test user first (`php artisan make:seeder`, then `php artisan db:seed`).
4. Test the endpoint with `curl -H "Authorization: Bearer <token>" https://rik.hemsbase.com/api/new-question`.

---

## 7. Definition of Done (acceptance criteria)

- [ ] A fresh clone runs after: `composer install`, set `.env`, `php artisan key:generate`,
      `php artisan migrate`, `php artisan serve`.
- [ ] Visiting `/` shows a random country and 3 capital options every time.
- [ ] Exactly one option is correct; the other two differ from it and from each other.
- [ ] Submitting without picking an option is blocked (validation).
- [ ] A correct answer shows a success message; a wrong answer shows the correct capital.
- [ ] "New Question" returns to a fresh question — the loop works.
- [ ] The external API is cached (verify: it still works with the network momentarily off,
      within the cache window).
- [ ] Code is committed to git with clear commit messages and a short README explaining how
      to run it.

---

## 8. What to submit

1. Create a new branch with your code
2. Write a short **README** with: some of techical details about your project.

---

## 9. Hints & gotchas

- Look at the API JSON **before** coding so you know the exact keys (`data`, `name`,
  `capital`).
- To build the 3 options: take the correct capital, then pick 2 more random capitals from
  the list, make sure they're different, then `shuffle()` so the correct one isn't always
  first.
- Store the correct answer in the session **when you render the question**, not when you
  grade it — the POST request is a separate request.
- Don't forget `@csrf` inside your `<form>`, or the POST will fail with a 419 error.
- If the page errors on cache/session, confirm you ran `php artisan migrate` (the
  `cache`, `sessions`, and `jobs` tables live in the database).

Good luck — build the core quiz first, then reach for the stretch goals. 🎯
