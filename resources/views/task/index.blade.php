@extends('layouts.main')

@section('content')

    <div class="grid col-span-full">
        <h1 class="mb-5 max-w-2xl text-4xl md:text-4xl xl:text-5xl">{{ __('strings.tasks') }}</h1>

        <div class="w-full flex items-center">
            <div>
                {!! Form::open(['route' => ['tasks.index'], 'method' => 'get']) !!}
                <div class="flex">

                    <div>
                        @if (!isset($activeFilter['status_id']))
                            {{ Form::select('filter[status_id]', $taskStatuses, null, ['class' => 'rounded border border-gray-300 p-2 bg-white', 'placeholder' => __('strings.status')]) }}
                        @else
                            {{ Form::select('filter[status_id]', $taskStatuses, $activeFilter['status_id'], ['class' => 'rounded border border-gray-300 p-2 bg-white', 'placeholder' => __('strings.status')]) }}
                        @endif
                    </div>

                    <div>
                        @if (!isset($activeFilter['created_by_id']))
                            {{ Form::select('filter[created_by_id]', $users, null, ['class' => 'ml-2 rounded border border-gray-300 p-2 bg-white', 'placeholder' => __('strings.author')]) }}
                        @else
                            {{ Form::select('filter[created_by_id]', $users, $activeFilter['created_by_id'], ['class' => 'ml-2 rounded border border-gray-300 p-2 bg-white', 'placeholder' => __('strings.author')]) }}
                        @endif
                    </div>

                    <div>
                        @if (!isset($activeFilter['assigned_to_id']))
                            {{ Form::select('filter[assigned_to_id]', $users, null, ['class' => 'ml-2 rounded border border-gray-300 p-2 bg-white', 'placeholder' => __('strings.executor')]) }}
                        @else
                            {{ Form::select('filter[assigned_to_id]', $users, $activeFilter['assigned_to_id'], ['class' => 'ml-2 rounded border border-gray-300 p-2 bg-white', 'placeholder' => __('strings.executor')]) }}
                        @endif
                    </div>

                    <div>
                        <input class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2" type="submit" value={{ __('strings.apply') }}>
                    </div>

                </div>
                {!! Form::close() !!}
            </div>

        <div class="ml-auto">
            @auth
                <a href="{{ route('tasks.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2">
                    {{ __('strings.create task') }}
                </a>
                @endauth
        </div>
        </div>

        <table class="mt-4">
            <thead class="border-b-2 border-solid border-black text-left">
            <tr>
                <th>ID</th>
                <th>{{ __('strings.status') }}</th>
                <th>{{ __('strings.name') }}</th>
                <th>{{ __('strings.author') }}</th>
                <th>{{ __('strings.executor') }}</th>
                <th>{{ __('strings.data created') }}</th>
                @auth
                    <th>{{ __('strings.actions') }}</th>
                @endauth
            </tr>
            </thead>
            <tbody>

            @foreach($tasks as $task)
                <tr class="border-b border-dashed text-left">
                    <td>{{ $task->id }}</td>
                    <td>{{ $task->status->name }}</td>
                    <td><a class="text-blue-600 hover:text-blue-900" href="{{ route('tasks.show', $task->id) }}">{{ $task->name }}</a></td>
                    <td>{{ $task->createdByUser->name }}</td>
                    <td>{{ $task->assignedToUser->name ?? "" }}</td>
                    <td>{{ $task->created_at->format('d.m.Y') }}</td>
                    @auth
                        <td>
                            @can('delete', $task)
                                <a data-method="delete" data-confirm="{{ __('strings.are you sure') }}" class="text-red-600 hover:text-red-900" href="{{ route('tasks.destroy', $task->id) }}">{{ __('strings.delete') }}</a>
                            @endcan
                            <a class="text-blue-600 hover:text-blue-900" href="{{ route('tasks.edit', $task) }}">{{ __('strings.edit') }}</a>
                        </td>
                    @endauth
                </tr>
            @endforeach

            </tbody>
        </table>

        {{ $tasks->links() }}
    </div>

@endsection
