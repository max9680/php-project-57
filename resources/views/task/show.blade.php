@extends('layouts.main')

@section('content')

    <div class="grid col-span-full">
        <h2 class="mb-5 text-3xl">Просмотр задачи: {{ $task->name }} <a href="{{ route('tasks.edit', $task->id) }}">⚙</a></h2>

        <p><span class="font-black">Имя:</span> {{ $task->name }}</p>
        <p><span class="font-black">Статус:</span> {{ $task->status->name }}</p>
        <p><span class="font-black">Описание:</span> {{ $task->description }}</p>
        <p><span class="font-black">Метки:</span></p>

    </div>

@endsection
