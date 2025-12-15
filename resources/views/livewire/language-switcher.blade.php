<div class="flex items-center gap-2">
    @php
        $flags = [
            'pl' => '🇵🇱',
            'en' => '🇬🇧',
        ];
    @endphp
    @foreach($availableLocales as $locale)
        <button
            wire:click="switchLanguage('{{ $locale }}')"
            class="px-3 py-2 text-lg rounded-md transition hover:scale-110
                {{ $currentLocale === $locale
                    ? 'ring-2 ring-blue-500 shadow-md'
                    : 'opacity-60 hover:opacity-100' }}"
            title="{{ strtoupper($locale) }}"
        >
            {{ $flags[$locale] ?? strtoupper($locale) }}
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
