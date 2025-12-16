<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-4">
            <a href="/" class="text-blue-600 hover:text-blue-800">&larr; {{ __('app.back_to_all_requests') }}</a>
        </div>

        @livewire('monitoring-request-details', ['requestId' => $requestId])
    </div>
</x-layouts.app>
