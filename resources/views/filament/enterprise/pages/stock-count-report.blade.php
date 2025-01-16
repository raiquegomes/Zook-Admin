<x-filament-panels::page>
    <x-filament::section class="p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-lg font-bold text-gray-800">Gerar Relatório</h2>
        <p class="text-sm text-gray-600">Preencha as informações abaixo para gerar o relatório desejado.</p>

        <form wire:submit.prevent="generateReport" class="mt-4 space-y-6 text-sm">
            {{ $this->form }}

            <x-filament::button type="submit" class="w-full px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                Gerar Relatório
            </x-filament::button>
        </form>
    </x-filament::section>

    @if ($reportData)
        @php
            // Coleta todos os produtos únicos em uma coleção
            $allProducts = collect($reportData)
                ->flatMap(fn($data) => array_keys($data['products']))
                ->unique();
        @endphp

        <x-filament::section class="p-6 mt-6 bg-white rounded-lg shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-800">Relatório de Contagem</h2>
                <x-filament::button type="button" onclick="printReport()" class="flex items-center px-3 py-2 text-sm text-white bg-gray-700 rounded-md hover:bg-gray-800">
                    <x-heroicon-o-printer class="w-5 h-5 mr-2" />
                </x-filament::button>
            </div>

            <div id="printable-area" class="text-sm">
                <table class="w-full text-left border border-collapse border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border border-gray-300">Produto</th>
                            @foreach ($reportData as $filialId => $data)
                                <th class="px-4 py-2 border border-gray-300" colspan="3">{{ $data['filial_name'] }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="px-4 py-2 border border-gray-300"></th>
                            @foreach ($reportData as $filialId => $data)
                                <th class="px-4 py-2 border border-gray-300">Desossa (kg)</th>
                                <th class="px-4 py-2 border border-gray-300">Caixaria (kg)</th>
                                <th class="px-4 py-2 border border-gray-300">Qualidade</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($allProducts as $productName)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border border-gray-300">{{ $productName }}</td>
                                @foreach ($reportData as $filialId => $data)
                                    <td class="px-4 py-2 border border-gray-300 {{ ($data['products'][$productName]['boning_stock'] ?? 0) > 0 ? 'font-bold text-blue-600' : '' }}">
                                        {{ $data['products'][$productName]['boning_stock'] ?? 0 }}
                                    </td>
                                    <td class="px-4 py-2 border border-gray-300 {{ ($data['products'][$productName]['cashier_stock'] ?? 0) > 0 ? 'font-bold text-green-600' : '' }}">
                                        {{ $data['products'][$productName]['cashier_stock'] ?? 0 }}
                                    </td>
                                    <td class="px-4 py-2 text-center border border-gray-300">
                                        @php
                                            $quality = $data['products'][$productName]['quality'] ?? null;
                                        @endphp
                                        @if ($quality === 'bom')
                                            <x-filament::badge color="info">
                                                Bom
                                            </x-filament::badge>
                                        @elseif ($quality === 'muito_bom')
                                            <x-filament::badge color="success">
                                                Muito Bom
                                            </x-filament::badge>
                                        @elseif ($quality === 'ruim')
                                            <x-filament::badge color="warning">
                                                Ruim
                                            </x-filament::badge>
                                        @else
                                            <x-filament::badge color="gray">
                                                Indefinido
                                            </x-filament::badge>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
