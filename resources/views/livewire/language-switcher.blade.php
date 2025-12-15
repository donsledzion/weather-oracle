<div class="flex items-center gap-2">
    @foreach($availableLocales as $locale)
        <button
            wire:click="switchLanguage('{{ $locale }}')"
            class="px-3 py-1 text-sm font-medium rounded-md transition
                {{ $currentLocale === $locale
                    ? 'bg-blue-500 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
        >
            {{ strtoupper($locale) }}
        </button>
    @endforeach

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('locale-changed', () => {
                window.location.reload();
            });
        });
    </script>
</div>
