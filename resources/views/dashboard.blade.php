<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">{{ __('app.your_monitoring_requests') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('app.managing_requests_for') }}: <strong>{{ auth()->user()->email }}</strong></p>
        </div>

        <!-- Monitoring Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">{{ __('app.monitor_forecasts') }}</h2>
            @livewire('monitoring-form')
        </div>

        <!-- User Requests List -->
        <div>
            <h2 class="text-xl font-bold mb-4">{{ __('app.your_monitoring_requests') }}</h2>
            @livewire('user-requests-list')
        </div>
    </div>
</x-layouts.app>
