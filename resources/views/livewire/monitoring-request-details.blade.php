<div>
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4">{{ $request->location }}</h2>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <p class="text-sm text-gray-600">Target Date</p>
                <p class="font-semibold">{{ $request->target_date->format('Y-m-d') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Status</p>
                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                    {{ $request->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($request->status) }}
                </span>
            </div>
            @if($request->email)
            <div>
                <p class="text-sm text-gray-600">Email</p>
                <p class="font-semibold">{{ $request->email }}</p>
            </div>
            @endif
            <div>
                <p class="text-sm text-gray-600">Created</p>
                <p class="font-semibold">{{ $request->created_at->format('Y-m-d H:i') }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold mb-4">Forecast Snapshots ({{ $request->forecastSnapshots->count() }})</h3>

        @if($request->forecastSnapshots->isEmpty())
            <p class="text-gray-500">No forecast snapshots yet.</p>
        @else
            <div class="space-y-4">
                @foreach($request->forecastSnapshots as $snapshot)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="font-semibold">{{ $snapshot->weatherProvider->name }}</p>
                                <p class="text-sm text-gray-500">Fetched: {{ $snapshot->fetched_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs text-gray-600">Temperature</p>
                                <p class="font-semibold">{{ round($snapshot->forecast_data['temperature_avg']) }}°C</p>
                                <p class="text-xs text-gray-500">
                                    Min: {{ round($snapshot->forecast_data['temperature_min']) }}°C /
                                    Max: {{ round($snapshot->forecast_data['temperature_max']) }}°C
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Conditions</p>
                                <p class="font-semibold">{{ $snapshot->forecast_data['conditions'] }}</p>
                                <p class="text-xs text-gray-500">{{ $snapshot->forecast_data['description'] }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Precipitation</p>
                                <p class="font-semibold">{{ round($snapshot->forecast_data['precipitation'] * 100) }}%</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
