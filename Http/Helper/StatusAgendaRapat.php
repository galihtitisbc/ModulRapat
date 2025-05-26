<?php
namespace Modules\Rapat\Http\Helper;

enum StatusAgendaRapat: string {
    case STARTED   = 'STARTED';
    case CANCELLED = 'CANCELED';
    case SCHEDULED = 'SCHEDULED';
    case COMPLETED = 'COMPLETED';

    public function label(): string
    {
        return match ($this) {
            self::STARTED => 'Sedang Berlangsung',
            self::CANCELLED => 'Dibatalkan',
            self::SCHEDULED => 'Di Jadwalkan',
            self::COMPLETED => 'Selesai',
        };
    }
}
