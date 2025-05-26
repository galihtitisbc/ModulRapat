<?php

namespace Modules\Rapat\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RapatNotulen extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // protected static function newFactory()
    // {
    //     return \Modules\Rapat\Database\factories\RapatNotulenFactory::new();
    // }
    public function rapatAgenda()
    {
        return $this->belongsTo(RapatAgenda::class, 'rapat_agenda_id');
    }
    public function notulenFiles()
    {
        return $this->hasMany(RapatNotulenFile::class, 'rapat_notulen_id');
    }
}
