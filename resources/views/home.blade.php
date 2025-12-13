<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-4">Monitor Weather Forecasts</h2>

                @livewire('monitoring-form')
            </div>
        </div>

        {{-- Example Alpine.js component --}}
        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6" x-data="{ open: false }">
            <button @click="open = !open" class="bg-blue-500 text-white px-4 py-2 rounded">
                Toggle Info
            </button>

            <div x-show="open" x-transition class="mt-4 p-4 bg-blue-50 rounded">
                <p>This is an example of Alpine.js working!</p>
            </div>
        </div>
    </div>
</x-layouts.app>
