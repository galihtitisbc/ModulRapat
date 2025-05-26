<?php

namespace Modules\Rapat\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RapatDokumentasi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // protected static function newFactory()
    // {
    //     return \Modules\Rapat\Database\factories\RapatDokumentasiFactory::new();
    // }
    public function rapatAgenda()
    {
        return $this->belongsTo(RapatAgenda::class, 'rapat_agenda_id');
    }
}
