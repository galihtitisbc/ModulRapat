<?php
namespace Modules\Rapat\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Rapat\Http\Helper\RoleGroupHelper;

class PimpinanRapatMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user        = Auth::user();
        $agendaRapat = $request->rapatAgenda;
        if (RoleGroupHelper::userHasRoleGroup($user, RoleGroupHelper::pimpinanRapatRoles())) {
            return $next($request);
        }
        if (RoleGroupHelper::userHasRoleGroup($user, RoleGroupHelper::pimpinanRapatRoles()) && $agendaRapat->pegawai_id === $user->pegawai->username) {
            return $next($request);
        }
        if (Auth::user()->pegawai->ketuaPanitia->isNotEmpty()) {
            return $next($request);
        }
        abort(403);
    }
}
