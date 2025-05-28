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
    public function getFormattedNameAttribute()
    {
        return
        ($this->gelar_dpn ? $this->gelar_dpn . ' ' : '') .
        ucwords(strtolower($this->nama)) .
            ($this->gelar_blk ? ', ' . $this->gelar_blk : '');

    }

    public function getRouteKeyName()
    {
        return 'username';
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }
    public function kepanitiaans()
    {
        return $this->belongsToMany(Kepanitiaan::class, 'kepanitiaan_pegawai', 'pegawai_username', 'kepanitiaan_id', 'username', 'id');
    }

    public function rapatAgendaPimpinan()
    {
        return $this->hasMany(Pegawai::class, 'pimpinan_username', 'username');
    }
    public function rapatAgendaNotulis()
    {
        return $this->hasMany(Pegawai::class, 'notulis_username', 'username');
    }
    public function rapatAgendaPeserta()
    {
        return $this->belongsToMany(RapatAgenda::class, 'rapat_pesertas', 'pegawai_username', 'rapat_agenda_id', 'username', 'id')->withPivot('status', 'is_penugasan');
    }
    public function rapatTindakLanjut()
    {
        return $this->hasMany(RapatTindakLanjut::class, 'pegawai_username', 'username');
    }
    public function ketuaPanitia()
    {
        return $this->hasMany(Kepanitiaan::class, 'pimpinan_username', 'username');
    }
}
