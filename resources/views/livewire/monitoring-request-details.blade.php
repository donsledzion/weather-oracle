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

    <div class="bg-white rounded-lg shadow p-6 mb-6" x-data="{ activeMetric: 'temperature' }">
        <h3 class="text-xl font-bold mb-4">{{ __('app.weather_trends') }}</h3>

        @if($request->forecastSnapshots->count() > 0)
            {{-- Metric tabs --}}
            <div class="flex flex-wrap gap-2 mb-4 border-b border-gray-200">
                <button @click="activeMetric = 'temperature'"
                        :class="activeMetric === 'temperature' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">
                    <span class="text-lg mr-1">🌡️</span> {{ __('app.metric_temperature') }}
                </button>
                <button @click="activeMetric = 'precipitation'"
                        :class="activeMetric === 'precipitation' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">
                    <span class="text-lg mr-1">🌧️</span> {{ __('app.metric_precipitation') }}
                </button>
                <button @click="activeMetric = 'clouds'"
                        :class="activeMetric === 'clouds' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">
                    <span class="text-lg mr-1">☁️</span> {{ __('app.metric_clouds') }}
                </button>
                <button @click="activeMetric = 'pressure'"
                        :class="activeMetric === 'pressure' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">
                    <span class="text-lg mr-1">🔽</span> {{ __('app.metric_pressure') }}
                </button>
                <button @click="activeMetric = 'wind'"
                        :class="activeMetric === 'wind' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">
                    <span class="text-lg mr-1">💨</span> {{ __('app.metric_wind') }}
                </button>
                <button @click="activeMetric = 'humidity'"
                        :class="activeMetric === 'humidity' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">
                    <span class="text-lg mr-1">💧</span> {{ __('app.metric_humidity') }}
                </button>
            </div>

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
                        maxTicks = dataCount;
                    } else if (dataCount <= 50) {
                        maxTicks = Math.ceil(dataCount / 3);
                    } else if (dataCount <= 100) {
                        maxTicks = Math.ceil(dataCount / 5);
                    } else {
                        maxTicks = Math.ceil(dataCount / 10);
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
                        pointRadius = 0;
                        pointHoverRadius = 4;
                    }

                    // Function to get datasets for a specific metric
                    function getDatasetsForMetric(metric) {
                        const datasets = [];
                        Object.keys(providerData).forEach(providerName => {
                            const providerSnapshots = providerData[providerName];
                            const color = colors[providerName] || { border: 'rgb(107, 114, 128)', bg: 'rgba(107, 114, 128, 0.1)' };

                            let label, dataExtractor;
                            switch(metric) {
                                case 'temperature':
                                    label = `${providerName} - {{ __("app.avg_temperature") }}`;
                                    dataExtractor = (snap) => snap.forecast_data.temperature_avg;
                                    break;
                                case 'precipitation':
                                    label = `${providerName} - {{ __("app.precipitation") }}`;
                                    dataExtractor = (snap) => snap.forecast_data.precipitation * 100;
                                    break;
                                case 'clouds':
                                    label = `${providerName} - {{ __("app.clouds") }}`;
                                    dataExtractor = (snap) => snap.forecast_data.clouds;
                                    break;
                                case 'pressure':
                                    label = `${providerName} - {{ __("app.pressure") }}`;
                                    dataExtractor = (snap) => snap.forecast_data.pressure;
                                    break;
                                case 'wind':
                                    label = `${providerName} - {{ __("app.wind") }}`;
                                    dataExtractor = (snap) => snap.forecast_data.wind_speed;
                                    break;
                                case 'humidity':
                                    label = `${providerName} - {{ __("app.humidity") }}`;
                                    dataExtractor = (snap) => snap.forecast_data.humidity;
                                    break;
                            }

                            datasets.push({
                                label: label,
                                data: timestamps.map(t => {
                                    const snap = providerSnapshots.find(s => {
                                        const sDate = new Date(s.fetched_at);
                                        const sRounded = new Date(sDate.getFullYear(), sDate.getMonth(), sDate.getDate(), sDate.getHours(), sDate.getMinutes()).toISOString();
                                        return sRounded === t;
                                    });
                                    return snap ? dataExtractor(snap) : null;
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
                        return datasets;
                    }

                    // Function to get Y-axis config for metric
                    function getYAxisConfig(metric) {
                        const configs = {
                            'temperature': { label: '{{ __("app.temperature") }} (°C)', unit: '°C' },
                            'precipitation': { label: '{{ __("app.precipitation") }} (%)', unit: '%' },
                            'clouds': { label: '{{ __("app.clouds") }} (%)', unit: '%' },
                            'pressure': { label: '{{ __("app.pressure") }} (hPa)', unit: ' hPa' },
                            'wind': { label: '{{ __("app.wind") }} (m/s)', unit: ' m/s' },
                            'humidity': { label: '{{ __("app.humidity") }} (%)', unit: '%' }
                        };
                        return configs[metric] || configs['temperature'];
                    }

                    // Create chart instance
                    let currentMetric = 'temperature';
                    const weatherChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: getDatasetsForMetric('temperature')
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
                                            const config = getYAxisConfig(currentMetric);
                                            return context.dataset.label + ': ' + Math.round(context.parsed.y * 10) / 10 + config.unit;
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
                                        text: getYAxisConfig('temperature').label,
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

                    // Listen to Alpine.js metric changes
                    document.addEventListener('alpine:initialized', () => {
                        Alpine.effect(() => {
                            const metric = Alpine.$data(document.querySelector('[x-data]')).activeMetric;
                            if (metric && metric !== currentMetric) {
                                currentMetric = metric;
                                const config = getYAxisConfig(metric);

                                // Update datasets
                                weatherChart.data.datasets = getDatasetsForMetric(metric);

                                // Update Y-axis label
                                weatherChart.options.scales.y.title.text = config.label;

                                // Update chart
                                weatherChart.update();
                            }
                        });
                    });
                });
            </script>
        @else
            <p class="text-gray-500">{{ __('app.not_enough_data') }}</p>
        @endif
    </div>


    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">{{ __('app.forecast_snapshots') }} ({{ $request->forecastSnapshots->count() }})</h3>

            @if($request->forecastSnapshots->count() > 0)
                <div x-data="{ allExpanded: false }">
                    <button
                        @click="allExpanded = !allExpanded; document.querySelectorAll('[x-data]').forEach(el => { if(el.__x) el.__x.$data.open = allExpanded })"
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        <span x-show="!allExpanded">{{ __('app.expand_all') }}</span>
                        <span x-show="allExpanded">{{ __('app.collapse_all') }}</span>
                    </button>
                </div>
            @endif
        </div>

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
            @php
                // Group snapshots by fetched time (rounded to minute)
                $groupedSnapshots = $request->forecastSnapshots->groupBy(function($snapshot) {
                    return $snapshot->fetched_at->format('Y-m-d H:i');
                })->sortKeysDesc();
            @endphp

            <div class="space-y-3">
                @foreach($groupedSnapshots as $fetchTime => $snapshots)
                    <div class="border border-gray-200 rounded-lg overflow-hidden" x-data="{ open: false }">
                        {{-- Compact view (always visible) --}}
                        <div @click="open = !open" class="p-4 cursor-pointer hover:bg-gray-50 transition">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4 flex-1">
                                    {{-- Icons from all providers --}}
                                    <div class="flex gap-1">
                                        @foreach($snapshots as $snap)
                                            <span class="text-2xl">{!! \App\Helpers\WeatherIconMapper::getIcon($snap->forecast_data['conditions'], $snap->weatherProvider->name) !!}</span>
                                        @endforeach
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-lg">{{ \Carbon\Carbon::parse($fetchTime)->format('Y-m-d H:i') }}</p>
                                        @php
                                            $count = $snapshots->count();
                                            if ($count == 1) {
                                                $sourceLabel = __('app.provider');
                                            } elseif ($count >= 2 && $count <= 4) {
                                                $sourceLabel = __('app.providers_2_4');
                                            } else {
                                                $sourceLabel = __('app.providers');
                                            }
                                        @endphp
                                        <p class="text-sm text-gray-600">{{ $count }} {{ $sourceLabel }}</p>
                                    </div>
                                    <div class="text-right text-sm text-gray-500">
                                        @php
                                            $temps = $snapshots->pluck('forecast_data.temperature_avg');
                                            $avgTemp = round($temps->avg());
                                        @endphp
                                        <p class="font-semibold text-lg">{{ $avgTemp }}°C</p>
                                        <p class="text-xs">{{ __('app.average') }}</p>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Detailed view (collapsible) --}}
                        <div x-show="open" x-collapse class="border-t border-gray-200 bg-gray-50">
                            <div class="p-4">
                                <div class="grid grid-cols-1 md:grid-cols-{{ $snapshots->count() }} gap-4">
                                    @foreach($snapshots as $snapshot)
                                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                                            {{-- Provider header --}}
                                            <div class="mb-3 pb-2 border-b border-gray-200">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-2xl">{!! \App\Helpers\WeatherIconMapper::getIcon($snapshot->forecast_data['conditions'], $snapshot->weatherProvider->name) !!}</span>
                                                    <p class="font-bold text-sm">{{ $snapshot->weatherProvider->name }}</p>
                                                </div>
                                            </div>

                                            {{-- Main metrics --}}
                                            <div class="space-y-3 mb-3">
                                                <div>
                                                    <p class="text-xs text-gray-600">{{ __('app.temperature') }}</p>
                                                    <p class="font-semibold text-lg">{{ round($snapshot->forecast_data['temperature_avg']) }}°C</p>
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
                                                    <p class="font-semibold text-sm">
                                                        {{ \App\Helpers\WeatherTranslator::translate($snapshot->forecast_data['conditions'], $snapshot->weatherProvider->name) }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">{{ \App\Helpers\WeatherTranslator::translateDescription($snapshot->forecast_data['description'], $snapshot->weatherProvider->name) }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-600">{{ __('app.precipitation') }}</p>
                                                    <p class="font-semibold">{{ round($snapshot->forecast_data['precipitation'] * 100) }}%</p>
                                                </div>
                                            </div>

                                            {{-- Additional metrics --}}
                                            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-100">
                                                <div>
                                                    <p class="text-xs text-gray-600">{{ __('app.humidity') }}</p>
                                                    <p class="font-semibold text-sm">{{ $snapshot->forecast_data['humidity'] ?? 'N/A' }}%</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-600">{{ __('app.pressure') }}</p>
                                                    <p class="font-semibold text-sm">{{ $snapshot->forecast_data['pressure'] ?? 'N/A' }} hPa</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-600">{{ __('app.wind') }}</p>
                                                    <p class="font-semibold text-sm">{{ round($snapshot->forecast_data['wind_speed'] ?? 0, 1) }} m/s</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-600">{{ __('app.clouds') }}</p>
                                                    <p class="font-semibold text-sm">{{ $snapshot->forecast_data['clouds'] ?? 'N/A' }}%</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
