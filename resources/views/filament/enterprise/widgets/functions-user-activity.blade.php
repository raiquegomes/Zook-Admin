<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Ferramentas de Atividades e Usuários
        </x-slot>

        <x-slot name="description">
            As funções encontradas foram desenvolvidas para organizar e planejar e corrigir.
        </x-slot>
        <x-filament::modal width="5xl">
            <x-slot name="trigger">
                <x-filament::button>
                    Pontuação (%)
                </x-filament::button>
            </x-slot>
            <x-slot name="heading">
                Pontuação (%)
            </x-slot>

            <form wire:submit.prevent="submit" class="flex items-center space-x-4">
                <!-- Campo de Input -->
                <x-filament::input.wrapper>
                    <x-filament::input
                        type="date"
                        wire:model.defer="startDate"
                        label="Data de Inicio"
                        required
                    />
                </x-filament::input.wrapper>
                até
                <x-filament::input.wrapper>
                    <x-filament::input
                    type="date"
                    wire:model.defer="endDate"
                    label="Data de Finalização"
                    required
                />
                </x-filament::input.wrapper>
                <x-filament::button type="submit" class="ml-auto" >
                    Confirmar
                </x-filament::button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th class="p-2 border border-gray-300">Usuário</th>
                        <th class="p-2 border border-gray-300">Total de %</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                        <tr>
                            <td class="p-2 border border-gray-300">{{ $usuario['name'] }}</td>
                            <td class="p-2 border border-gray-300">{{ number_format($usuario['total_percentage'], 2) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-filament::modal>
    </x-filament::section>

</x-filament-widgets::widget>
