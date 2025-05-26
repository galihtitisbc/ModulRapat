<?php

namespace Modules\Rapat\Http\Helper;

enum StatusTindakLanjut: string
{
    case SELESAI           = 'SELESAI';
    case BELUM_SELESAI     = 'BELUM_SELESAI';
    public function label(): string
    {
        return match ($this) {
            self::SELESAI => 'Selesai',
            self::BELUM_SELESAI => 'BELUM SELESAI',
        };
    }
}
