<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ModelSexo;
use Illuminate\Support\Facades\DB;

class SexoController extends Controller
{

    private $objSexo;
    
    public function __construct(){
        $this->objSexo = new ModelSexo();        
    }

    public function index()
    {

        $result= $this->objSexo->all();        
        return view('/cadastro-geral/cad-sexo',['result'=>$result]);
    }

    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $sexo = $request->input('sexo');
        $siglaSexo = $request->input('siglaSexo');        
        
        DB::insert('insert into tipo_sexo (nome,sigla) values (?,?)', [$sexo,$siglaSexo]);
        
        $result= $this->objSexo->all();
        return view('/cadastro-geral/cad-sexo',['result'=>$result]);
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $resultSexo = DB::select("select id, nome, sigla from tipo_sexo where id=$id");

        return view('/cadastro-geral/alterar-sexo', compact("resultSexo"));
    }


    public function update(Request $request, $id)
    {
        DB::table('tipo_sexo')
        ->where('id', $id)
        ->update([
            'nome' => $request->input('sexo'),
            'sigla' => $request->input('siglaSexo'),
        ]);

        return redirect()->action('SexoController@index');
    }

    public function destroy($id)
    {
       $deleted = DB::delete('delete from tipo_sexo where id =?' , [$id]);
       $result= $this->objSexo->all();
       return view('/cadastro-geral/cad-sexo',['result'=>$result]);
    }
}
