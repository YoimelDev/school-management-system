<?php

use App\Livewire\Actions\Logout;

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};

?>

<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}" class="font-bold text-xl">
                        School Management
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <a 
                        href="{{ url('/') }}"
                        wire:navigate
                        class="inline-flex items-center px-1 pt-1 
                        {{ request()->is('/') ? 'border-b-2 border-indigo-400 dark:border-indigo-600' : 'border-b-2 border-transparent' }} 
                         text-sm font-medium leading-5 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out"
                    >
                        Inicio
                    </a>

                    @auth
                        <a 
                            href="{{ url('/admin/communications') }}"
                            wire:navigate
                            class="inline-flex items-center px-1 pt-1 {{ request()->is('admin/communications') ? 'border-b-2 border-indigo-400 dark:border-indigo-600' : 'border-b-2 border-transparent' }} text-sm font-medium leading-5 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                            Comunicados
                        </a>
                    @endauth
                </div>
            </div>

            @auth
                <div class="flex items-center gap-2">
                    <span class="text-gray-700 dark:text-gray-300">
                        {{ Auth::user()->name }}
                    </span>

                    <!-- Authentication -->
                    <flux:button
                        wire:click="logout"
                        icon:trailing="x-mark"
                        variant="ghost"
                    >
                        Cerrar sesión
                    </flux:button>
                </div>

            @else
                <div class="flex items-center gap-2">
                    <flux:button
                        wire:navigate
                        href="{{ url('/login') }}"
                    >
                        Iniciar sesión
                    </flux:button>

                    <flux:button
                        wire:navigate
                        href="{{ url('/register') }}"
                    >
                        Registrarse
                    </flux:button>
                </div>
            @endauth

            <!-- Mobile menu button -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ url('/') }}" class="block pl-3 pr-4 py-2 border-l-4 border-indigo-400 dark:border-indigo-600 text-base font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50 focus:outline-none focus:text-indigo-800 dark:focus:text-indigo-200 focus:bg-indigo-100 dark:focus:bg-indigo-900 focus:border-indigo-700 dark:focus:border-indigo-300 transition duration-150 ease-in-out">
                Inicio
            </a>
        </div>
    </div>
</nav>
