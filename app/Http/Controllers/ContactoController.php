<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;


class ContactoController extends Controller
{
    public function send(Request $r){
        Log::info(__FILE__.'/'.__FUNCTION__); Log::info($r);  $rta['cod'] = 500; $rta['msg'] = "Ocurrio un error de proceso, por favor intente mas tarde";

        request()->validate([
            'nombre' => 'required',
            'correo' => 'required|email',
            'mensaje' => 'required',
            'g-recaptcha-response' => 'required|captcha'
        ]);
        $asunto = 'OrientalPY App - Contacto';
        $asunto .= 'Nombre: '.$r->nombre.' Correo: '.$r->correo;
        $asunto .= (empty($r->telefono)) ? '' : ' Telefono: '.$r->telefono;
        $html = $r->mensaje;

        Mail::send([], [], function ($mail) use ($r, $html, $asunto) {
            $mail
                ->from(env('MAIL_USERNAME'), 'OrientalPY')
                ->to(env('MAIL_TO'), 'market.orientalpy@gmail.com')
                ->subject($asunto)
                ->setBody($html, 'text/html');

            });
            if(count(Mail::failures()) > 0){
                $envio = false;
                Log::error('Ocurrio un error al intentar enviar el correo');
            }else{
                $envio = true;
                Log::info('Fin del proceso, se envio correo al usuario con su nueva clave exitosamente!');
            }
            return 'Gracias por tu mensaje, nos pondremos en contacto en la brevedad posible';
            // return redirect('/contacto')->route(['envio' => $envio]);
            // return View::make('contacto')->with('envio', $envio);

    }
}
