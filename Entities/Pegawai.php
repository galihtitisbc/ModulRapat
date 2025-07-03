<?php
namespace Modules\Rapat\Entities;

use App\Models\Core\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Rapat\Http\Scopes\UsernameNotNullScope;

class Pegawai extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected static function booted()
    {
        static::addGlobalScope(new UsernameNotNullScope);
    }
    public function getRouteKeyName()
    {
        return 'username';
    }
    public function getFormattedNameAttribute()
    {
        return
        ($this->gelar_dpn ? $this->gelar_dpn . ' ' : '') .
        ucwords(strtolower($this->nama)) .
            ($this->gelar_blk ? ', ' . $this->gelar_blk : '');

    }
    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }
    public function kepanitiaans()
    {
        return $this->belongsToMany(Kepanitiaan::class, 'kepanitiaan_pegawai', 'pegawai_id', 'kepanitiaan_id', 'id', 'id');
    }

    public function rapatAgendaPimpinan()
    {
        return $this->hasMany(Pegawai::class, 'pimpinan_id', 'id');
    }
    public function rapatAgendaNotulis()
    {
        return $this->hasMany(Pegawai::class, 'notulis_id', 'id');
    }
    public function rapatAgendaPeserta()
    {
        return $this->belongsToMany(RapatAgenda::class, 'rapat_pesertas', 'pegawai_id', 'rapat_agenda_id', 'id', 'id')->withPivot('status', 'is_penugasan');
    }
    public function rapatTindakLanjut()
    {
        return $this->hasMany(RapatTindakLanjut::class, 'pegawai_id', 'id');
    }
    public function ketuaPanitia()
    {
        return $this->hasMany(Kepanitiaan::class, 'pimpinan_id', 'id');
    }
}
