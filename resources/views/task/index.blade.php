@extends('layouts.main')

@section('content')

    <div class="grid col-span-full">
        <h1 class="mb-5 max-w-2xl text-4xl md:text-4xl xl:text-5xl">Задачи</h1>

        <div class="ml-auto">
            <a href="{{ route('tasks.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2">
            Создать задачу
            </a>
        </div>

        <table class="mt-4">
            <thead class="border-b-2 border-solid border-black text-left">
            <tr>
                <th>ID</th>
                <th>Статус</th>
                <th>Имя</th>
                <th>Автор</th>
                <th>Исполнитель</th>
                <th>Дата создания</th>
            </tr>
            </thead>
            <tbody>

            @foreach($tasks as $task)
                <tr class="border-b border-dashed text-left">
                    <td>{{ $task->id }}</td>
                    <td>{{ $task->status->name }}</td>
                    <td><a class="text-blue-600 hover:text-blue-900" href="{{ route('tasks.show', $task->id) }}">{{ $task->name }}</a></td>
                    <td>{{ $task->created_by_user->name }}</td>
                    @if ($task->assigned_to_user == null)
                        <td></td>
                    @else
                        <td>{{ $task->assigned_to_user->name }}</td>
                    @endif
                    <td>{{ $task->created_at->format('d.m.Y') }}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>

@endsection
