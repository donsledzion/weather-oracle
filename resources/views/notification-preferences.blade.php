<x-layouts.app>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ __('app.notification_preferences') }}</h1>
            <p class="text-gray-600 mb-6">
                {{ __('app.managing_notifications_for') }}:
                <strong>{{ $preferences->email ?? auth()->user()?->email }}</strong>
            </p>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Global Notification Preferences -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('app.global_notification_settings') }}</h2>
                <p class="text-sm text-gray-600 mb-4">{{ __('app.global_settings_description') }}</p>

                <form method="POST" action="{{ route('notifications.update-global', $token) }}">
                    @csrf
                    <div class="space-y-4">
                        <!-- First Snapshot Toggle -->
                        <div class="flex items-center justify-between p-5 bg-gradient-to-r from-gray-50 to-white border-2 border-gray-200 rounded-xl hover:border-blue-400 hover:shadow-md transition-all">
                            <div class="flex-1 pr-4">
                                <label class="font-semibold text-gray-900 text-base cursor-pointer">{{ __('app.first_snapshot_notifications') }}</label>
                                <p class="text-sm text-gray-600 mt-1">{{ __('app.first_snapshot_description') }}</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                <input type="checkbox" name="first_snapshot_enabled" value="1"
                                       {{ $preferences->first_snapshot_enabled ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-14 h-8 bg-red-500 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-7 after:w-7 after:transition-all after:shadow-md peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <!-- Daily Summary Toggle -->
                        <div class="flex items-center justify-between p-5 bg-gradient-to-r from-gray-50 to-white border-2 border-gray-200 rounded-xl hover:border-blue-400 hover:shadow-md transition-all">
                            <div class="flex-1 pr-4">
                                <label class="font-semibold text-gray-900 text-base cursor-pointer">{{ __('app.daily_summary_notifications') }}</label>
                                <p class="text-sm text-gray-600 mt-1">{{ __('app.daily_summary_description') }}</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                <input type="checkbox" name="daily_summary_enabled" value="1"
                                       {{ $preferences->daily_summary_enabled ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-14 h-8 bg-red-500 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-7 after:w-7 after:transition-all after:shadow-md peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <!-- Final Summary Toggle -->
                        <div class="flex items-center justify-between p-5 bg-gradient-to-r from-gray-50 to-white border-2 border-gray-200 rounded-xl hover:border-blue-400 hover:shadow-md transition-all">
                            <div class="flex-1 pr-4">
                                <label class="font-semibold text-gray-900 text-base cursor-pointer">{{ __('app.final_summary_notifications') }}</label>
                                <p class="text-sm text-gray-600 mt-1">{{ __('app.final_summary_description') }}</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                <input type="checkbox" name="final_summary_enabled" value="1"
                                       {{ $preferences->final_summary_enabled ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-14 h-8 bg-red-500 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-7 after:w-7 after:transition-all after:shadow-md peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="mt-6 w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition">
                        {{ __('app.save_preferences') }}
                    </button>
                </form>
            </div>

            <!-- Per-Request Notification Toggles -->
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('app.per_request_settings') }}</h2>
                <p class="text-sm text-gray-600 mb-4">{{ __('app.per_request_description') }}</p>

                @if($requests->isEmpty())
                    <p class="text-gray-500 italic">{{ __('app.no_requests_found') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach($requests as $request)
                            <form method="POST" action="{{ route('notifications.toggle-request', [$token, $request->id]) }}"
                                  class="flex items-center justify-between p-5 bg-gradient-to-r from-gray-50 to-white border-2 border-gray-200 rounded-xl hover:border-green-400 hover:shadow-md transition-all">
                                @csrf
                                <div class="flex-1 pr-4">
                                    <div class="font-semibold text-gray-900 text-base">{{ $request->location }}</div>
                                    <div class="text-sm text-gray-600 mt-1">
                                        {{ __('app.target_date') }}: {{ $request->target_date->format('Y-m-d') }}
                                        <span class="ml-3 px-2 py-1 rounded-md text-xs font-semibold
                                            @if($request->status === 'pending_verification') bg-yellow-100 text-yellow-800
                                            @elseif($request->status === 'verified') bg-blue-100 text-blue-800
                                            @elseif($request->status === 'active') bg-green-100 text-green-800
                                            @elseif($request->status === 'completed') bg-gray-100 text-gray-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ __('app.status_' . $request->status) }}
                                        </span>
                                    </div>
                                </div>

                                <button type="submit" class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                    <input type="hidden" name="toggle" value="1">
                                    <div class="w-14 h-8 rounded-full transition-all duration-200
                                        {{ $request->notifications_enabled ? 'bg-green-600' : 'bg-red-500' }}">
                                        <div class="absolute top-[2px] left-[2px] bg-white border border-gray-300 rounded-full h-7 w-7 transition-transform duration-200 shadow-md
                                            {{ $request->notifications_enabled ? 'translate-x-6' : '' }}"></div>
                                    </div>
                                </button>
                            </form>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
