<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ __('app.welcome_title') }}</h1>
            <p class="text-xl text-gray-600 mb-8">{{ __('app.welcome_subtitle') }}</p>
        </div>

        @auth
            {{-- Zalogowany użytkownik: formularz + link do dashboardu --}}
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h2 class="text-xl font-bold mb-4">{{ __('app.monitor_forecasts') }}</h2>
                @livewire('monitoring-form')
            </div>

            <div class="text-center mb-12">
                <a href="{{ route('dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition">
                    {{ __('app.go_to_dashboard') }}
                </a>
            </div>
        @else
            {{-- Niezalogowany: formularz (z emailem wymaganym) --}}
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h2 class="text-xl font-bold mb-4">{{ __('app.monitor_forecasts') }}</h2>
                <p class="text-gray-600 text-sm mb-4">{{ __('app.no_account_required') }}</p>
                @livewire('monitoring-form')
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-12 text-center">
                <p class="text-gray-700 mb-4">{{ __('app.already_have_account') }}</p>
                <div class="flex gap-4 justify-center">
                    <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition">
                        {{ __('app.login') }}
                    </a>
                    <a href="{{ route('register') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-6 rounded-lg transition">
                        {{ __('app.register') }}
                    </a>
                </div>
            </div>
        @endauth

        <div class="grid md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-3xl mb-4">&#128205;</div>
                <h3 class="text-lg font-bold mb-2">{{ __('app.choose_location') }}</h3>
                <p class="text-gray-600">{{ __('app.choose_location_desc') }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-3xl mb-4">&#128257;</div>
                <h3 class="text-lg font-bold mb-2">{{ __('app.compare_sources') }}</h3>
                <p class="text-gray-600">{{ __('app.compare_sources_desc') }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-3xl mb-4">&#128231;</div>
                <h3 class="text-lg font-bold mb-2">{{ __('app.get_notifications') }}</h3>
                <p class="text-gray-600">{{ __('app.get_notifications_desc') }}</p>
            </div>
        </div>

        <div class="bg-blue-50 p-8 rounded-lg text-center">
            <h2 class="text-2xl font-bold mb-4">{{ __('app.why_weather_oracle') }}</h2>
            <div class="grid md:grid-cols-2 gap-4 text-left max-w-2xl mx-auto">
                <div class="flex items-start gap-2">
                    <span class="text-green-600 font-bold">&#10003;</span>
                    <span>{{ __('app.why_independent_sources') }}</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-green-600 font-bold">&#10003;</span>
                    <span>{{ __('app.why_monitor_changes') }}</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-green-600 font-bold">&#10003;</span>
                    <span>{{ __('app.why_notifications') }}</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-green-600 font-bold">&#10003;</span>
                    <span>{{ __('app.why_free_limits') }}</span>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
