<?php

namespace App\Filament\Clusters\Products\Resources\ProductResource\Widgets;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Clusters\Products\Resources\ProductResource\Pages\ListProducts;

class ProductStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    public function getTablePage(): string
    {
        return ListProducts::class;
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Total Products', $this->getPageTableQuery()->count()),
            Stat::make('Product Inventory', $this->getPageTableQuery()->sum('qty')),
            Stat::make('Average price', number_format($this->getPageTableQuery()->avg('price'), 2)),
        ];
    }
}
