<?php

namespace App\Filament\Exports;

use App\Models\Activity;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ActivityExporter extends Exporter
{
    protected static ?string $model = Activity::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('title')->label('Titulo'),
            ExportColumn::make('description')->label('Descrição'),
            ExportColumn::make('frequency')->label('Frequência'),
            ExportColumn::make('order_number')->label('Ordem N°'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'A exportação da sua atividade foi concluída e ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exportado.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' não foi possível exportar.';
        }

        return $body;
    }
}
