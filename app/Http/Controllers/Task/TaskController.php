<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\UpdateRequest;
use App\Http\Requests\Task\StoreRequest;
use App\Models\Label;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TaskController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Task::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all()->pluck('name', 'id');
        $taskStatuses = TaskStatus::select('name', 'id')->pluck('name', 'id');

        $tasks = QueryBuilder::for(Task::class)
            ->allowedFilters([
                'name',
                AllowedFilter::exact('status_id'),
                AllowedFilter::exact('created_by_id'),
                AllowedFilter::exact('assigned_to_id'),
            ])
            ->orderBy('id')
            ->paginate(10);

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
        $taskStatuses = TaskStatus::select('name', 'id')->pluck('name', 'id');
        $users = User::select('name', 'id')->pluck('name', 'id');
        $labels = Label::select('name', 'id')->pluck('name', 'id');

        return view('task.create', compact('taskStatuses', 'users', 'labels'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Task\StoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $request->validated();

        $data = $request->except('labels');
        $data['created_by_id'] = optional(auth()->user())->id;

        $labels = $request->input('labels');

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
        $users = User::select('name', 'id')->pluck('name', 'id');
        $taskLabels = $task->labels;
        $labels = Label::select('name', 'id')->pluck('name', 'id');

        return view('task.edit', compact('task', 'taskStatuses', 'users', 'labels', 'taskLabels'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Task\UpdateRequest  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(updateRequest $request, Task $task)
    {
        $request->validated();

        $data = $request->except('labels');

        $labels = $request->input('labels');

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
        $task->labels()->detach();
        $task->delete();

        flash(__('messages.task.deleted'), 'success');

        return redirect()->route('tasks.index');
    }
}
