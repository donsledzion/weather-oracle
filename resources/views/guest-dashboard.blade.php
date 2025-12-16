<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h1 class="text-2xl font-bold mb-2">{{ __('app.guest_dashboard') }}</h1>
            <p class="text-gray-600">{{ __('app.managing_requests_for') }}: <strong>{{ $email }}</strong></p>
            <p class="text-sm text-gray-500 mt-2">
                {{ __('app.guest_dashboard_info') }}
            </p>
        </div>

        @if($requests->isEmpty())
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                <p class="text-gray-600">{{ __('app.no_requests_yet') }}</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($requests as $request)
                    <div class="bg-white border rounded-lg p-6
                        @if($request->status === 'pending_verification') border-yellow-400
                        @elseif($request->status === 'active') border-green-400
                        @elseif($request->status === 'completed') border-blue-400
                        @else border-gray-300 @endif">

                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold">{{ $request->location }}</h3>
                                <p class="text-gray-600">{{ __('app.target_date') }}: {{ $request->target_date->format('Y-m-d') }}</p>
                                <p class="text-sm text-gray-500">{{ __('app.created') }}: {{ $request->created_at->diffForHumans() }}</p>
                            </div>

                            <div>
                                @if($request->status === 'pending_verification')
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        ⏳ {{ __('app.pending_verification') }}
                                    </span>
                                @elseif($request->status === 'active')
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        ✅ {{ __('app.active') }}
                                    </span>
                                @elseif($request->status === 'completed')
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        📊 {{ __('app.completed') }}
                                    </span>
                                @elseif($request->status === 'expired')
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        ⏰ {{ __('app.expired') }}
                                    </span>
                                @elseif($request->status === 'rejected')
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        ❌ {{ __('app.rejected') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($request->status === 'pending_verification')
                            <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mb-4">
                                <p class="text-yellow-800 text-sm mb-2">
                                    ⚠️ {{ __('app.pending_activation_message') }}
                                </p>
                                @if($request->expires_at)
                                    <p class="text-xs text-yellow-700">
                                        {{ __('app.expires_in') }}: {{ $request->expires_at->diffForHumans() }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex gap-3">
                                <a href="{{ route('requests.verify', $request->verification_token) }}"
                                   class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                                    ✅ {{ __('app.activate_now') }}
                                </a>
                                <a href="{{ route('requests.reject', $request->verification_token) }}"
                                   class="inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition"
                                   onclick="return confirm('{{ __('app.confirm_reject') }}')">
                                    ❌ {{ __('app.cancel_request') }}
                                </a>
                            </div>
                        @elseif($request->status === 'active' || $request->status === 'completed')
                            <div class="flex gap-3">
                                <a href="{{ route('requests.show', $request->id) }}"
                                   class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                                    📊 {{ __('app.view_details') }}
                                </a>
                                @if($request->forecastSnapshots->count() > 0)
                                    <span class="text-sm text-gray-600 self-center">
                                        {{ $request->forecastSnapshots->count() }} {{ __('app.snapshots') }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="font-bold text-blue-900 mb-2">💡 {{ __('app.want_more') }}</h3>
            <p class="text-blue-800 mb-4">
                {{ __('app.guest_limit_info') }}
            </p>
            <a href="/" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                {{ __('app.create_free_account') }}
            </a>
        </div>
    </div>
</x-layouts.app>
