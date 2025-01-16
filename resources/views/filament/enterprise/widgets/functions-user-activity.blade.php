<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Ferramentas de Atividades e Usuários
        </x-slot>

        <x-slot name="description">
            As funções encontradas foram desenvolvidas para organizar, planejar e corrigir.
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
                        label="Data de Início"
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
                <x-filament::button type="submit" class="ml-auto">
                    Confirmar
                </x-filament::button>
            </form>

            <!-- Botão de impressão -->
            <div class="flex justify-end mt-4">
                <x-filament::button type="button" onclick="printReport()">
                    Imprimir Relatório
                </x-filament::button>
            </div>

            <!-- Área imprimível -->
            <div id="printable-area" class="mt-4">
                <table class="w-full bg-white border border-collapse border-gray-300 rounded-lg shadow-lg table-auto">
                    <thead class="text-sm tracking-wide text-gray-700 uppercase bg-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left">Usuário</th>
                            <th class="px-4 py-2 text-left">Média de Conclusão (%)</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm tracking-wide text-gray-700 uppercase bg-gray-200">
                        @foreach($usuarios as $usuario)
                            <tr class="hover:bg-gray-100">
                                <td class="px-4 py-2 border border-gray-300">{{ $usuario['name'] }}</td>
                                <td class="px-4 py-2 text-right border border-gray-300">{{ number_format($usuario['average_percentage'], 2) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::modal>
    </x-filament::section>
</x-filament-widgets::widget>
