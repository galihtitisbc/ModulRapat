<?php
namespace Modules\Rapat\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Rapat\Http\Helper\RoleGroupHelper;

class KepegawaianMiddleware
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
        $user = Auth::user();
        if (! RoleGroupHelper::userHasRoleGroup($user, RoleGroupHelper::kepegawaianRoles())) {
            abort(403);
        }
        return $next($request);
    }
}
