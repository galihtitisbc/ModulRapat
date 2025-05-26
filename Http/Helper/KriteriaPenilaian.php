<?php

namespace Modules\Rapat\Http\Helper;

enum KriteriaPenilaian: string
{
    case BELUM_DINILAI   = 'BELUM_DINILAI';
    case MELEBIHI_EKSPETASI = 'MELEBIHI_EKSPETASI';
    case SESUAI_EKSPETASI = 'SESUAI_EKSPETASI';
    case TIDAK_SESUAI_EKSPETASI = 'TIDAK_SESUAI_EKSPETASI';
    public function label(): string
    {
        return match ($this) {
            self::BELUM_DINILAI => 'Belum Dinilai',
            self::MELEBIHI_EKSPETASI => 'Melebihi Ekspektasi',
            self::SESUAI_EKSPETASI => 'Sesuai Ekspektasi',
            self::TIDAK_SESUAI_EKSPETASI => 'Tidak Sesuai Ekspektasi',
        };
    }
}
