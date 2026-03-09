<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}
        
        <div class="flex items-center gap-4">
            <x-filament::button
                type="submit"
                color="primary"
                icon="heroicon-o-check"
            >
                Simpan Perubahan
            </x-filament::button>
            
            <x-filament::button
                type="button"
                color="gray"
                icon="heroicon-o-arrow-path"
                tag="a"
                href="{{ url()->previous() }}"
            >
                Batal
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>