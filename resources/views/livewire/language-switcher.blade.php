<div>
    <x-filament::dropdown placement="bottom-end">
        <x-slot name="trigger">
            <x-filament::button color="gray" size="sm" icon="heroicon-o-language">
                {{ strtoupper($selectedLocale) }}
            </x-filament::button>
        </x-slot>
        
        <x-filament::dropdown.list>
            @foreach($locales as $localeCode => $properties)
                <x-filament::dropdown.list.item 
                    wire:click="switchLanguage('{{ $localeCode }}')"
                    :color="$localeCode === $selectedLocale ? 'primary' : 'gray'"
                    :icon="$localeCode === $selectedLocale ? 'heroicon-m-check-circle' : null"
                >
                    <div class="flex items-center justify-between gap-3 w-full">
                        <span class="font-medium">
                            {{ $properties['native'] ?? $properties['name'] }}
                        </span>
                        <span class="text-xs text-gray-500 uppercase">
                            {{ $localeCode }}
                        </span>
                    </div>
                </x-filament::dropdown.list.item>
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
