@extends('layouts.main')

@section('content')

    <div class="grid col-span-full">
        <h1 class="mb-5 max-w-2xl text-4xl md:text-4xl xl:text-5xl">Изменение задачи</h1>
        <div>
            {!! Form::open(['route' => ['tasks.update', $task], 'method' => 'patch']) !!}
            <div class="mt-2">
                {!! Form::label("name", "Имя") !!}
            </div>
            <div class="mt-2">
                {!! Form::text('name', $task->name, ['class' => 'rounded border border-gray-300 w-1/3 p-2', 'value' => old('name')]) !!}
            </div>
            @error('name')
            <div class="text-rose-600">
                {{ $message }}
            </div>
            @enderror

            <div class="mt-2">
                {!! Form::label("description", "Описание") !!}
            </div>
            <div class="mt-2">
                {{ Form::textarea('description', $task->description, ['class' => 'rounded border border-gray-300 w-1/3 h-32 p-2', 'value' => old('description'), 'rows' => 10, 'cols' => 50]) }}

            </div>
            @error('description')
            <div class="text-rose-600">
                {{ $message }}
            </div>
            @enderror

            <div class="mt-2">
                {!! Form::label("status_id", "Статус") !!}
            </div>
            <div class="mt-2">
                {{ Form::select('status_id', $taskStatuses->pluck('name', 'id'), $task->status->id, ['class' => 'rounded border border-gray-300 w-1/3 p-2 bg-white', 'placeholder' => '----------']) }}

            </div>
            @error('status_id')
            <div class="text-rose-600">
                {{ $message }}
            </div>
            @enderror

            <div class="mt-2">
                {!! Form::label("assigned_to_id", "Исполнитель") !!}
            </div>
            <div class="mt-2">
                @if ($task->assigned_to_user == null)
                    {{ Form::select('assigned_to_id', $users, null, ['class' => 'rounded border border-gray-300 w-1/3 p-2 bg-white', 'placeholder' => '----------']) }}
                @else
                    {{ Form::select('assigned_to_id', $users, $task->assigned_to_user->id, ['class' => 'rounded border border-gray-300 w-1/3 p-2 bg-white', 'placeholder' => '----------']) }}
                @endif
            @error('assigned_to_id')
            <div class="text-rose-600">
                {{ $message }}
            </div>
            @enderror
            </div>

            <div class="mt-2">
                {!! Form::label("labels", "Метки") !!}
            </div>
            <div class="mt-2">
                {{ Form::select('labels[]', $labels, $taskLabels, ['class' => 'rounded border border-gray-300 w-1/3 p-2 bg-white', 'multiple' => true, 'placeholder' => '']) }}
            </div>

            <div class="mt-2">
                {!! Form::submit('Обновить', ['class' => 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>

@endsection
