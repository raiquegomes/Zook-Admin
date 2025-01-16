<div x-data="{ open: true }">
    <x-filament::modal :open="true" @close="open = false">
        <x-slot name="heading">
            Motivo da Rejeição
        </x-slot>

        <x-filament::form wire:submit.prevent="rejectActivity({{ $record->id }}, rejectReason)">
            <div>
                <x-filament::textarea wire:model="rejectReason" label="Motivo da Rejeição" required />
            </div>
            <x-filament::button type="submit" color="danger">
                Rejeitar
            </x-filament::button>
        </x-filament::form>

        <x-slot name="footer">
            <x-filament::button @click="open = false" color="secondary">
                Cancelar
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
</div>
