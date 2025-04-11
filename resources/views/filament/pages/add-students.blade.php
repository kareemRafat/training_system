<x-filament::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}
        <div class="flex justify-end mt-4">
            <x-filament::button type="submit" wire:loading.class="opacity-50" wire:target="save" :loading="false">
                <span wire:target="save" class="inline-flex items-center">
                    حفظ الطلاب
                </span>
            </x-filament::button>
        </div>
    </form>
    <x-filament-actions::modals />
</x-filament::page>
