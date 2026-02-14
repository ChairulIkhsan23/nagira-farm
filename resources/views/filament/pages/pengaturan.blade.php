<x-filament-panels::page>
    {{ $this->form }}

    <div class="mt-6">
        <x-filament::button wire:click="save">
            Simpan Perubahan
        </x-filament::button>
    </div>
</x-filament-panels::page>
