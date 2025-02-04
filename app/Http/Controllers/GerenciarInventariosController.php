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

    $resultCategorias = DB::select ("select distinct(tcm.id), tcm.id, tcm.nome from tipo_categoria_material tcm left join item_material im on (im.id_item_catalogo_material = tcm.id) order by tcm.nome");   

    $itemmaterial = DB::select ("select distinct(icm.nome), id, nome from item_catalogo_material icm order by nome"); 


    $resultItens = DB::table('item_material AS im')
                                    ->select('icm.nome', 'im.adquirido','im.adquirido', 'im.id as idmat', 'im.data_cadastro', 'v.data', 'tcm.nome AS ncat', 'im.valor_venda', DB::raw('count(*) as qtd'), DB::raw('sum(valor_venda) as total'))
                                    ->leftjoin('item_catalogo_material AS icm', 'im.id_item_catalogo_material','icm.id')
                                    ->leftJoin('tipo_categoria_material AS tcm', 'icm.id_categoria_material', 'tcm.id' )
                                    ->leftjoin('venda_item_material AS vim','im.id','vim.id_item_material')
                                    ->leftjoin('venda AS v','vim.id_venda', 'v.id')
                                    ->where(function ($query) use ($array_sessao) {
                                        $query->whereNull('im.id_deposito')
                                              ->orWhereIn('im.id_deposito', $array_sessao);
                                    })
                                    ->groupBy('icm.nome', 'im.adquirido', 'im.valor_venda', 'tcm.nome', 'im.adquirido', 'im.id', 'im.data_cadastro', 'v.data');


    $data = $request->data;
    $categoria = $request->categoria;
    $item = $request->item;
    $compra = $request->compra;

    //dd($data);

    if ($data !== null) {
        $resultItens->whereDate('im.data_cadastro', '<=', $data)
                    ->where(function ($query) use ($data) {
                        // Exclui itens vendidos antes da data registrada
                        $query->whereNull('v.data')
                              ->orWhereDate('v.data', '>=', $data);
                    });

    }

    if ($compra === 'null'){

        $resultItens->where(function($query) {
            $query->whereIn('im.adquirido', [true, false]) // Para booleanos
                  ->orWhereIn('im.adquirido', ['true', 'false']) // Para strings
                  ->orWhereIn('im.adquirido', [0, 1]); // Para inteiros
        });
    }
    else{

        $resultItens->where('im.adquirido', $request->compra);
    }

    if ($categoria !== null){

        $resultItens->where('icm.id_categoria_material', $categoria);
    }

    if ($compra === 'null'){

        $resultItens->where(function($query) {
            $query->whereIn('im.adquirido', [true, false]) // Para booleanos
                  ->orWhereIn('im.adquirido', ['true', 'false']) // Para strings
                  ->orWhereIn('im.adquirido', [0, 1]); // Para inteiros
        });
    }
    else{

        $resultItens->where('im.adquirido', $request->compra);

    }

    if ($item !== null){
        $resultItens->where('icm.id', $item);
    }

    $resultData = $resultItens->get();
    
    $total_itens = $resultData->sum('qtd');

    $total_soma = $resultData->sum('total');

    $resultItens = $resultItens->orderBy('tcm.nome', 'asc', 'icm.nome', 'asc')->paginate(100);

    //dd($resultItens);





    return view('relatorios/inventarios', compact('nr_ordem', 'compra', 'categoria', 'item', 'data', 'resultCategorias', 'resultItens', 'total_itens', 'total_soma', 'itemmaterial'));

    }

}
