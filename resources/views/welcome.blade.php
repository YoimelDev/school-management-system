<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            Bienvenido
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">School Management System</h1>
                    <p class="mb-6 text-gray-600">
                        Welcome to our School Management System. Here you can explore our academies and courses, and
                        register students.
                    </p>

                    <livewire:home.academic-offer-component />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
