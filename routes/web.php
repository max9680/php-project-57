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


Route::resource('task_statuses', TaskStatusController::class)->except([
    'index'
])->middleware('auth');

Route::resource('task_statuses', TaskStatusController::class)->only([
    'index'
]);


Route::resource('tasks', TaskController::class)->except([
    'index', 'show'
])->middleware('auth');

Route::resource('tasks', TaskController::class)->only([
    'index', 'show'
]);


Route::resource('labels', LabelController::class)->except([
    'index'
])->middleware('auth');

Route::resource('labels', LabelController::class)->only([
    'index'
]);

