<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\ModelItemMaterial;
use DateTimeImmutable;
use PhpParser\Node\Stmt\Foreach_;
use Symfony\Component\VarDumper\Cloner\Data;
use Illuminate\Support\Carbon;


class GerenciarInventariosController extends Controller{

    public function index(Request $request){

    //$request->session()->put('data', "2022-03-10");
    $sessao = session()->get('usuario.depositos');

    $array_sessao = explode(",", $sessao);

    $data_atual = (\Carbon\carbon::now()->toDateTimeString());

    $nr_ordem = 1;

    $vendas = DB::select ('select id_item_material from venda_item_material');

    $resultCategorias = DB::select ("select distinct(tcm.id), tcm.id, tcm.nome from tipo_categoria_material tcm left join item_material im on (im.id_item_catalogo_material = tcm.id) where im.id_deposito = " . $sessao );   

    $itemmaterial = DB::select ("select distinct(icm.nome), id, nome from item_catalogo_material icm order by nome"); 


    $resultItens = DB::table('item_material')
                                    ->select('item_material.id', 'venda.data', 'item_material.data_cadastro', 'item_catalogo_material.nome', 'item_material.valor_venda', DB::raw('count(*) as qtd'), DB::raw('sum(valor_venda) as total'), DB::raw('count(*) as qtd'))
                                    ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material','=','item_catalogo_material.id')
                                    ->leftjoin('venda_item_material','item_material.id','venda_item_material.id_item_material')
                                    ->leftjoin('venda','venda_item_material.id_venda', 'venda.id')
                                    ->where('item_material.id_deposito', $array_sessao)
                                    ->groupBy('item_material.id','venda.data', 'item_material.data_cadastro', 'item_catalogo_material.nome', 'item_material.valor_venda');


    $data = $request->data;

    $categoria = $request->categoria;

    if ($request->data){
        $resultItens->where('item_material.data_cadastro','<=', $request->data)
        ->Where('venda.data','>=', $request->data)
        ->orWhere('venda.data','<=', $request->data)
        ->whereNull('venda.data');
    }

    if ($request->categoria){
        $resultItens->where('item_catalogo_material.id_categoria_material', $request->categoria);
    }

    $resultItens = $resultItens->get();

    //dd($resultItens);

    $total_itens = $resultItens->sum('qtd');

    $total_soma = $resultItens->sum('total');



    return view('relatorios/inventarios', compact('nr_ordem', 'data', 'resultCategorias', 'resultItens', 'total_itens', 'total_soma', 'itemmaterial'));

    }

}
