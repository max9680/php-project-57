@extends('layouts.main')

@section('content')

    <div class="grid col-span-full">
        <h1 class="mb-5 max-w-2xl text-4xl md:text-4xl xl:text-5xl">Статусы</h1>
        <div>
        </div>

        <table class="mt-4">
            <thead class="border-b-2 border-solid border-black text-left">
            <tr>
                <th>ID</th>
                <th>Имя</th>
                <th>Дата создания</th>
            </tr>
            </thead>
            <tbody>
            <tr class="border-b border-dashed text-left">
                <td>1</td>
                <td>новая</td>
                <td>15.09.2023</td>
                <td>
                </td>
            </tr>
            </tbody></table>

    </div>

@endsection
