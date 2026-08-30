<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Jadwal Rapat')) - Meeting Digital Service</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-surface text-on-surface font-sans antialiased">
    @auth
        @if (!request()->routeIs('home'))
            <div class="flex flex-col min-h-screen">
                <header class="w-full top-0 sticky z-50 bg-surface border-b border-outline-variant">
                    <div class="flex justify-between items-center h-16 px-6 max-w-[1440px] mx-auto">
                        <div class="flex items-center gap-4">
                            <span class="text-headline-md font-bold text-primary">Meeting Digital Service</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <button class="p-2 hover:bg-surface-container-low rounded-full transition-colors relative">
                                <span class="material-symbols-outlined text-on-surface-variant">notifications</span>
                                <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full"></span>
                            </button>
                            <div
                                class="flex items-center gap-2 cursor-pointer hover:bg-surface-container-low p-1 rounded-lg transition-colors">
                                <div class="text-right hidden sm:block">
                                    <p class="text-label-md text-on-surface leading-none">{{ auth()->user()->name }}</p>
                                    <p class="text-label-sm text-on-surface-variant">{{ auth()->user()->email }}</p>
                                </div>
                                <span class="material-symbols-outlined text-headline-md text-primary"
                                    style="font-variation-settings: 'FILL' 1;">account_circle</span>
                            </div>
                        </div>
                    </div>
                </header>
                <div class="flex flex-1 max-w-[1440px] mx-auto w-full">
                    <aside
                        class="hidden md:flex flex-col w-64 border-r border-outline-variant bg-surface-container-lowest p-4 gap-2 shrink-0">
                        <p class="text-label-sm text-outline font-bold uppercase tracking-widest px-2 mb-2">Main Menu</p>
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.dashboard') ? 'text-primary border-b-2 border-primary font-bold bg-surface-container-low' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
                            <span class="material-symbols-outlined">dashboard</span>
                            <span class="text-label-md">Beranda</span>
                        </a>
                        <a href="{{ route('admin.meetings') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.meetings*') ? 'text-primary border-b-2 border-primary font-bold bg-surface-container-low' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
                            <span class="material-symbols-outlined">event_note</span>
                            <span class="text-label-md">Rapat</span>
                        </a>
                        <a href="{{ route('admin.reports') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.reports') ? 'text-primary border-b-2 border-primary font-bold bg-surface-container-low' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
                            <span class="material-symbols-outlined">analytics</span>
                            <span class="text-label-md">Laporan</span>
                        </a>
                        <a href="{{ route('admin.uploaded-files') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.uploaded-files*') ? 'text-primary border-b-2 border-primary font-bold bg-surface-container-low' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
                            <span class="material-symbols-outlined">upload_file</span>
                            <span class="text-label-md">Upload Notulensi</span>
                        </a>
                        {{-- <div x-data="{ open: {{ request()->routeIs('admin.notulens*') || request()->routeIs('admin.kepalas*') || request()->routeIs('admin.notulensis*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all w-full text-left {{ request()->routeIs('admin.notulens*') || request()->routeIs('admin.kepalas*') || request()->routeIs('admin.notulensis*') ? 'text-primary border-b-2 border-primary font-bold bg-surface-container-low' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
                                <span class="material-symbols-outlined">note</span>
                                <span class="text-label-md flex-1">Notulensi</span>
                                <span class="material-symbols-outlined transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                            </button>
                            <div x-show="open" x-transition class="ml-4 mt-1 flex flex-col gap-1">
                                <a href="{{ route('admin.notulens') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all {{ request()->routeIs('admin.notulens*') ? 'text-primary bg-surface-container-low font-bold' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
                                    <span class="material-symbols-outlined text-label-md">person</span>
                                    <span class="text-label-sm">Notulen</span>
                                </a>
                                <a href="{{ route('admin.kepalas') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all {{ request()->routeIs('admin.kepalas*') ? 'text-primary bg-surface-container-low font-bold' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
                                    <span class="material-symbols-outlined text-label-md">admin_panel_settings</span>
                                    <span class="text-label-sm">Kepala</span>
                                </a>
                                <a href="{{ route('admin.notulensis') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all {{ request()->routeIs('admin.notulensis*') ? 'text-primary bg-surface-container-low font-bold' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
                                    <span class="material-symbols-outlined text-label-md">description</span>
                                    <span class="text-label-sm">Notulensi</span>
                                </a>
                            </div>
                        </div> --}}
                        @if (auth()->user()?->role === \App\Enums\UserRole::SuperAdmin)
                            <a href="{{ route('admin.users') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.users*') ? 'text-primary border-b-2 border-primary font-bold bg-surface-container-low' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
                                <span class="material-symbols-outlined">group</span>
                                <span class="text-label-md">User</span>
                            </a>
                        @endif
                        <div class="pt-4 border-t border-outline-variant">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-all w-full">
                                    <span class="material-symbols-outlined">logout</span>
                                    <span class="text-label-md">Logout</span>
                                </button>
                            </form>
                        </div>
                        <div class="flex-1"></div>
                    </aside>
                    <main class="flex-1 p-4 md:p-8 bg-background overflow-y-auto">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        @else
            {{ $slot }}
        @endif
    @else
        {{ $slot }}
    @endauth
    @livewireScripts
</body>

</html>
