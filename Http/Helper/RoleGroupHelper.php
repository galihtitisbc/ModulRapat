<?php
namespace Modules\Rapat\Http\Helper;

class RoleGroupHelper
{
    public static function pimpinanRoles(): array
    {
        return ['direktur', 'wadir1', 'wadir2', 'wadir3'];
    }

    public static function pimpinanRapatRoles(): array
    {
        return ['direktur', 'wadir1', 'wadir2', 'wadir3', 'sekjur', 'kaprodi', 'kajur'];
    }
    public static function kepegawaianRoles(): array
    {
        return ['kepegawaian'];
    }

    public static function userHasRoleGroup($user, array $roleGroup): bool
    {
        return in_array($user->role_aktif, $roleGroup);
    }
}
