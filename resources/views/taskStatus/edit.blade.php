@extends('layouts.main')

@section('content')

    <div class="grid col-span-full">
        <h1 class="mb-5 max-w-2xl text-4xl md:text-4xl xl:text-5xl">Изменение статуса</h1>
        <div>
            {!! Form::open(['route' => ['task_statuses.update', $taskStatus], 'method' => 'patch']) !!}
            <div>
                {!! Form::label("name", "Имя") !!}
            </div>
            <div class="mt-2">
                {!! Form::text('name', $taskStatus->name, ['class' => 'rounded border-gray-300 w-1/3', 'value' => old('name')]) !!}
            </div>
            @error('name')
            <div class="text-rose-600">
                {{ $message }}
            </div>
            @enderror
            <div class="mt-2">
                {!! Form::submit('Обновить', ['class' => 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>

@endsection
