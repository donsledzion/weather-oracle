<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-4">Monitor Weather Forecasts</h2>

                @livewire('monitoring-form')
            </div>
        </div>

        {{-- Monitoring Requests List --}}
        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            @livewire('monitoring-requests-list')
        </div>
    </div>
</x-layouts.app>
