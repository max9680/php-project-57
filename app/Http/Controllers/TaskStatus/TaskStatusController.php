<?php

namespace App\Http\Controllers\TaskStatus;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskStatus\StoreRequest;
use App\Http\Requests\TaskStatus\UpdateRequest;
use App\Models\Task;
use App\Models\TaskStatus;
use Illuminate\Http\Request;

class TaskStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taskStatuses = TaskStatus::all();

        return view('taskStatus.index', compact('taskStatuses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('taskStatus.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        TaskStatus::create($data);

//        flash('Статус успешно создан')->success();
        flash(__('messages.status.created'), 'success');

        return redirect()->route('task_statuses.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TaskStatus $taskStatus)
    {
        return view('taskStatus.edit', compact('taskStatus'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, TaskStatus $taskStatus)
    {
        $data = $request->validated();

        $taskStatus->update($data);

        flash(__('messages.status.modified'), 'success');

        return redirect()->route('task_statuses.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaskStatus $taskStatus)
    {
        if ($taskStatus->tasks->toArray() == null) {
            $taskStatus->delete();

            flash(__('messages.status.deleted'), 'success');
        } else {
            flash(__('messages.status.deleted.error'), 'failure');
        };

        return redirect()->route('task_statuses.index');
    }
}
