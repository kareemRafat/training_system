<x-filament::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}

        <div class="flex justify-end mt-4">
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                حفظ الطلاب
            </button>
        </div>
    </form>

    <x-filament-actions::modals />
</x-filament::page>
