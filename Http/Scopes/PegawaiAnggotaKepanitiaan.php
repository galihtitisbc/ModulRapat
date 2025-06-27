<?php
namespace Modules\Rapat\Http\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Modules\Rapat\Http\Helper\RoleGroupHelper;

class PegawaiAnggotaKepanitiaan implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $pegawai = Auth::user()->username;
        $builder
            ->when(! RoleGroupHelper::userHasRoleGroup(Auth::user(), RoleGroupHelper::kepegawaianRoles()), function ($query) use ($pegawai) {
                return $query->where('pimpinan_username', $pegawai)
                    ->orWhereHas('pegawai', function ($query) use ($pegawai) {
                        $query->where('username', $pegawai);
                    });
            });

    }
}
