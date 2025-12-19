<div>
    @if(session('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('message') }}
        </div>
    @endif

    @if($requests->isEmpty())
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
            <p class="text-gray-600">{{ __('app.no_requests_yet') }}</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($requests as $request)
                <div class="bg-white border rounded-lg p-6
                    @if($request->status === 'active') border-green-400
                    @elseif($request->status === 'completed') border-blue-400
                    @else border-gray-300 @endif">

                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold">{{ $request->location }}</h3>
                            <p class="text-gray-600">{{ __('app.target_date') }}: {{ $request->target_date->format('Y-m-d') }}</p>
                            <p class="text-sm text-gray-500">{{ __('app.created') }}: {{ $request->created_at->diffForHumans() }}</p>
                        </div>

                        <div>
                            @if($request->status === 'active')
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

                    <div class="flex gap-3">
                        @if($request->status === 'active' || $request->status === 'completed')
                            <a href="{{ route('requests.show', $request->id) }}"
                               class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                                📊 {{ __('app.view_details') }}
                            </a>
                            @if($request->forecastSnapshots->count() > 0)
                                <span class="text-sm text-gray-600 self-center">
                                    {{ $request->forecastSnapshots->count() }} {{ __('app.snapshots') }}
                                </span>
                            @endif
                        @endif

                        <button
                            wire:click="deleteRequest({{ $request->id }})"
                            onclick="return confirm('{{ __('app.confirm_delete') }}')"
                            class="ml-auto inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                            🗑️ {{ __('app.delete_request') }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
