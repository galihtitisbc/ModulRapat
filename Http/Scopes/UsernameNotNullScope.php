<?php
namespace Modules\Rapat\Http\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UsernameNotNullScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereNotNull('username')
            ->whereNotNull('status_karyawan')
            ->where('status_karyawan', '<>', '')
        // ->where('nip', '<>', '-')
            ->orderBy('nama', 'asc');
    }
}
