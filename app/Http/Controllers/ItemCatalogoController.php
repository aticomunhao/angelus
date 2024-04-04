<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ModelItemCatalogo;
use App\Models\ModelCatMaterial;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ItemCatalogoController extends Controller
{

    private $objItemCatalogo;
    private $objTipoMaterial;

    public function __construct(){
        $this->objItemCatalogo = new ModelItemCatalogo();
        $this->objTipoMaterial = new ModelCatMaterial();
    }

    private function getListaItemMatAll(){
        $lista = DB::select("
            select
                i.id,
                i.nome,
                c.nome nome_categoria,
                i.valor_minimo,
                i.valor_medio,
                i.valor_maximo,
                i.valor_marca,
                i.valor_etiqueta,
                i.composicao,
                i.ativo
            from item_catalogo_material i
            left join tipo_categoria_material c on i.id_categoria_material =c.id
        ");
        return $lista;
    }

    public function index()
    {
        $result= $this->getListaItemMatAll();
        return view('catalogo/gerenciar-item-catalogo',['result'=>$result]);
    }


    public function create()
    {
        $resultCategoria = $this->objTipoMaterial->all();
        return view('catalogo/incluir-item-catalogo', compact('resultCategoria'));
    }


    public function store(Request $request)   
    {

        // $validator = Validator::make($request->all(), [
        //     'nome' => 'required|unique:item_catalogo_material',
        //     // Outras regras de validação, se necessário
        // ]);
    
        // dd($validator);
        // // Verifica se a validação falhou
        // if ($validator->fails()) {
        //     return redirect('gerenciar-item-catalogo')
        //                 ->withErrors($validator)
        //                 ->withInput();
        // }
        $nome_item = $request->nome_item;
        $categoria_item = $request->categoria_item;

       $verifica = DB::table('item_catalogo_material AS icm')
                        ->where('nome', $nome_item)
                        ->where('id_categoria_material', $categoria_item)
                        ->count();

       if ($verifica < 1){

        $ativo = isset($request->ativo) ? 1 : 0;
        $composicao = isset($request->composicao) ? 1 : 0;


        DB::table('item_catalogo_material')->insert([
            'nome' => $request->input('nome_item'),
            'id_categoria_material' => $request->input('categoria_item'),
            'valor_minimo' => $request->input('val_minimo'),
            'valor_medio' => $request->input('val_medio'),
            'valor_maximo' => $request->input('val_maximo'),
            'valor_marca' => $request->input('val_marca'),
            'valor_etiqueta' => $request->input('val_etiqueta'),
            'composicao' => $composicao,
            'ativo' => $ativo,
        ]);

        $result= $result= $this->getListaItemMatAll();

        return redirect()
                    ->action('ItemCatalogoController@index')
                    ->with('message', 'O nome foi inserido no catálogo com sucesso!');  

        return view('catalogo/gerenciar-item-catalogo',['result'=>$result]);

        }elseif($verifica > 0){


            return redirect()
                    ->back()
                    ->with('danger', 'Este nome está duplicado!');            

        }

       
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $resultCategoria = $this->objTipoMaterial->all();
        $result =DB::table('item_catalogo_material')->where('id',$id)->get();
        return view('catalogo/editar-item-catalogo', compact('resultCategoria', 'result'));
    }


    public function update(Request $request, $id)
    {

        
        $ativo = isset($request->ativo) ? 1 : 0;
        $composicao = isset($request->composicao) ? 1 : 0;

        DB::table('item_catalogo_material')
            ->where('id', $id)
            ->update([
                'nome' => $request->input('nome_item'),
                'id_categoria_material' => $request->input('categoria_item'),
                'valor_minimo' => $request->input('val_minimo'),
                'valor_medio' => $request->input('val_medio'),
                'valor_maximo' => $request->input('val_maximo'),
                'valor_marca' => $request->input('val_marca'),
                'valor_etiqueta' => $request->input('val_etiqueta'),
                'composicao' => $composicao,
                'ativo' => $ativo,
            ]);

        $result= $result= $this->getListaItemMatAll();
        return view('catalogo/gerenciar-item-catalogo', ['result'=>$result]);

    }

    public function destroy($id)
    {

            DB::delete('delete from item_catalogo_material where id = ?' , [$id]);

            $result= $result= $this->getListaItemMatAll();

            return view('catalogo/gerenciar-item-catalogo', ['result'=>$result]);

    }

//    public function messages() {
  //      return [     'required' => 'O campo :attribute é requerido',
    //         'description.min' => 'O campo :attribute deve ter no mínimo 3 caracteres',
      //            'expiration_time.min' => 'O campo :attribute deve ser a quantidade de dias',
        //               'price.regex' => 'O campo :attribute deve conter um valor monetário. Ex.: 2.25',   ]; }
        //Para funcionar utilize nome do campo.nome da regra


}
