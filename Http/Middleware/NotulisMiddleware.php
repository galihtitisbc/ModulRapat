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
        if ($agendaRapat && ($agendaRapat->notulis_id === $pegawai->username) || $agendaRapat && ($agendaRapat->pimpinan_id === $pegawai->username)) {
            return $next($request);
        }
        abort(403);
    }
}
