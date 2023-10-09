@extends('layouts.main')

@section('content')

    <div class="grid col-span-full">
        <h1 class="mb-5 max-w-2xl text-4xl md:text-4xl xl:text-5xl">Задачи</h1>

        <div class="w-full flex items-center">
            <div>
                {!! Form::open(['route' => ['tasks.index'], 'method' => 'get']) !!}
                <div class="flex">

                    <div>
                        @if ($activeFilter['status_id'] == null)
                            {{ Form::select('filter[status_id]', $taskStatuses, null, ['class' => 'rounded border border-gray-300 p-2 bg-white', 'placeholder' => 'Статус']) }}
                        @else
                            {{ Form::select('filter[status_id]', $taskStatuses, $activeFilter['status_id'], ['class' => 'rounded border border-gray-300 p-2 bg-white', 'placeholder' => 'Статус']) }}
                        @endif
                    </div>

                    <div>
                        @if ($activeFilter['created_by_id'] == null)
                            {{ Form::select('filter[created_by_id]', $users, null, ['class' => 'ml-2 rounded border border-gray-300 p-2 bg-white', 'placeholder' => 'Автор']) }}
                        @else
                            {{ Form::select('filter[created_by_id]', $users, $activeFilter['created_by_id'], ['class' => 'ml-2 rounded border border-gray-300 p-2 bg-white', 'placeholder' => 'Автор']) }}
                        @endif
                    </div>

                    <div>
                        @if ($activeFilter['assigned_to_id'] == null)
                            {{ Form::select('filter[assigned_to_id]', $users, null, ['class' => 'ml-2 rounded border border-gray-300 p-2 bg-white', 'placeholder' => 'Исполнитель']) }}
                        @else
                            {{ Form::select('filter[assigned_to_id]', $users, $activeFilter['assigned_to_id'], ['class' => 'ml-2 rounded border border-gray-300 p-2 bg-white', 'placeholder' => 'Исполнитель']) }}
                        @endif
                    </div>

                    <div>
                        <input class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2" type="submit" value="Применить">
                    </div>

                </div>
                {!! Form::close() !!}
            </div>

        <div class="ml-auto">
            @auth
                <a href="{{ route('tasks.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2">
                Создать задачу
                </a>
                @endauth
        </div>
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
                @auth
                    <th>Действия</th>
                @endauth
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
                    @auth
                        <td>
                            @can('delete', $task)
                                <a data-method="delete" data-confirm="Вы уверены?" class="text-red-600 hover:text-red-900" href="{{ route('tasks.destroy', $task->id) }}">Удалить</a>
                            @endcan
                            <a class="text-blue-600 hover:text-blue-900" href="{{ route('tasks.edit', $task) }}">Изменить</a>
                        </td>
                    @endauth
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>

@endsection
