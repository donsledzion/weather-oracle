<div>
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4">{{ $request->location }}</h2>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <p class="text-sm text-gray-600">{{ __('app.target_date') }}</p>
                <p class="font-semibold">{{ $request->target_date->format('Y-m-d') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">{{ __('app.status') }}</p>
                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                    {{ $request->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ __('app.' . $request->status) }}
                </span>
            </div>
            @if($request->email)
            <div>
                <p class="text-sm text-gray-600">{{ __('app.email') }}</p>
                <p class="font-semibold">{{ $request->email }}</p>
            </div>
            @endif
            <div>
                <p class="text-sm text-gray-600">{{ __('app.created') }}</p>
                <p class="font-semibold">{{ $request->created_at->format('Y-m-d H:i') }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-xl font-bold mb-4">{{ __('app.temperature_trends') }}</h3>

        @if($request->forecastSnapshots->count() > 0)
            <div class="relative w-full" style="height: 300px;">
                <canvas id="temperatureChart"></canvas>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('temperatureChart').getContext('2d');
                    const snapshots = @json($request->forecastSnapshots->sortBy('fetched_at')->values());

                    // Group snapshots by provider
                    const providerData = {};
                    snapshots.forEach(s => {
                        const providerName = s.weather_provider.name;
                        if (!providerData[providerName]) {
                            providerData[providerName] = [];
                        }
                        providerData[providerName].push(s);
                    });

                    // Get unique timestamps for labels (rounded to nearest minute to group simultaneous fetches)
                    const uniqueTimes = {};
                    snapshots.forEach(s => {
                        const date = new Date(s.fetched_at);
                        const rounded = new Date(date.getFullYear(), date.getMonth(), date.getDate(), date.getHours(), date.getMinutes()).toISOString();
                        uniqueTimes[rounded] = true;
                    });
                    const timestamps = Object.keys(uniqueTimes).sort();
                    const labels = timestamps.map(t => new Date(t).toLocaleString('{{ app()->getLocale() }}', {
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }));

                    // Calculate adaptive maxTicksLimit based on data range
                    const dataCount = timestamps.length;
                    let maxTicks;
                    if (dataCount <= 10) {
                        maxTicks = dataCount; // Show all labels for small datasets
                    } else if (dataCount <= 50) {
                        maxTicks = Math.ceil(dataCount / 3); // Show ~1/3 of labels
                    } else if (dataCount <= 100) {
                        maxTicks = Math.ceil(dataCount / 5); // Show ~1/5 of labels
                    } else {
                        maxTicks = Math.ceil(dataCount / 10); // Show ~1/10 of labels
                    }

                    // Provider colors
                    const colors = {
                        'OpenWeather': { border: 'rgb(59, 130, 246)', bg: 'rgba(59, 130, 246, 0.1)' },
                        'Open-Meteo': { border: 'rgb(16, 185, 129)', bg: 'rgba(16, 185, 129, 0.1)' },
                        'Visual Crossing': { border: 'rgb(249, 115, 22)', bg: 'rgba(249, 115, 22, 0.1)' }
                    };

                    // Calculate adaptive point settings based on data density
                    let pointRadius, pointHoverRadius;
                    if (dataCount <= 20) {
                        pointRadius = 4;
                        pointHoverRadius = 6;
                    } else if (dataCount <= 50) {
                        pointRadius = 2;
                        pointHoverRadius = 5;
                    } else if (dataCount <= 100) {
                        pointRadius = 1;
                        pointHoverRadius = 4;
                    } else {
                        pointRadius = 0; // Hide points for very dense data
                        pointHoverRadius = 4;
                    }

                    // Create datasets for each provider
                    const datasets = [];
                    Object.keys(providerData).forEach(providerName => {
                        const providerSnapshots = providerData[providerName];
                        const color = colors[providerName] || { border: 'rgb(107, 114, 128)', bg: 'rgba(107, 114, 128, 0.1)' };

                        // Avg temperature
                        datasets.push({
                            label: `${providerName} - {{ __("app.avg_temperature") }}`,
                            data: timestamps.map(t => {
                                // Find snapshot matching this timestamp (rounded to minute)
                                const snap = providerSnapshots.find(s => {
                                    const sDate = new Date(s.fetched_at);
                                    const sRounded = new Date(sDate.getFullYear(), sDate.getMonth(), sDate.getDate(), sDate.getHours(), sDate.getMinutes()).toISOString();
                                    return sRounded === t;
                                });
                                return snap ? snap.forecast_data.temperature_avg : null;
                            }),
                            borderColor: color.border,
                            backgroundColor: color.border,
                            borderWidth: 2,
                            tension: 0.3,
                            fill: false,
                            spanGaps: true,
                            pointRadius: pointRadius,
                            pointHoverRadius: pointHoverRadius,
                            pointBackgroundColor: color.border,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointHoverBackgroundColor: color.border,
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2
                        });
                    });

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 10,
                                        font: {
                                            size: window.innerWidth < 640 ? 10 : 12
                                        }
                                    }
                                },
                                title: {
                                    display: false
                                },
                                tooltip: {
                                    enabled: true,
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': ' + Math.round(context.parsed.y * 10) / 10 + '°C';
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        maxTicksLimit: maxTicks,
                                        maxRotation: window.innerWidth < 640 ? 45 : 0,
                                        minRotation: window.innerWidth < 640 ? 45 : 0,
                                        font: {
                                            size: window.innerWidth < 640 ? 9 : 11
                                        }
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: '{{ __("app.temperature") }} (°C)',
                                        font: {
                                            size: window.innerWidth < 640 ? 11 : 13
                                        }
                                    },
                                    ticks: {
                                        font: {
                                            size: window.innerWidth < 640 ? 10 : 12
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
        @else
            <p class="text-gray-500">{{ __('app.not_enough_data') }}</p>
        @endif
    </div>


    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold mb-4">{{ __('app.forecast_snapshots') }} ({{ $request->forecastSnapshots->count() }})</h3>

        @if($request->forecastSnapshots->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-yellow-800 font-semibold">⚠ {{ __('app.no_data_yet') }}</p>
                <p class="text-sm text-yellow-700 mt-2">
                    {{ __('app.no_data_message') }}
                </p>
                <p class="text-xs text-yellow-600 mt-2">
                    {{ __('app.target_date') }}: {{ $request->target_date->format('Y-m-d') }}
                    ({{ $request->target_date->diffForHumans() }})
                </p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($request->forecastSnapshots as $snapshot)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="font-semibold">{{ $snapshot->weatherProvider->name }}</p>
                                <p class="text-sm text-gray-500">{{ __('app.fetched') }}: {{ $snapshot->fetched_at->format('Y-m-d H:i:s') }}</p>
                                @if(isset($snapshot->forecast_data['forecast_date']))
                                    <p class="text-sm text-gray-500">
                                        {{ __('app.forecast_for') }}: {{ $snapshot->forecast_data['forecast_date'] }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-3">
                            <div>
                                <p class="text-xs text-gray-600">{{ __('app.temperature') }}</p>
                                <p class="font-semibold">{{ round($snapshot->forecast_data['temperature_avg']) }}°C</p>
                                <p class="text-xs text-gray-500">
                                    {{ __('app.min') }}: {{ round($snapshot->forecast_data['temperature_min']) }}°C /
                                    {{ __('app.max') }}: {{ round($snapshot->forecast_data['temperature_max']) }}°C
                                </p>
                                @if(isset($snapshot->forecast_data['feels_like']))
                                    <p class="text-xs text-gray-500">{{ __('app.feels_like') }}: {{ round($snapshot->forecast_data['feels_like']) }}°C</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">{{ __('app.conditions') }}</p>
                                <p class="font-semibold">
                                    <span class="text-2xl">{!! \App\Helpers\WeatherIconMapper::getIcon($snapshot->forecast_data['conditions'], $snapshot->weatherProvider->name) !!}</span>
                                    {{ \App\Helpers\WeatherTranslator::translate($snapshot->forecast_data['conditions'], $snapshot->weatherProvider->name) }}
                                </p>
                                <p class="text-xs text-gray-500">{{ \App\Helpers\WeatherTranslator::translateDescription($snapshot->forecast_data['description'], $snapshot->weatherProvider->name) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">{{ __('app.precipitation') }}</p>
                                <p class="font-semibold">{{ round($snapshot->forecast_data['precipitation'] * 100) }}%</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-4 gap-4 pt-3 border-t border-gray-100">
                            <div>
                                <p class="text-xs text-gray-600">{{ __('app.humidity') }}</p>
                                <p class="font-semibold">{{ $snapshot->forecast_data['humidity'] ?? 'N/A' }}%</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">{{ __('app.pressure') }}</p>
                                <p class="font-semibold">{{ $snapshot->forecast_data['pressure'] ?? 'N/A' }} hPa</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">{{ __('app.wind') }}</p>
                                <p class="font-semibold">{{ round($snapshot->forecast_data['wind_speed'] ?? 0, 1) }} m/s</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">{{ __('app.clouds') }}</p>
                                <p class="font-semibold">{{ $snapshot->forecast_data['clouds'] ?? 'N/A' }}%</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
