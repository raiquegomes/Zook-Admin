<div>
    <h3>Resumo dos Produtos</h3>
    @if (!empty($productSummary))
        <ul>
            @foreach ($productSummary as $productName => $filialData)
                <li>
                    <strong>{{ $productName }}</strong>
                    <ul>
                        @foreach ($filialData as $filial => $stocks)
                            <li>
                                FILIAL {{ $filial }}:
                                Estoque Desossa: {{ $stocks['boning_stock'] }}kg,
                                Estoque Caixaria: {{ $stocks['cashier_stock'] }}kg
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    @else
        <p>Nenhum dado disponível.</p>
    @endif
</div>
