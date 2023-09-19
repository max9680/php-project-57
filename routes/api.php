<?php

use App\Http\Controllers\Api\TaskStatus\TaskStatusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::resource('task_statuses', TaskStatusController::class)->only('index', 'store', 'update', 'destroy');
