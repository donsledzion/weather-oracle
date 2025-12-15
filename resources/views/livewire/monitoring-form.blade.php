<div>
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit="submit" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('app.location') }}</label>
            <input
                type="text"
                wire:model="location"
                placeholder="{{ __('app.location_placeholder') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
            @error('location') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('app.target_date') }}</label>
            <input
                type="date"
                wire:model="targetDate"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
            @error('targetDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('app.email') }}</label>
            <input
                type="email"
                wire:model="email"
                placeholder="{{ __('app.email_placeholder') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <button
            type="submit"
            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove>{{ __('app.start_monitoring') }}</span>
            <span wire:loading>{{ __('app.creating') }}</span>
        </button>
    </form>
</div>
