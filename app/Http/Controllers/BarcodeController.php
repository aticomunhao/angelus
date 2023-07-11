<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class BarcodeController extends Controller

{
   public function show($id)
    {
     //$itens = DB::table ('item_material')->get();
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
     
     where im.id = $id");
     //return view ('item_material', ['item_material' => $itens]);
     return view ('item_material', compact('itens'));
    }


    public function index ()
    {

        $result = DB::select ("
        select
        m.id,
        m.valor_venda,
        m.data_cadastro,
        c.nome
        from item_material m
        left join item_catalogo_material c on (c.id = m.id_item_catalogo_material)
        ");


       // dd($result);


        return view('/barcode', compact('result'));
     //return view ('barcode', ['result' => $result]);

    }

}
