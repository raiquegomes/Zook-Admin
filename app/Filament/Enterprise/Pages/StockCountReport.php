<?php
namespace App\Filament\Enterprise\Pages;

use Filament\Pages\Page;
use App\Models\StockCount;

class StockCountReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Relatório de Contagem';
    protected static ?string $navigationGroup = 'Relatórios';
    protected static ?string $title = 'Relatório de Contagem';

    public $date;
    public $selectedFilials = [];
    public $reportData = null;
    public $type;

    protected static string $view = 'filament.enterprise.pages.stock-count-report';

    public function mount()
    {
        $this->date = now()->toDateString();
        $this->selectedFilials = [];
        $this->reportData = null;
    }

    public function generateReport()
    {
        // Validar campos obrigatórios
        if (empty($this->selectedFilials) || empty($this->date)) {
            session()->flash('error', 'Selecione a data e as filiais para gerar o relatório.');
            return;
        }

        // Buscar registros com base na data e filiais selecionadas
        $this->reportData = StockCount::whereDate('date', $this->date)
            ->whereIn('filial_id', $this->selectedFilials)
            ->with('products', 'filial')
            ->get()
            ->groupBy('filial_id')
            ->map(function ($records, $filialId) {
                $productsSummary = [];

                foreach ($records as $record) {
                    foreach ($record->products as $product) {
                        $name = $product['name'];
                        $boningStock = $product['boning_stock'];
                        $cashierStock = $product['cashier_stock'];
                        $quality = $product['quality'] ?? '-'; // Adiciona a qualidade do produto

                        if (!isset($productsSummary[$name])) {
                            $productsSummary[$name] = [
                                'boning_stock' => 0,
                                'cashier_stock' => 0,
                                'quality' => $quality, // Armazena a qualidade
                            ];
                        }

                        $productsSummary[$name]['boning_stock'] += $boningStock;
                        $productsSummary[$name]['cashier_stock'] += $cashierStock;
                    }
                }

                return [
                    'filial_name' => $records->first()->filial->name ?? 'Desconhecida',
                    'products' => $productsSummary,
                ];
            });
    }

    protected function getFormSchema(): array
    {
        return [
            \Filament\Forms\Components\Grid::make(2)
            ->schema([
                \Filament\Forms\Components\DatePicker::make('date')
                    ->label('Data')
                    ->required(),

                \Filament\Forms\Components\Select::make('type')
                    ->label('Setor')
                    ->options([
                        'acougue' => 'Açougue',
                        'hortifruti' => 'Hortifruti',
                    ])
                    ->required(),
            ]),

            \Filament\Forms\Components\Select::make('selectedFilials')
                ->label('Filiais')
                ->multiple()
                ->options(fn () => \App\Models\Filial::pluck('name', 'id'))
                ->required(),
        ];
    }
}
