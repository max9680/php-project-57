<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Label\LabelController;
use App\Http\Controllers\Task\TaskController;
use App\Http\Controllers\TaskStatus\TaskStatusController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

//Route::resource('task_statuses', TaskStatusController::class);

Route::get('/task_statuses', [TaskStatusController::class, 'index'])->name('task_statuses.index');
Route::get('/task_statuses/create', [TaskStatusController::class, 'create'])
    ->name('task_statuses.create')
    ->middleware('auth');
Route::post('/task_statuses', [TaskStatusController::class, 'store'])->name('task_statuses.store');
Route::get('/task_statuses/{task_status}/edit', [TaskStatusController::class, 'edit'])
    ->name('task_statuses.edit')
    ->middleware('auth');
Route::patch('/task_statuses/{task_status}', [TaskStatusController::class, 'update'])
    ->name('task_statuses.update')
    ->middleware('auth');
Route::delete('/task_statuses/{task_status}', [TaskStatusController::class, 'destroy'])
    ->name('task_statuses.destroy')
    ->middleware('auth');

//Route::resource('task', TaskController::class);

Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create')
    ->middleware('auth');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store')
    ->middleware('auth');
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit')
    ->middleware('auth');
Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update')
    ->middleware('auth');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])
    ->name('tasks.destroy');


Route::post('/labels', [LabelController::class, 'store'])->name('labels.store')
    ->middleware('auth');
Route::get('/labels', [LabelController::class, 'index'])->name('labels.index');
Route::get('/labels/create', [LabelController::class, 'create'])->name('labels.create')
    ->middleware('auth');
Route::get('/labels/{label}/edit', [LabelController::class, 'edit'])->name('labels.edit')
    ->middleware('auth');
Route::delete('/labels/{label}', [LabelController::class, 'destroy'])->name('labels.destroy')
    ->middleware('auth');
Route::patch('/labels/{label}', [LabelController::class, 'update'])->name('labels.update')
    ->middleware('auth');

Route::get('/test', [TaskController::class, 'test'])->name('tasks.test');
