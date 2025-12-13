<div>
    <h3 class="text-xl font-bold mb-4">Active Monitoring Requests</h3>

    @if($requests->isEmpty())
        <p class="text-gray-500">No monitoring requests yet. Create one above!</p>
    @else
        <div class="space-y-3">
            @foreach($requests as $request)
                <a href="{{ route('requests.show', $request->id) }}" class="block">
                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-lg">{{ $request->location }}</h4>
                                <p class="text-sm text-gray-600">Target: {{ $request->target_date->format('Y-m-d') }}</p>
                                @if($request->email)
                                    <p class="text-sm text-gray-500">{{ $request->email }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                                    {{ $request->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                                <p class="text-xs text-gray-400 mt-1">Created {{ $request->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
