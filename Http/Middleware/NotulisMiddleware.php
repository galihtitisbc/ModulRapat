<?php
namespace Modules\Rapat\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotulisMiddleware
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
        $pegawai     = Auth::user()->pegawai;
        $agendaRapat = $request->rapatAgenda;
        if ($agendaRapat && ($agendaRapat->notulis_username === $pegawai->username) || $agendaRapat && ($agendaRapat->pimpinan_username === $pegawai->username)) {
            return $next($request);
        }
        abort(403);
    }
}
