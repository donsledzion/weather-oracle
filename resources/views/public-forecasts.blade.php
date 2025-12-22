<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ __('app.public_forecasts_title') }}</h1>
            <p class="text-xl text-gray-600">{{ __('app.public_forecasts_description') }}</p>
        </div>

        @foreach($locations as $location)
                @php
                    $requests = $publicRequests->get($location->name, collect());
                    $activeRequests = $requests->whereIn('status', ['active', 'pending_verification']);
                    $completedRequests = $requests->where('status', 'completed')->take(5);
                @endphp

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold">{{ $location->name }}</h3>
                        <span class="text-sm text-gray-500">
                            {{ __('app.active') }}: {{ $activeRequests->count() }} / {{ __('app.completed') }}: {{ $completedRequests->count() }}
                        </span>
                    </div>

                    @if($activeRequests->count() > 0)
                        <div class="mb-6">
                            <h4 class="font-semibold text-lg mb-3">{{ __('app.active_monitors') }}</h4>
                            <div class="space-y-3">
                                @foreach($activeRequests as $request)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                        {{ __('app.status_' . $request->status) }}
                                                    </span>
                                                    <span class="text-sm text-gray-600">
                                                        {{ __('app.target_date') }}: {{ $request->target_date->format('Y-m-d') }}
                                                    </span>
                                                </div>

                                                @php
                                                    $daysElapsed = $request->created_at->diffInDays(now());
                                                    $totalDays = $request->created_at->diffInDays($request->target_date);
                                                    $progress = $totalDays > 0 ? min(100, ($daysElapsed / $totalDays) * 100) : 0;
                                                @endphp

                                                <div class="mt-2">
                                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                                        <span>{{ __('app.progress') }}</span>
                                                        <span>{{ round($progress) }}%</span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        {{ __('app.created') }}: {{ $request->created_at->format('Y-m-d H:i') }}
                                                        ({{ $daysElapsed }} {{ __('app.days_ago') }})
                                                    </p>
                                                </div>

                                                @if($request->forecastSnapshots->count() > 0)
                                                    <p class="text-xs text-gray-500 mt-2">
                                                        {{ __('app.forecast_snapshots') }}: {{ $request->forecastSnapshots->count() }}
                                                    </p>
                                                @endif
                                            </div>

                                            <div class="ml-4">
                                                <a href="{{ route('requests.show', $request->id) }}"
                                                   class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-md hover:bg-blue-600 transition">
                                                    {{ __('app.view_details') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($completedRequests->count() > 0)
                        <div>
                            <h4 class="font-semibold text-lg mb-3">{{ __('app.completed_monitors') }} ({{ __('app.latest_5') }})</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($completedRequests as $request)
                                    <div class="border border-gray-200 rounded-lg p-3 hover:border-blue-300 transition">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ __('app.status_completed') }}
                                            </span>
                                        </div>
                                        <p class="text-sm font-medium mb-1">{{ __('app.target_date') }}: {{ $request->target_date->format('Y-m-d') }}</p>
                                        <p class="text-xs text-gray-500 mb-2">
                                            {{ __('app.forecast_snapshots') }}: {{ $request->forecastSnapshots->count() }}
                                        </p>
                                        <a href="{{ route('requests.show', $request->id) }}"
                                           class="text-sm text-blue-500 hover:text-blue-700">
                                            {{ __('app.view_summary') }} &rarr;
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($activeRequests->count() === 0 && $completedRequests->count() === 0)
                        <p class="text-gray-500 text-center py-8">{{ __('app.no_public_monitors') }}</p>
                    @endif
            </div>
        @endforeach
    </div>
</x-layouts.app>
