<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Processamento de Atividades
        </x-slot>

        <x-slot name="description">
            A realização do processamento de atividades e totalmente prioritario para manter os calculos atualizados.
        </x-slot>

        <form wire:submit.prevent="reprocessActivities" class="flex items-center">
            <x-filament::input.wrapper>
                <x-filament::input
                    type="date"
                    wire:model.defer="date"
                    label="Selecione uma data"
                    display-format="d/m/Y"
                    placeholder="Escolha a data"
                    required
                />
            </x-filament::input.wrapper>

            {{-- Botão para Reprocessar --}}
            <x-filament::button class="ml-auto" icon="heroicon-m-arrow-path" icon-position="after" tooltip="Reprocessar Atividades" type="submit" color="primary">
                Reprocessar
            </x-filament::button>
        </form>
    </x-filament::section>
</x-filament-widgets::widget>
