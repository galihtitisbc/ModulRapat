<?php

namespace Modules\Rapat\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RapatTindakLanjutFile extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // protected static function newFactory()
    // {
    //     return \Modules\Rapat\Database\factories\RapatTindakLanjutFileFactory::new();
    // }

    public function rapatTindakLanjut()
    {
        return $this->belongsTo(RapatTindakLanjut::class, 'rapat_tindak_lanjut_id');
    }
}
