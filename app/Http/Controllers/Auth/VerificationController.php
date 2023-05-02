<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Session;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */
    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */


    public function validar (Request $request){

        $email = $request->input('email');

        $result=DB::select("
                        select
                        u.id id_usuario,
                        p.id id_pessoa,
                        p.cpf,
                        p.nome,
                        u.hash_senha,
                        string_agg(distinct u_p.id_tp_perfil::text, ',') perfis,
                        string_agg(distinct u_d.id_deposito::text, ',') depositos
                        from usuario u
                        left join pessoa p on u.id_pessoa = p.id
                        left join usuario_perfil u_p on u.id = u_p.id_usuario
                        left join usuario_deposito u_d on u.id = u_d.id_usuario
                        where u.ativo is true and p.email ='$email'
                        group by u.id, p.id
                        ");

        // dd($result);

        if (count($result)>0){

            echo "Send mail";

        }else{

            return view('login/login')->withErrors(['Credenciais inválidas']);

        }


    }

}
