<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Convite - Empresa
        </x-slot>
        {{-- Formulário de entrada --}}
        <form wire:submit.prevent="submit" class="flex items-center space-x-4">
            <!-- Campo de Input -->
            <x-filament::input.wrapper>
                <x-filament::input
                    type="text"
                    wire:model.defer="code"
                    label="Código de Convite"
                    placeholder="Insira seu código de convite"
                    required
                    />
            </x-filament::input.wrapper>

            <!-- Botão de Confirmar -->
            <x-filament::button
                type="submit"
                class="ml-auto"
            >
                Confirmar
            </x-filament::button>
        </form>
    </x-filament::section>
</x-filament-widgets::widget>
