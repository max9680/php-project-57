@extends('layouts.main')

@section('content')

    <div class="grid col-span-full">
        <h1 class="mb-5 max-w-2xl text-4xl md:text-4xl xl:text-5xl">Метки</h1>

        <div>
            @auth
                <a href="{{ route('labels.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Создать метку
                </a>
            @endauth
        </div>

        <table class="mt-4">
            <thead class="border-b-2 border-solid border-black text-left">
            <tr>
                <th>ID</th>
                <th>Имя</th>
                <th>Описание</th>
                <th>Дата создания</th>
                @auth
                    <th>Действия</th>
                @endauth
            </tr>
            </thead>
            <tbody>

            @foreach($labels as $label)
                <tr class="border-b border-dashed text-left">
                    <td>{{ $label->id }}</td>
                    <td>{{ $label->name }}</td>
                    <td>{{ $label->description }}</td>
                    <td>{{ $label->created_at->format('d.m.Y') }}</td>
                    @auth
                        <td>
                            <a data-method="delete" data-confirm="Вы уверены?" class="text-red-600 hover:text-red-900" href="{{ route('labels.destroy', $label->id) }}">Удалить</a>

                            <a class="text-blue-600 hover:text-blue-900" href="{{ route('labels.edit', $label) }}">Изменить</a>
                        </td>
                    @endauth
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>

@endsection
