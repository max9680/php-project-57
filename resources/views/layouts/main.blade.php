<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="csrf-param" content="_token" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>Менеджер задач</title>

</head>

<body>
<header class="fixed w-full">
    <nav class="bg-white border-gray-200 py-2.5 dark:bg-gray-900 shadow-md">
        <div class="flex flex-wrap items-center justify-between max-w-screen-xl px-4 mx-auto">
            <a href="{{ route('home') }}" class="flex items-center">
                <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">{{ __('strings.task manager') }}</span></a>
            @auth
                <div class="flex items-center lg:order-2">
                    <a data-method="post" href="{{ route('logout') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">{{ __('strings.log out') }}</a>
                </div>
            @else
                <div class="flex items-center lg:order-2">
                <a href="{{ route('login') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">{{ __('strings.log in') }}</a>
                <a href="{{ route('register') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2">{{ __('strings.registration') }}</a>
            </div>
            @endauth

            <div class="items-center justify-between hidden w-full lg:flex lg:w-auto lg:order-1">
                <ul class="flex flex-col mt-4 font-medium lg:flex-row lg:space-x-8 lg:mt-0">
                    <li><a href="{{ route('tasks.index') }}" class="block py-2 pl-3 pr-4 text-gray-700 hover:text-blue-700 lg:p-0">{{ __('strings.tasks') }}</a></li>
                    <li><a href="{{ route('task_statuses.index') }}" class="block py-2 pl-3 pr-4 text-gray-700 hover:text-blue-700 lg:p-0">{{ __('strings.statuses') }}</a></li>
                    <li><a href="{{ route('labels.index') }}" class="block py-2 pl-3 pr-4 text-gray-700 hover:text-blue-700 lg:p-0">{{ __('strings.labels') }}</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<section class="bg-white dark:bg-gray-900">
    <div class="grid max-w-screen-xl px-4 pt-20 pb-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12 lg:pt-28">


        @if (flash()->message)
            @if (flash()->class === 'success')
            <div class="mb-4 rounded-lg bg-success-100 px-6 py-5 text-base text-success-700">
                {{ flash()->message }}
            </div>
            @endif

            @if (flash()->class === 'failure')
                <div class="mb-4 rounded-lg bg-danger-100 px-6 py-5 text-base text-danger-700">
                    {{ flash()->message }}
                </div>
            @endif
        @endif

    @yield('content')

    </div>
</section>

</body>
</html>
