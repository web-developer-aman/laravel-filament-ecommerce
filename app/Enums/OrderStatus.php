<?php 

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasColor, HasIcon, HasLabel
{
    case New = 'new';
    case Processing = 'processing';
    case Shipped ='shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function getColor(): string | array | null
    {
        return match ($this) {
             self::New => 'info',
             self::Processing => 'warning',
             self::Shipped,self::Delivered => 'success',
             self::Cancelled => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
             self::New => 'heroicon-m-sparkles',
             self::Processing => 'heroicon-m-arrow-path',
             self::Shipped => 'heroicon-m-truck',
             self::Delivered => 'heroicon-m-check-badge',
             self::Cancelled => 'heroicon-m-x-circle',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Processing => 'processing',
            self::Delivered => 'delivered',
            self::Shipped =>'shipped',
            self::Cancelled => 'cancelled',
        };
    }
}

