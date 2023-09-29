@extends('layouts.main')

@section('content')

    <div class="grid col-span-full">
        <h1 class="mb-5 max-w-2xl text-4xl md:text-4xl xl:text-5xl">Статусы</h1>

        @auth
        <div>
            <a href="{{ route('task_statuses.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Создать статус
            </a>
        </div>
        @endauth

        <table class="mt-4">
            <thead class="border-b-2 border-solid border-black text-left">
            <tr>
                <th>ID</th>
                <th>Имя</th>
                <th>Дата создания</th>
                @auth
                    <th>Действия</th>
                @endauth
            </tr>
            </thead>
            <tbody>

            @foreach($taskStatuses as $taskStatus)
                <tr class="border-b border-dashed text-left">
                    <td>{{ $taskStatus->id }}</td>
                    <td>{{ $taskStatus->name }}</td>
                    <td>{{ $taskStatus->created_at->format('d.m.Y') }}</td>
                    @auth
                        <td>
                            <a data-method="delete" data-confirm="Вы уверены?" class="text-red-600 hover:text-red-900" href="{{ route('task_statuses.destroy', $taskStatus->id) }}">Удалить</a>
                            <a class="text-blue-600 hover:text-blue-900" href="{{ route('task_statuses.edit', $taskStatus->id) }}">Изменить</a>
                        </td>
                    @endauth
                </tr>
            @endforeach

            </tbody>
        </table>

    </div>

@endsection
