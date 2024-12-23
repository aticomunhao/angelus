<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class BarcodeController extends Controller

{
   public function show($id)
    {
     //$itens = DB::table ('item_material')->get();

     $sessao = session()->get('usuario.depositos');

    $array_sessao = explode(",", $sessao);

     $itens = DB::select ("
     select
     im.id,
     im.valor_venda,
     c.nome as nomei,
     im.observacao,
     m.nome as nomem
     from item_material im
     left join item_catalogo_material c on (c.id = im.id_item_catalogo_material)
     left join marca m on (im.id_marca = m.id)
     where im.id = $id
     and id_deposito IN (" . implode(",", $array_sessao) . ")");
     //return view ('item_material', ['item_material' => $itens]);
     return view ('item_material', compact('itens'));
    }


    public function index (Request $request)
    {

            $sessao = session()->get('usuario.depositos');

            $array_sessao = explode(",", $sessao);


            $resultCat = DB::select ('select id, nome from tipo_categoria_material');
               
            $lista = DB::table('item_material AS im')
            ->select('im.data_cadastro','im.id AS id_item', 'im.ref_fabricante AS ref_fab', 'icm.nome AS n1','tcm.nome AS n5', 'im.observacao AS obs', 'im.valor_venda','m.nome AS n2', 't.nome AS n3', 'c.nome AS n4', 'im.valor_venda', 'im.adquirido', 'tcm.id AS id_cat','tcm.nome AS nome_cat', 'im.id_tipo_situacao', 'icm.id_categoria_material AS cat')
            ->where('id_tipo_situacao', 1)
            ->leftjoin('item_catalogo_material AS icm', 'icm.id' , 'im.id_item_catalogo_material')
            ->leftjoin('tipo_categoria_material AS tcm', 'icm.id_categoria_material' , 'tcm.id')
            ->leftjoin('marca AS m', 'm.id' , 'im.id_marca')
            ->leftjoin('tamanho AS t', 't.id' , 'im.id_tamanho')
            ->leftjoin('cor AS c', 'c.id', 'im.id_cor')
            ->where(function ($query) use ($array_sessao) {
                $query->whereNull('im.id_deposito')
                      ->orWhereIn('im.id_deposito', $array_sessao);
            });



            $data_inicio = $request->data_inicio;
            $data_fim = $request->data_fim;
            $compra = $request->compra;

            if ($request->data_inicio){

            $lista->where('im.data_cadastro','>=' , $request->data_inicio);
            }
            if ($request->data_fim){
            $lista->where('im.data_cadastro','<=' , $request->data_fim);
            }

            $material = $request->material;
            if ($request->material){
            $lista->where('icm.nome', 'ilike', "%$request->material%");
            }

            $obs = $request->obs;
            if ($request->obs){
            $lista->where('im.observacao', 'ilike', "%$request->obs%");
            }

            $ref_fab = $request->ref_fab;
            if ($request->ref_fab){
            $lista->where('im.ref_fabricante', '=', $request->ref_fab);
            }

           // dd($request->$compra === 'null');

            if ($request->$compra === null){

                $lista->where(function($query) {
                    $query->whereIn('im.adquirido', [true, false]) // Para booleanos
                          ->orWhereIn('im.adquirido', ['true', 'false']) // Para strings
                          ->orWhereIn('im.adquirido', [0, 1]); // Para inteiros
                });
            }
            else{
    
                $lista->where('im.adquirido', $request->compra);
    
            }

            $categoria = $request->categoria;
            if ($request->categoria){
            $lista->where('tcm.id', '=', "$request->categoria");
            }

            $total = $request->compra;
            if ($request->compra){
            $lista->where('im.adquirido', '=', "$request->compra");
            }


            $lista = $lista->get();

 

        return view('/barcode', compact('lista'));
    

    }

}
