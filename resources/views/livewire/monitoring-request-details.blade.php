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

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-xl font-bold mb-4">Temperature Trends</h3>

        @if($request->forecastSnapshots->count() > 1)
            <canvas id="temperatureChart" height="100"></canvas>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('temperatureChart').getContext('2d');
                    const snapshots = @json($request->forecastSnapshots->sortBy('fetched_at')->values());

                    const labels = snapshots.map(s => new Date(s.fetched_at).toLocaleString('pl-PL', {
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }));

                    const avgTemps = snapshots.map(s => s.forecast_data.temperature_avg);
                    const minTemps = snapshots.map(s => s.forecast_data.temperature_min);
                    const maxTemps = snapshots.map(s => s.forecast_data.temperature_max);

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Avg Temperature',
                                    data: avgTemps,
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.3
                                },
                                {
                                    label: 'Min Temperature',
                                    data: minTemps,
                                    borderColor: 'rgb(99, 102, 241)',
                                    borderDash: [5, 5],
                                    fill: false
                                },
                                {
                                    label: 'Max Temperature',
                                    data: maxTemps,
                                    borderColor: 'rgb(239, 68, 68)',
                                    borderDash: [5, 5],
                                    fill: false
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Temperature (°C)'
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
        @else
            <p class="text-gray-500">Not enough data for chart (need at least 2 snapshots)</p>
        @endif
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

                        <div class="grid grid-cols-3 gap-4 mb-3">
                            <div>
                                <p class="text-xs text-gray-600">Temperature</p>
                                <p class="font-semibold">{{ round($snapshot->forecast_data['temperature_avg']) }}°C</p>
                                <p class="text-xs text-gray-500">
                                    Min: {{ round($snapshot->forecast_data['temperature_min']) }}°C /
                                    Max: {{ round($snapshot->forecast_data['temperature_max']) }}°C
                                </p>
                                @if(isset($snapshot->forecast_data['feels_like']))
                                    <p class="text-xs text-gray-500">Feels like: {{ round($snapshot->forecast_data['feels_like']) }}°C</p>
                                @endif
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

                        <div class="grid grid-cols-4 gap-4 pt-3 border-t border-gray-100">
                            <div>
                                <p class="text-xs text-gray-600">Humidity</p>
                                <p class="font-semibold">{{ $snapshot->forecast_data['humidity'] ?? 'N/A' }}%</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Pressure</p>
                                <p class="font-semibold">{{ $snapshot->forecast_data['pressure'] ?? 'N/A' }} hPa</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Wind</p>
                                <p class="font-semibold">{{ round($snapshot->forecast_data['wind_speed'] ?? 0, 1) }} m/s</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Clouds</p>
                                <p class="font-semibold">{{ $snapshot->forecast_data['clouds'] ?? 'N/A' }}%</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
