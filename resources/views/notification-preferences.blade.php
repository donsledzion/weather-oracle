<x-layouts.app>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ __('app.notification_preferences') }}</h1>
            <p class="text-gray-600 mb-6">
                {{ __('app.managing_notifications_for') }}:
                <strong>{{ $preferences->email ?? auth()->user()?->email }}</strong>
            </p>

            @livewire('notification-toggles', ['token' => $token])
        </div>
    </div>
</x-layouts.app>
