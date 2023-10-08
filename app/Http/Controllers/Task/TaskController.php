<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\UpdateRequest;
use App\Http\Requests\Task\StoreRequest;
use App\Models\Label;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use App\Models\LabelTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::all();

        return view('task.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $taskStatuses = TaskStatus::select('id', 'name')->get()->pluck('name', 'id');
        $users = User::select('id', 'name')->get()->pluck('name', 'id');
        $labels = Label::all()->pluck('name', 'id');

        return view('task.create', compact('taskStatuses', 'users', 'labels'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request,)
    {
        $data = $request->validated();
        $data['created_by_id'] = auth()->user()->id;

        $labels = $data['labels'];
        unset($data['labels']);
        $labels = array_filter($labels);

        $task = Task::create($data);

        $task->labels()->attach($labels);

        flash(__('messages.task.created'), 'success');

        return redirect()->route('tasks.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        $labels = $task->labels;

        return view('task.show', compact('task', 'labels'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        $taskStatuses = TaskStatus::all();
        $users = User::all()->pluck('name', 'id');
        $taskLabels = $task->labels;
        $labels = Label::all()->pluck('name', 'id');

        return view('task.edit', compact('task', 'taskStatuses', 'users', 'labels', 'taskLabels'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(updateRequest $request, Task $task)
    {
        $data = $request->validated();

        $labels = $data['labels'];
        unset($data['labels']);
        $labels = array_filter($labels);

        $task->update($data);

        $task->labels()->sync($labels);

        flash(__('messages.task.updated'), 'success');

        return redirect()->route('tasks.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        flash(__('messages.task.deleted'), 'success');

        return redirect()->route('tasks.index');
    }
}
