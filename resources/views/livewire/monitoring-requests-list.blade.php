<div>
    <h3 class="text-xl font-bold mb-4">{{ __('app.your_monitoring_requests') }}</h3>

    @if($requests->isEmpty())
        <p class="text-gray-500">{{ __('app.no_requests_yet') }}</p>
    @else
        <div class="space-y-3">
            @foreach($requests as $request)
                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h4 class="font-semibold text-lg">{{ $request->location }}</h4>
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                                    {{ $request->status === 'active' ? 'bg-green-100 text-green-800' : ($request->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ __('app.status_' . $request->status) }}
                                </span>
                                <span class="text-sm text-gray-600">
                                    {{ __('app.target_date') }}: {{ $request->target_date->format('Y-m-d') }}
                                </span>
                            </div>

                            @if($request->status === 'active' || $request->status === 'pending_verification')
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
                            @else
                                <p class="text-xs text-gray-500">
                                    {{ __('app.created') }}: {{ $request->created_at->format('Y-m-d H:i') }}
                                </p>
                            @endif

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
    @endif
</div>
