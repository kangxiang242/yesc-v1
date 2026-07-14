<x-filament-panels::page>
    <form wire:submit="save">
        <div class="mb-6 flex items-center justify-end gap-3">
            <x-filament::button type="submit">
                保存
            </x-filament::button>
        </div>

        {{ $this->form }}

        <div class="mt-6 flex items-center justify-end gap-3">
            <x-filament::button type="submit">
                保存
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
