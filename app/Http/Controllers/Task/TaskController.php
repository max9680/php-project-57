<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\UpdateRequest;
use App\Http\Requests\Task\StoreRequest;
use App\Models\Label;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Spatie\QueryBuilder\QueryBuilder;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        \Log::debug('Test debug message');

        $users = User::all()->pluck('name', 'id');
        $taskStatuses = TaskStatus::all()->pluck('name', 'id');

        $tasks = QueryBuilder::for(Task::class)
            ->allowedFilters('status_id', 'created_by_id', 'assigned_to_id')
            ->paginate(15)
            ->appends(request()->query());

        return view('task.index', [
            'tasks' => $tasks,
            'users' => $users,
            'taskStatuses' => $taskStatuses,
            'activeFilter' => request()->get('filter') ?? [
                    'status_id' => '',
                    'assigned_to_id' => '',
                    'created_by_id' => ''
                ]
            ]);
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
