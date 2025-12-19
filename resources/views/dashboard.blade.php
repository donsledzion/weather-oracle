<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ __('app.your_monitoring_requests') }}</h1>
                <p class="text-gray-600 mt-2">{{ __('app.managing_requests_for') }}: <strong>{{ auth()->user()->email }}</strong></p>
            </div>
            <a href="{{ route('notifications.show', auth()->user()->notificationPreferences()->token) }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition shadow-sm">
                <span class="text-xl">🔔</span>
                <span>{{ __('app.notification_preferences') }}</span>
            </a>
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
