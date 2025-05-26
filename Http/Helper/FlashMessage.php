<?php
namespace Modules\Rapat\Http\Helper;

class FlashMessage
{
    public static function success($message)
    {
        session()->flash('swal', [
            'title' => 'Berhasil',
            'text'  => $message,
            'icon'  => 'success',
        ]);

    }

    public static function error($message)
    {
        session()->flash('swal', [
            'title' => 'Gagal',
            'text'  => $message,
            'icon'  => 'error',
        ]);
    }
}
