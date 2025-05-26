<?php

namespace Modules\Rapat\Http\Helper;

enum StatusPesertaRapat: string
{
    case BERSEDIA = 'BERSEDIA';
    case TIDAK_BERSEDIA = 'TIDAK_BERSEDIA';
    case HADIR = 'HADIR';
    case TIDAK_HADIR = 'TIDAK_HADIR';
    case MENUNGGU = 'MENUNGGU';

    public function label(): string
    {
        return match ($this) {
            self::BERSEDIA => 'Bersedia',
            self::TIDAK_BERSEDIA => 'Tidak Bersedia',
            self::HADIR => 'Hadir',
            self::TIDAK_HADIR => 'Tidak Hadir',
            self::MENUNGGU => 'Menunggu',
        };
    }
}
