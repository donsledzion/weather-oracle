<div>
    <!-- Success Message -->
    <div x-data="{ show: false, message: '' }"
         @notification-updated.window="show = true; message = $event.detail.message; setTimeout(() => show = false, 3000)"
         x-show="show"
         x-transition
         x-cloak
         style="display: none;"
         class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <span x-text="message"></span>
    </div>

    <!-- Global Notification Preferences -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('app.global_notification_settings') }}</h2>
        <p class="text-sm text-gray-600 mb-4">{{ __('app.global_settings_description') }}</p>

        <div class="space-y-3">
            <!-- First Snapshot Toggle -->
            <div class="flex items-center justify-between p-4 bg-white border-2 border-gray-200 rounded-lg hover:border-blue-300 transition">
                <div class="flex-1">
                    <label class="font-medium text-gray-800 cursor-pointer">{{ __('app.first_snapshot_notifications') }}</label>
                    <p class="text-sm text-gray-600">{{ __('app.first_snapshot_description') }}</p>
                </div>
                <button
                    wire:click="toggleGlobal('first_snapshot')"
                    wire:loading.attr="disabled"
                    class="relative inline-flex h-10 w-16 shrink-0 items-center rounded-full border-2 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                        {{ $firstSnapshotEnabled ? 'bg-blue-600 border-blue-700' : 'bg-red-500 border-red-600' }}">
                    <span class="inline-block h-7 w-7 transform rounded-full bg-white shadow-lg transition-transform duration-200
                        {{ $firstSnapshotEnabled ? 'translate-x-7' : 'translate-x-1' }}"></span>
                    <span wire:loading wire:target="toggleGlobal('first_snapshot')" class="absolute inset-0 flex items-center justify-center">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </div>

            <!-- Daily Summary Toggle -->
            <div class="flex items-center justify-between p-4 bg-white border-2 border-gray-200 rounded-lg hover:border-blue-300 transition">
                <div class="flex-1">
                    <label class="font-medium text-gray-800 cursor-pointer">{{ __('app.daily_summary_notifications') }}</label>
                    <p class="text-sm text-gray-600">{{ __('app.daily_summary_description') }}</p>
                </div>
                <button
                    wire:click="toggleGlobal('daily_summary')"
                    wire:loading.attr="disabled"
                    class="relative inline-flex h-10 w-16 shrink-0 items-center rounded-full border-2 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                        {{ $dailySummaryEnabled ? 'bg-blue-600 border-blue-700' : 'bg-red-500 border-red-600' }}">
                    <span class="inline-block h-7 w-7 transform rounded-full bg-white shadow-lg transition-transform duration-200
                        {{ $dailySummaryEnabled ? 'translate-x-7' : 'translate-x-1' }}"></span>
                    <span wire:loading wire:target="toggleGlobal('daily_summary')" class="absolute inset-0 flex items-center justify-center">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </div>

            <!-- Final Summary Toggle -->
            <div class="flex items-center justify-between p-4 bg-white border-2 border-gray-200 rounded-lg hover:border-blue-300 transition">
                <div class="flex-1">
                    <label class="font-medium text-gray-800 cursor-pointer">{{ __('app.final_summary_notifications') }}</label>
                    <p class="text-sm text-gray-600">{{ __('app.final_summary_description') }}</p>
                </div>
                <button
                    wire:click="toggleGlobal('final_summary')"
                    wire:loading.attr="disabled"
                    class="relative inline-flex h-10 w-16 shrink-0 items-center rounded-full border-2 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                        {{ $finalSummaryEnabled ? 'bg-blue-600 border-blue-700' : 'bg-red-500 border-red-600' }}">
                    <span class="inline-block h-7 w-7 transform rounded-full bg-white shadow-lg transition-transform duration-200
                        {{ $finalSummaryEnabled ? 'translate-x-7' : 'translate-x-1' }}"></span>
                    <span wire:loading wire:target="toggleGlobal('final_summary')" class="absolute inset-0 flex items-center justify-center">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </div>
        </div>
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
                    <div class="flex items-center justify-between p-4 bg-white border-2 border-gray-200 rounded-lg hover:border-green-300 transition">
                        <div class="flex-1">
                            <div class="font-medium text-gray-800">{{ $request->location }}</div>
                            <div class="text-sm text-gray-600">
                                {{ __('app.target_date') }}: {{ $request->target_date->format('Y-m-d') }}
                                <span class="ml-3 px-2 py-1 rounded text-xs font-medium
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

                        <button
                            wire:click="toggleRequest({{ $request->id }})"
                            wire:loading.attr="disabled"
                            class="relative inline-flex h-10 w-16 shrink-0 items-center rounded-full border-2 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2
                                {{ $request->notifications_enabled ? 'bg-green-600 border-green-700' : 'bg-red-500 border-red-600' }}">
                            <span class="inline-block h-7 w-7 transform rounded-full bg-white shadow-lg transition-transform duration-200
                                {{ $request->notifications_enabled ? 'translate-x-7' : 'translate-x-1' }}"></span>
                            <span wire:loading wire:target="toggleRequest({{ $request->id }})" class="absolute inset-0 flex items-center justify-center">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
