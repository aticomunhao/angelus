<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ModelVendas;
use App\Models\ModelPagamentos;
use App\Models\ModelItemMaterial;

class RelatoriosController extends Controller
{


    public function index(Request $request)
    {

        $sessao = session()->get('usuario.depositos');

        $array_sessao = explode(",", $sessao);

        //dd($array_sessao);
        //AQUI TODAS AS REGRAS DE FILTROS DE PESQUISA

        $rela = ModelVendas::select('venda.id_tp_situacao_venda', 'item_material.valor_venda', 'venda.data', 'item_material.id_deposito', 'venda.id as idv', 'pessoa.nome as nomep', 'item_material.adquirido', DB::raw('sum(item_material.valor_venda) as soma_orig'), DB::raw('sum(floor(item_material.valor_venda * item_material.valor_venda_promocional)) as desconto'), DB::raw('sum(item_material.valor_venda) as vlr_original'), DB::raw('(sum(item_material.valor_venda)) - (sum(floor(item_material.valor_venda * item_material.valor_venda_promocional))) as vlr_final'))
                                ->leftJoin('venda_item_material', 'venda.id', 'venda_item_material.id_venda')
                                ->leftJoin('item_material', 'venda_item_material.id_item_material', 'item_material.id')
                                ->leftJoin('pessoa', 'venda.id_pessoa', 'pessoa.id')                                
                                ->where('venda.id_tp_situacao_venda', '3')
                                //->where('item_material.valor_venda', '>', 0)
                                ->where(function ($query) use ($array_sessao) {
                                    $query->whereNull('item_material.id_deposito')
                                          ->orWhereIn('item_material.id_deposito', $array_sessao);
                                })
                                ->groupby('item_material.valor_venda','venda.id_tp_situacao_venda','venda.data','venda.id', 'item_material.id_deposito', 'pessoa.nome', 'item_material.adquirido');


        $relb = ModelPagamentos::select('venda.id_tp_situacao_venda', 'venda.data', 'item_material.id_deposito', 'pagamento.id as pid', 'item_material.adquirido', 'tipo_pagamento.id as tpid', 'tipo_pagamento.nome as nomepg', 'pagamento.valor as valor_p', 'pagamento.id_venda', 'item_material.adquirido')
                            ->leftJoin('venda', 'pagamento.id_venda', 'venda.id')
                            ->leftJoin('tipo_pagamento', 'pagamento.id_tipo_pagamento', 'tipo_pagamento.id')
                            ->leftJoin('venda_item_material', 'venda.id', 'venda_item_material.id_venda')
                            ->leftJoin('item_material', 'venda_item_material.id_item_material', 'item_material.id')
                            ->where('venda.id_tp_situacao_venda', '3')
                            ->where(function ($query) use ($array_sessao) {
                                $query->whereNull('item_material.id_deposito')
                                      ->orWhereIn('item_material.id_deposito', $array_sessao);
                            })
                            ->groupby('venda.id_tp_situacao_venda', 'venda.data', 'item_material.id_deposito', 'pagamento.id', 'tipo_pagamento.id', 'tipo_pagamento.nome', 'pagamento.valor', 'pagamento.id_venda', 'item_material.adquirido');



        $data_inicio = $request->data_inicio;
        $data_fim = $request->data_fim;
        $compra = $request->compra;
        $dep = $request->deposito;



        if ($request->data_inicio){

            $rela->whereDate('venda.data','>=' , $request->data_inicio);

            $relb->whereDate('venda.data','>=' , $request->data_inicio);

        }

        if ($request->data_fim){

            $rela->whereDate('venda.data','<=' , $request->data_fim);

            $relb->whereDate('venda.data','<=' , $request->data_fim);

        }

        if ($request->compra !== null){

            $rela->where('item_material.adquirido', $request->compra);

            $relb->where('item_material.adquirido', $request->compra);
        }
        elseif ($request->compra === null){

            $rela->whereIn('item_material.adquirido', [true, false]);

            $relb->whereIn('item_material.adquirido', [true, false]);
        }

        if ($request->deposito){

            $rela->where('item_material.id_deposito', $request->deposito);

            $relb->where('item_material.id_deposito', $request->deposito);

        }


       




        //dd($total_desconto);
        $rela = $rela->get();

        $original = $rela->sum('vlr_original');
        $desconto = floatval($rela->sum('desconto'));
        
        $soma_origem = $rela->sum('soma_orig');
        $total1 = ($original - $desconto);
        $total_desconto = $rela->sum('desconto');


        
        $relb = $relb->get();
     
        $total_din = $relb->where('tpid', '1')->sum('valor_p');
        $total_deb = $relb->where('tpid', '2')->sum('valor_p');
        $total_cre = $relb->where('tpid', '3')->sum('valor_p');
        $total_che = $relb->where('tpid', '4')->sum('valor_p');
        $total_pix = $relb->where('tpid', '5')->sum('valor_p');

        $total_pag = ($relb->sum("valor_p"));



        //dd($total_cre);
        $deposito = DB::table('deposito')->select('id as iddep', 'nome')->whereIn('id', $array_sessao)->get();

        $compra = $request->input('compra');

        //dd($deposito);

        return view('relatorios/relatorio-vendas', compact('compra', 'deposito', 'soma_origem', 'total_pag', 'data_fim', 'data_inicio', 'rela', 'relb', 'total_din', 'total_deb', 'total_cre', 'total_che', 'total_pix', 'total1', 'total_desconto'));

    }

    public function venda_cliente(Request $request)
    {

        $sessao = session()->get('usuario.depositos');

        $array_sessao = explode(",", $sessao);

        //dd($array_sessao);
        //AQUI TODAS AS REGRAS DE FILTROS DE PESQUISA

        $rela = DB::table('venda AS v')->select('item_material.valor_venda', 'v.data', 'item_material.id_deposito', 'v.id as idv', 'pessoa.nome as nomep', 'item_material.adquirido', DB::raw('sum(item_material.valor_venda) as soma_orig'), DB::raw('sum(floor(item_material.valor_venda * item_material.valor_venda_promocional)) as desconto'), DB::raw('sum(item_material.valor_venda) as vlr_original'), DB::raw('sum(item_material.valor_venda) - sum(floor(item_material.valor_venda * item_material.valor_venda_promocional)) as vlr_final'))
                                ->leftJoin('venda_item_material', 'v.id', 'venda_item_material.id_venda')
                                ->leftJoin('item_material', 'venda_item_material.id_item_material', 'item_material.id')
                                ->leftJoin('pessoa', 'v.id_pessoa', 'pessoa.id')                                
                                ->where('v.id_tp_situacao_venda', 3)
                                ->where(function ($query) use ($array_sessao) {
                                    $query->whereNull('item_material.id_deposito')
                                          ->orWhereIn('item_material.id_deposito', $array_sessao);
                                })
                                ->groupby('item_material.valor_venda', 'v.data', 'item_material.id_deposito', 'v.id', 'pessoa.nome', 'item_material.adquirido');
        

        $relb =  DB::table('pagamento')
        ->select('tipo_pagamento.id as tpid', 'tipo_pagamento.nome as nomepg', 'pagamento.valor as valor_p')
                            ->leftJoin('venda', 'pagamento.id_venda', 'venda.id')
                            ->leftJoin('tipo_pagamento', 'pagamento.id_tipo_pagamento', 'tipo_pagamento.id')
                            ->leftJoin('venda_item_material', 'venda.id', 'venda_item_material.id_venda')
                            ->leftJoin('item_material', 'venda_item_material.id_item_material', 'item_material.id')
                            ->where('venda.id_tp_situacao_venda', 3)
                            ->where(function ($query) use ($array_sessao) {
                                $query->whereNull('item_material.id_deposito')
                                      ->orWhereIn('item_material.id_deposito', $array_sessao);
                            })
                            ->groupby('tipo_pagamento.id','tipo_pagamento.nome', 'pagamento.valor');



        $data_inicio = $request->data_inicio;
        $data_fim = $request->data_fim;
        $compra = $request->compra;
        $dep = $request->deposito;



        if ($request->data_inicio){

            $rela->whereDate('v.data','>=' , $request->data_inicio);

            $relb->whereDate('venda.data','>=' , $request->data_inicio);

        }

        if ($request->data_fim){

            $rela->whereDate('v.data','<=' , $request->data_fim);

            $relb->whereDate('venda.data','<=' , $request->data_fim);

        }

        if ($compra === 'null'){

            $rela->where(function($query) {
                $query->whereIn('item_material.adquirido', [true, false]) // Para booleanos
                      ->orWhereIn('item_material.adquirido', ['true', 'false']) // Para strings
                      ->orWhereIn('item_material.adquirido', [0, 1]); // Para inteiros
            });

            $relb->where(function($query) {
                $query->whereIn('item_material.adquirido', [true, false]) // Para booleanos
                      ->orWhereIn('item_material.adquirido', ['true', 'false']) // Para strings
                      ->orWhereIn('item_material.adquirido', [0, 1]); // Para inteiros
            });
        }
        else{

            $rela->where('item_material.adquirido', $request->compra);

            $relb->where('item_material.adquirido', $request->compra);
        }


        if ($request->deposito !== null){

            $rela->where('item_material.id_deposito', $request->deposito);

            $relb->where('item_material.id_deposito', $request->deposito);
        }
        elseif ($request->deposito == null){

            $rela->whereIn('item_material.id_deposito', [1, 2]);

            $relb->whereIn('item_material.id_deposito', [1, 2]);
        }

        $lista = $rela->where('v.id_tp_situacao_venda', '3')->get();

        $original = floatval( $lista->sum('vlr_original'));
        $desconto = floatval($lista->sum('desconto'));
        
        $soma_origem = floatval($lista->sum('vlr_original'));
        $total1 = floatval($lista->sum('vlr_final'));
        $total_desconto = floatval($lista->sum('desconto'));


        $rela = $rela->where('v.id_tp_situacao_venda', '3')->paginate(50);

        $relb = $relb->where('venda.id_tp_situacao_venda', '3')->get();
        
        
        
        
        $total_din = $relb->where('tpid', '1')->sum('valor_p');
        $total_deb = $relb->where('tpid', '2')->sum('valor_p');
        $total_cre = $relb->where('tpid', '3')->sum('valor_p');
        $total_che = $relb->where('tpid', '4')->sum('valor_p');
        $total_pix = $relb->where('tpid', '5')->sum('valor_p');

        $total_pag = ($relb->sum("valor_p"));



        //dd($total_pag);

        $deposito = DB::table('deposito')->select('id as iddep', 'nome')->whereIn('id', $array_sessao)->get();

        $compra = $request->input('compra');

        //dd($deposito);

        return view('relatorios/vendas-cliente', compact('compra', 'deposito', 'soma_origem', 'total_pag', 'data_fim', 'data_inicio', 'rela', 'relb', 'total_din', 'total_deb', 'total_cre', 'total_che', 'total_pix', 'total1', 'total_desconto'));

    }

    public function show($id){

        $total_preco = DB::table ('venda')
        ->leftjoin('venda_item_material', 'venda.id', '=', 'venda_item_material.id_venda')
        ->leftjoin('item_material', 'venda_item_material.id_item_material', '=', 'item_material.id')
        ->where ('id_venda', '=', $id)
        ->sum('item_material.valor_venda');

    return view('relatorios/relatorio-vendas', compact('total_preco'));

    }

    public function entrada(Request $request) {

        $sessao = session()->get('usuario.depositos');

        $array_sessao = explode(",", $sessao);

        $nr_ordem = 1;

        $entmat = ModelItemMaterial::leftJoin('item_catalogo_material', 'item_material.id_item_catalogo_material', '=', 'item_catalogo_material.id')
                                ->leftJoin('tipo_categoria_material', 'item_catalogo_material.id_categoria_material', '=', 'tipo_categoria_material.id')
                                ->select('item_material.adquirido', 'item_material.data_cadastro','item_catalogo_material.nome', 'tipo_categoria_material.nome AS nomecat',  'item_material.valor_venda', DB::raw('COUNT(item_material.id_item_catalogo_material) as total'), DB::raw('SUM(item_material.valor_venda) as vlr_venda'))
                                ->where('item_material.id_deposito', $array_sessao)
                                ->groupBy('item_material.adquirido', 'item_material.data_cadastro', 'item_catalogo_material.nome', 'tipo_categoria_material.nome',  'item_material.valor_venda');

                        //dd($entmat->get());
        $data_inicio = $request->data_inicio;
        $data_fim = $request->data_fim;
        $categoria = $request->categoria;
        $compra = $request->compra;
        $nomeitem = $request->nomeitem;

        if ($request->data_inicio){

        $entmat->whereDate('item_material.data_cadastro','>=' , $request->data_inicio);

        }

        if ($request->data_fim){

            $entmat->whereDate('item_material.data_cadastro', '<=' , $request->data_fim);
        }

        if ($request->categoria){

            $entmat->whereIn('item_catalogo_material.id_categoria_material', $request->categoria);
        }
        if ($request->nomeitem){

            $entmat->whereIn('item_catalogo_material.id', $request->nomeitem);
        }

        if ($request->compra){

            $entmat->where('item_material.adquirido', '=', $request->compra);

        }

        $entData = $entmat->get();

        $somaent = $entData->sum('vlr_venda');

        $somait = $entData->sum('total');


        $entmat = $entmat->orderBy('tipo_categoria_material.nome', 'desc', 'item_catalogo_material.nome', 'desc')->paginate(100);

    

        //dd($somaent);

        $result = DB::select('select id, nome from tipo_categoria_material order by nome');
        $itemmaterial = DB::select ("select distinct(icm.nome), id, nome from item_catalogo_material icm order by nome"); 


        return view('relatorios/relatorio-entrada', compact('entmat','somaent','result', 'nr_ordem', 'data_inicio', 'data_fim', 'somait', 'itemmaterial'));

    }

    public function saida(Request $request) {

        $sessao = session()->get('usuario.depositos');

        $array_sessao = explode(",", $sessao);

        $nr_ordem = 1;

        $saidamat = DB::table('item_material')
                        ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material', 'item_catalogo_material.id')
                        ->leftjoin('tipo_categoria_material', 'tipo_categoria_material.id','item_catalogo_material.id_categoria_material')
                        ->leftjoin('venda_item_material', 'item_material.id', 'id_item_material')
                        ->leftjoin('venda', 'venda_item_material.id_venda', 'venda.id')
                        ->select('item_catalogo_material.nome AS nomemat', 'item_material.valor_venda','tipo_categoria_material.nome AS nomecat', 'venda.data', DB::raw('sum(item_material.valor_venda) as vlr_venda'), DB::raw('COUNT(item_material.id_item_catalogo_material) as qtdsaida'), 'item_material.adquirido')
                        ->where('item_material.id_tipo_situacao', '>', 1)
                        ->whereIn('item_material.id_deposito', $array_sessao)
                        ->groupby('item_catalogo_material.nome', 'item_material.valor_venda', 'tipo_categoria_material.nome', 'venda.data', 'item_material.adquirido');
                        //dd($saidamat->get());

        $data_inicio = $request->data_inicio;
        $data_fim = $request->data_fim;
        $categoria = $request->categoria;
        $compra = $request->compra;
        $nomeitem = $request->nomeitem;



        if ($request->data_inicio){

            $saidamat->whereDate('venda.data', '>=' , $request->data_inicio);
        }

        if ($request->data_fim){

            $saidamat->whereDate('venda.data','<=' , $request->data_fim);
        }

        if ($request->categoria){

            $saidamat->whereIn('item_catalogo_material.id_categoria_material', $request->categoria);
        }

        if ($request->nomeitem){

            $saidamat->whereIn('item_catalogo_material.id', $request->nomeitem);
        }

        if ($request->compra){

            $saidamat->where('item_material.adquirido', '=', $request->compra);
        }

        $datesai = $saidamat->get();

        $somasai = $datesai->sum('vlr_venda');
        $somaqtd = $datesai->sum('qtdsaida');


        $saidamat = $saidamat->orderBy('tipo_categoria_material.nome', 'desc' , 'item_catalogo_material.nome','desc')->paginate(100);

       //dd($saidamat);
    

        $result = DB::select('select id, nome from tipo_categoria_material order by nome');
        
        $itemmaterial = DB::select ("select distinct(icm.nome), id, nome from item_catalogo_material icm order by nome"); 




        return view('relatorios/relatorio-saida', compact('saidamat', 'result', 'somasai','nr_ordem', 'data_inicio', 'data_fim', 'somaqtd', 'itemmaterial'));

    }

       public function venda_cat(Request $request) {

        $sessao = session()->get('usuario.depositos');

        $array_sessao = explode(",", $sessao);

        $options = DB::table('item_material AS im')->select('im.id AS idad');

        $nr_ordem = 1;

         $saidacat1 = DB::table('venda')->select('tipo_categoria_material.nome AS nome_cat', 'item_material.adquirido')
         ->selectRaw('count (item_material.id) as qnt_cat')
         ->selectRaw('SUM(floor(item_material.valor_venda * item_material.valor_venda_promocional)) as desconto')
         ->selectraw('SUM(item_material.valor_venda) as vlr_original')
         ->selectRaw('(SUM(item_material.valor_venda)) - (SUM(FLOOR(item_material.valor_venda * item_material.valor_venda_promocional))) as vlr_final')
         ->leftJoin('venda_item_material', 'venda.id', 'venda_item_material.id_venda')
         ->leftJoin('item_material', 'venda_item_material.id_item_material', 'item_material.id')
         ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material', 'item_catalogo_material.id')         
         ->leftjoin('tipo_categoria_material', 'tipo_categoria_material.id','item_catalogo_material.id_categoria_material')                               
         ->where('venda.id_tp_situacao_venda', 3)
         ->where(function ($query) use ($array_sessao) {
             $query->whereNull('item_material.id_deposito')
                   ->orWhereIn('item_material.id_deposito', $array_sessao);
         })
         ->groupby('tipo_categoria_material.nome', 'item_material.adquirido');
      

        $saidacat2 = DB::table('pagamento')
                            ->select('tipo_pagamento.id as tpid', 'tipo_pagamento.nome as nomepg', 'pagamento.valor as valor_p')
                            ->leftjoin('venda', 'pagamento.id_venda', 'venda.id')
                            ->leftjoin('tipo_pagamento', 'pagamento.id_tipo_pagamento', 'tipo_pagamento.id')
                            ->leftjoin('venda_item_material', 'venda.id', 'venda_item_material.id_venda')
                            ->leftjoin('item_material', 'venda_item_material.id_item_material', 'item_material.id')
                            ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material', 'item_catalogo_material.id')
                            ->where('venda.id_tp_situacao_venda', 3)
                            ->where(function ($query) use ($array_sessao) {
                                $query->whereNull('item_material.id_deposito')
                                      ->orWhereIn('item_material.id_deposito', $array_sessao);
                            })
                            ->groupby('tipo_pagamento.id', 'tipo_pagamento.nome', 'pagamento.valor');



        $data_inicio =  $request->data_inicio;
        $data_fim = $request->data_fim;
        $categoria = $request->categoria;
        $compra = $request->compra;
        $dep = $request->deposito;



        if ($request->data_inicio){

            $saidacat1->whereDate('venda.data','>=' , $request->data_inicio);

            $saidacat2->whereDate('venda.data','>=' , $request->data_inicio);
        }

        if ($request->data_fim){

            $saidacat1->whereDate('venda.data','<=', $request->data_fim);

            $saidacat2->whereDate('venda.data','<=', $request->data_fim);

        }

        if ($request->categoria){

            $saidacat1->whereIn('item_catalogo_material.id_categoria_material', $request->categoria);

            $saidacat2->whereIn('item_catalogo_material.id_categoria_material', $request->categoria);
        }
//dd($compra);
        if ($compra === 'null'){

            $saidacat1->where(function($query) {
                $query->whereIn('item_material.adquirido', [true, false]) // Para booleanos
                      ->orWhereIn('item_material.adquirido', ['true', 'false']) // Para strings
                      ->orWhereIn('item_material.adquirido', [0, 1]); // Para inteiros
            });

            $saidacat2->where(function($query) {
                $query->whereIn('item_material.adquirido', [true, false]) // Para booleanos
                      ->orWhereIn('item_material.adquirido', ['true', 'false']) // Para strings
                      ->orWhereIn('item_material.adquirido', [0, 1]); // Para inteiros
            });
        }
        else{

            $saidacat1->where('item_material.adquirido', $request->compra);

            $saidacat2->where('item_material.adquirido', $request->compra);
        }

        if ($request->deposito){

            $saidacat1->where('item_material.id_deposito', $request->deposito);

            $saidacat2->where('item_material.id_deposito', $request->deposito);

        }

       
   

        $saidacat1 = $saidacat1->get();

        $original = $saidacat1->sum('vlr_original');
        $desconto = floatval($saidacat1->sum('desconto'));
        $total1 = $saidacat1->sum('vlr_final');
        $total2 = $saidacat1->sum('qnt_cat');

        $saidacat2 = $saidacat2->get();
  
        $total_din = $saidacat2->where('tpid', '1')->sum('valor_p');
        $total_deb = $saidacat2->where('tpid', '2')->sum('valor_p');
        $total_cre = $saidacat2->where('tpid', '3')->sum('valor_p');
        $total_che = $saidacat2->where('tpid', '4')->sum('valor_p');
        $total_pix = $saidacat2->where('tpid', '5')->sum('valor_p');

        $total3 = ($saidacat2->sum("valor_p"));
      //dd($total2);

        $result = DB::select('select id, nome from tipo_categoria_material order by nome');

        $deposito = DB::table('deposito')->select('id as iddep', 'nome')->whereIn('id', $array_sessao)->get();


        $compra = $request->input('compra');


        return view('relatorios/vendas-categoria', compact('options', 'deposito', 'compra', 'saidacat1', 'saidacat2', 'result', 'original', 'desconto', 'total1',  'total2', 'total3', 'nr_ordem', 'data_inicio', 'data_fim', 'total_deb', 'total_cre', 'total_che', 'total_pix', 'total_din'));

    }

    public function venda_valor(Request $request) {

        $sessao = session()->get('usuario.depositos');

        $array_sessao = explode(",", $sessao);

        $tipo_pag = DB::table('tipo_pagamento AS tp')->select('tp.id AS tid', 'tp.nome AS tnome')->get();

        $rela = DB::table('venda AS v')
        ->select('v.data','v.id as idv', 'pessoa.nome as nomep', 'tipo_pagamento.id as tpid', 'tipo_pagamento.nome as tpnome', 'pagamento.valor as pagvalor')
        ->leftJoin('venda_item_material', 'v.id', 'venda_item_material.id_venda')
        ->leftJoin('pagamento', 'v.id', 'pagamento.id_venda')
        ->leftJoin('tipo_pagamento', 'pagamento.id_tipo_pagamento', 'tipo_pagamento.id')
        ->leftJoin('item_material', 'venda_item_material.id_item_material', 'item_material.id')
        ->leftJoin('pessoa', 'v.id_pessoa', 'pessoa.id')                                
        ->where('v.id_tp_situacao_venda', 3)
        ->where(function ($query) use ($array_sessao) {
            $query->whereNull('item_material.id_deposito')
                  ->orWhereIn('item_material.id_deposito', $array_sessao);
        })
        ->groupby('v.data', 'v.id', 'pessoa.nome', 'tipo_pagamento.id', 'tipo_pagamento.nome', 'pagamento.valor');




        $data_inicio = $request->data_inicio;
        $data_fim = $request->data_fim;
        $tp_pag = $request->tp_pag;



        if ($request->data_inicio){

            $rela->whereDate('v.data', '>=' , $request->data_inicio);
        }

        if ($request->data_fim){

            $rela->whereDate('v.data','<=' , $request->data_fim);
        }

        if ($request->tp_pag){

            $rela->whereIn('tipo_pagamento.id', $request->tp_pag);
        }


        $tot = $rela->get();
        
        $total_pag = $tot->sum("pagvalor");
      
        $rela = $rela->orderBy('idv', 'asc')->paginate(50);

        //dd($rela);

       

        return view('relatorios/vendas-tipo-pag', compact('rela', 'data_inicio', 'data_fim', 'total_pag', 'tipo_pag'));

    }

}