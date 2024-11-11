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

        $rela = ModelVendas::select('venda.id_tp_situacao_venda', 'venda.data', 'item_material.id_deposito', 'venda.id as idv', 'pessoa.nome as nomep', DB::raw('sum(item_material.valor_venda * item_material.valor_venda_promocional) as desconto'), DB::raw('sum(item_material.valor_venda) as vlr_original'), DB::raw('sum(item_material.valor_venda) - sum(item_material.valor_venda * item_material.valor_venda_promocional) as vlr_final'))
                                ->leftJoin('venda_item_material', 'venda.id', 'venda_item_material.id_venda')
                                ->leftJoin('item_material', 'venda_item_material.id_item_material', 'item_material.id')
                                ->leftJoin('pessoa', 'venda.id_pessoa', 'pessoa.id')                                
                                ->where('item_material.id_tipo_situacao', '2')
                                ->where(function ($query) use ($array_sessao) {
                                    $query->whereNull('item_material.id_deposito')
                                          ->orWhereIn('item_material.id_deposito', $array_sessao);
                                })
                                ->groupby('venda.id_tp_situacao_venda','venda.data','venda.id', 'item_material.id_deposito', 'pessoa.nome');


        $relb = ModelPagamentos::select('venda.id_tp_situacao_venda', 'venda.data', 'item_material.id_deposito', 'pagamento.id as pid', 'tipo_pagamento.id as tpid', 'tipo_pagamento.nome as nomepg', 'pagamento.valor as valor_p', 'pagamento.id_venda')
                            ->leftJoin('venda', 'pagamento.id_venda', 'venda.id')
                            ->leftJoin('tipo_pagamento', 'pagamento.id_tipo_pagamento', 'tipo_pagamento.id')
                            ->leftJoin('venda_item_material', 'venda.id', 'venda_item_material.id_venda')
                            ->leftJoin('item_material', 'venda_item_material.id_item_material', 'item_material.id')
                            ->where('item_material.id_tipo_situacao', '2')
                            ->where(function ($query) use ($array_sessao) {
                                $query->whereNull('item_material.id_deposito')
                                      ->orWhereIn('item_material.id_deposito', $array_sessao);
                            })
                            ->groupby('venda.id_tp_situacao_venda', 'venda.data', 'item_material.id_deposito', 'pagamento.id', 'tipo_pagamento.id', 'tipo_pagamento.nome', 'pagamento.valor', 'pagamento.id_venda');



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

        if ($request->compra){

            $rela->where('item_material.adquirido','=' , $request->compra);

            $relb->where('item_material.adquirido','=' , $request->compra);

        }

        if ($request->deposito){

            $rela->where('item_material.id_deposito', $request->deposito);

            $relb->where('item_material.id_deposito', $request->deposito);

        }

        $rela = $rela->where('venda.id_tp_situacao_venda', '3')->get();
        $relb = $relb->where('venda.id_tp_situacao_venda', '3')->get();

        $total1 = floor($rela->sum('vlr_final'));
        $total_desconto = floor($rela->sum('desconto'));
//dd($total1);

        $total_din = $relb->where('tpid', '1')->sum('valor_p');
        $total_deb = $relb->where('tpid', '2')->sum('valor_p');
        $total_cre = $relb->where('tpid', '3')->sum('valor_p');
        $total_che = $relb->where('tpid', '4')->sum('valor_p');
        $total_pix = $relb->where('tpid', '5')->sum('valor_p');

        $total_pag = floor($relb->sum("valor_p"));

        //dd($total_cre);
        $deposito = DB::table('deposito')->select('id as iddep', 'nome')->whereIn('id', $array_sessao)->get();

        $compra = $request->input('compra');

        //dd($deposito);

        return view('relatorios/relatorio-vendas', compact('compra', 'deposito', 'total_pag', 'data_fim', 'data_inicio', 'rela', 'relb', 'total_din', 'total_deb', 'total_cre', 'total_che', 'total_pix', 'total1', 'total_desconto'));

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

        $entmat->where(DB::raw("DATE(item_material.data_cadastro)"),'>=' , $request->data_inicio);

        }

        if ($request->data_fim){

            $entmat->where(DB::raw("DATE(item_material.data_cadastro)"),'<=' , $request->data_fim);
        }

        if ($request->categoria){

            $entmat->where('item_catalogo_material.id_categoria_material','=' , $request->categoria);
        }
        if ($request->nomeitem){

            $entmat->where('item_catalogo_material.id','=' , $request->nomeitem);
        }

        if ($request->compra){

            $entmat->where('item_material.adquirido', '=', $request->compra);

        }


        $entmat = $entmat->get();

        $somaent = $entmat->sum('vlr_venda');

        $somait = $entmat->sum('total');

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

            $saidamat->where(DB::raw("DATE(venda.data)"),'>=' , $request->data_inicio);
        }

        if ($request->data_fim){

            $saidamat->where(DB::raw("DATE(venda.data)"),'<=' , $request->data_fim);
        }

        if ($request->categoria){

            $saidamat->where('item_catalogo_material.id_categoria_material','=' , $request->categoria);
        }

        if ($request->nomeitem){

            $saidamat->where('item_catalogo_material.id','=' , $request->nomeitem);
        }

        if ($request->compra){

            $saidamat->where('item_material.adquirido', '=', $request->compra);
        }

        $saidamat = $saidamat->orderBy('venda.data','ASC')->get();

       //dd($saidamat);

        $somasai = $saidamat->sum('vlr_venda');
        $somaqtd = $saidamat->sum('qtdsaida');

        $result = DB::select('select id, nome from tipo_categoria_material order by nome');
        
        $itemmaterial = DB::select ("select distinct(icm.nome), id, nome from item_catalogo_material icm order by nome"); 




        return view('relatorios/relatorio-saida', compact('saidamat', 'result', 'somasai','nr_ordem', 'data_inicio', 'data_fim', 'somaqtd', 'itemmaterial'));

    }

    public function vendas(Request $request)
    {
        $sessao = session()->get('usuario.depositos');

        $array_sessao = explode(",", $sessao);

       // dd($sessao);

        //AQUI TODAS AS REGRAS DE FILTROS DE PESQUISA

        $rela = ModelVendas::select('venda.data','venda.id as idv',  'pessoa.nome as nomep', DB::raw('sum(item_material.valor_venda * item_material.valor_venda_promocional) as desconto'), DB::raw('sum(item_material.valor_venda) as vlr_original'), DB::raw('sum(item_material.valor_venda) - sum(item_material.valor_venda * item_material.valor_venda_promocional) as vlr_final') )
                            ->lefjoin('venda_item_material', 'venda.id', 'venda_item_material.id_venda')
                            ->leftjoin('item_material', 'venda_item_material.id_item_material', 'item_material.id')
                            ->leftjoin('pessoa', 'venda.id_pessoa', 'pessoa.id')
                            ->leftjoin ('usuario AS u',  'u.id', '=', 'v.id_usuario')                            
                            ->leftjoin ('usuario_deposito AS ud',  'u.id', '=', 'ud.id_usuario')
                            ->whereIn('ud.id_deposito', $array_sessao)
                            ->groupBy('venda.id', 'pessoa.nome','venda.data');

        //$rel = ModelVendas::select('venda.data','venda.id as idv','tipo_pagamento.nome as nome_tp', 'pagamento.valor as vlr_tp' )
          //                  ->join('venda_item_material', 'venda.id', 'venda_item_material.id_venda')
            //                ->join('pagamento',  'venda.id', 'pagamento.id_venda')
              //              ->join('tipo_pagamento', 'pagamento.id_tipo_pagamento', 'tipo_pagamento.id')
                //            ->groupby('venda.id', 'venda.data','tipo_pagamento.nome', 'pagamento.valor');


        $relb = ModelPagamentos::select('venda.id', 'venda.data', 'tipo_pagamento.nome as nomepg', 'pagamento.valor as valor_p')
                            ->leftjoin('venda', 'pagamento.id_venda', 'venda.id')
                            ->leftjoin('tipo_pagamento', 'pagamento.id_tipo_pagamento', 'tipo_pagamento.id')
                            ->leftjoin('venda_item_material', 'venda.id', 'venda_item_material.id_venda')
                            ->leftjoin('item_material', 'venda_item_material.id_item_material', 'item_material.id')
                            ->whereIn('im.id_deposito', $array_sessao)
                            ->groupBy('venda.id','venda.data', 'tipo_pagamento.nome', 'pagamento.valor');



        $data_inicio = $request->data_inicio;
        $data_fim = $request->data_fim;
       // $compra = $request->compra;




        if ($request->data_inicio){

            $rela->where(DB::raw("DATE(venda.data)"),'>=' , $request->data_inicio);

            $relb->where(DB::raw("DATE(venda.data)"),'>=' , $request->data_inicio);

        }

        if ($request->data_fim){

            $rela->where(DB::raw("DATE(venda.data)"),'<=' , $request->data_fim);

            $relb->where(DB::raw("DATE(venda.data)"),'<=' , $request->data_fim);

        }

       // if ($request->compra){

         //   $rela->where('item_material.adquirido','=' , $request->compra);

       //     $relb->where('item_material.adquirido','=' , $request->compra);

        //}


        $rela = $rela->where('venda.id_tp_situacao_venda', '3')->get();
        $relb = $relb->where('venda.id_tp_situacao_venda', '3')->get();

        //dd($rela);
        //dd($relb);
        $total1 = $rela->sum('vlr_final');
        $total_desconto = $rela->sum('desconto');


        $total_din = $relb->where('tpid', '1')->sum('valor_p');
        $total_deb = $relb->where('tpid', '2')->sum('valor_p');
        $total_cre = $relb->where('tpid', '3')->sum('valor_p');
        $total_che = $relb->where('tpid', '4')->sum('valor_p');
        $total_pix = $relb->where('tpid', '5')->sum('valor_p');

        $total_pag = $relb->sum("valor_p");

        //dd($total_cre);
        //$result = DB::select('select id, nome from tipo_categoria_material order by nome');


        return view('relatorios/vendas-detalhe', compact('total_pag', 'data_fim', 'data_inicio', 'rela', 'relb', 'total_din', 'total_deb', 'total_cre', 'total_che', 'total_pix', 'total1', 'total_desconto'));


    }

    public function saida_cat(Request $request) {

        $sessao = session()->get('usuario.depositos');

        $array_sessao = explode(",", $sessao);

        $nr_ordem = 1;

         $saidacat1 = ModelItemMaterial::select('item_material.adquirido', 'item_material.id_deposito', 'tipo_categoria_material.nome AS nome_cat',  DB::raw('sum(item_material.valor_venda * item_material.valor_venda_promocional) as desconto'), DB::raw('sum(item_material.valor_venda) as vlr_original'), DB::raw('(sum(item_material.valor_venda) - sum(item_material.valor_venda * item_material.valor_venda_promocional)) as vlr_final'), DB::raw('count(item_material.id) as qnt_cat'))
                        ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material', 'item_catalogo_material.id')
                        ->leftjoin('tipo_categoria_material', 'tipo_categoria_material.id','item_catalogo_material.id_categoria_material')
                        ->leftjoin('venda_item_material', 'item_material.id', 'id_item_material')
                        ->leftjoin('venda', 'venda_item_material.id_venda', 'venda.id')
                        ->where('venda.id_tp_situacao_venda', '3')
                        ->where(function ($query) use ($array_sessao) {
                            $query->whereNull('item_material.id_deposito')
                                  ->orWhereIn('item_material.id_deposito', $array_sessao);
                        })
                        ->groupby('item_material.adquirido', 'item_material.id_deposito', 'tipo_categoria_material.nome')
                        ->orderby('tipo_categoria_material.nome');


        $saidacat2 = ModelPagamentos::select('pagamento.id as pid', 'item_material.id_deposito', 'tipo_pagamento.id as tpid', 'tipo_pagamento.nome as nomepg', 'pagamento.valor as valor_p')
                            ->leftjoin('venda', 'pagamento.id_venda', 'venda.id')
                            ->leftjoin('tipo_pagamento', 'pagamento.id_tipo_pagamento', 'tipo_pagamento.id')
                            ->leftjoin('venda_item_material', 'venda.id', 'venda_item_material.id_venda')
                            ->leftjoin('item_material', 'venda_item_material.id_item_material', 'item_material.id')
                            ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material', 'item_catalogo_material.id')
                            ->where('venda.id_tp_situacao_venda', '3')
                            ->where(function ($query) use ($array_sessao) {
                                $query->whereNull('item_material.id_deposito')
                                      ->orWhereIn('item_material.id_deposito', $array_sessao);
                            })
                            ->groupby('venda.data', 'pagamento.id', 'item_material.id_deposito', 'tipo_pagamento.id', 'tipo_pagamento.nome', 'pagamento.valor', 'pagamento.id_venda');



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

        if ($request->compra){

            $saidacat1->where('item_material.adquirido', '=', $request->compra);

            $saidacat2->where('item_material.adquirido', '=', $request->compra);
        }

        if ($request->deposito){

            $saidacat1->where('item_material.id_deposito', $request->deposito);

            $saidacat2->where('item_material.id_deposito', $request->deposito);

        }

        $saidacat1 = $saidacat1->orderby('tipo_categoria_material.nome')->get();

        $saidacat2 = $saidacat2->get();

        //dd($saidacat2);

        $total1 = floor($saidacat1->sum('vlr_final'));
        $total3 = floor($saidacat1->sum('qnt_cat'));
        $total_desconto = floor($saidacat1->sum('desconto'));

        $total_din = $saidacat2->where('tpid', '1')->sum('valor_p');
        $total_deb = $saidacat2->where('tpid', '2')->sum('valor_p');
        $total_cre = $saidacat2->where('tpid', '3')->sum('valor_p');
        $total_che = $saidacat2->where('tpid', '4')->sum('valor_p');
        $total_pix = $saidacat2->where('tpid', '5')->sum('valor_p');

        $total2 = floor($saidacat2->sum("valor_p"));
      //dd($total2);

        $result = DB::select('select id, nome from tipo_categoria_material order by nome');

        $deposito = DB::table('deposito')->select('id as iddep', 'nome')->whereIn('id', $array_sessao)->get();


        $compra = $request->input('compra');


        return view('relatorios/saida-categoria', compact('deposito', 'compra', 'saidacat1', 'saidacat2', 'result', 'total1', 'total2', 'total3', 'nr_ordem', 'data_inicio', 'data_fim', 'total_deb', 'total_cre', 'total_che', 'total_pix', 'total_desconto', 'total_din'));

    }

}