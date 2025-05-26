<?php
namespace Modules\Rapat\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RapatLampiran extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // protected static function newFactory()
    // {
    //     return \Modules\Rapat\Database\factories\RapatLampiranFactory::new();
    // }
    public function rapatAgenda()
    {
        return $this->belongsTo(RapatAgenda::class, 'rapat_agenda_id');
    }
}
