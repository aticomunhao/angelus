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

        //AQUI TODAS AS REGRAS DE FILTROS DE PESQUISA

        $rela = ModelVendas::select('venda.data','venda.id as idv', 'pessoa.nome as nomep', DB::raw('sum(item_material.valor_venda * item_material.valor_venda_promocional) as desconto'), DB::raw('sum(item_material.valor_venda) as vlr_original'), DB::raw('sum(item_material.valor_venda) - sum(item_material.valor_venda * item_material.valor_venda_promocional) as vlr_final'))
                                ->join('venda_item_material', 'venda.id', 'venda_item_material.id_venda')
                                ->join('item_material', 'venda_item_material.id_item_material', 'item_material.id')
                                ->join('pessoa', 'venda.id_pessoa', 'pessoa.id')
                                ->groupby('venda.data','venda.id', 'pessoa.nome');

        $relb = ModelPagamentos::select('venda.data', 'pagamento.id as pid', 'tipo_pagamento.id as tpid', 'tipo_pagamento.nome as nomepg', 'pagamento.valor as valor_p', 'pagamento.id_venda')
                            ->join('venda', 'pagamento.id_venda', 'venda.id')
                            ->join('tipo_pagamento', 'pagamento.id_tipo_pagamento', 'tipo_pagamento.id')
                            ->join('venda_item_material', 'venda.id', 'venda_item_material.id_venda')
                            ->join('item_material', 'venda_item_material.id_item_material', 'item_material.id')
                            ->groupby('venda.data', 'pagamento.id', 'tipo_pagamento.id', 'tipo_pagamento.nome', 'pagamento.valor', 'pagamento.id_venda');



        $data_inicio = $request->data_inicio;
        $data_fim = $request->data_fim;
        //$compra = $request->compra;
        //$categoria = $request->categoria;



        if ($request->data_inicio){

            $rela->where(DB::raw("DATE(venda.data)"),'>=' , $request->data_inicio);

            $relb->where(DB::raw("DATE(venda.data)"),'>=' , $request->data_inicio);

        }

        if ($request->data_fim){

            $rela->where(DB::raw("DATE(venda.data)"),'<=' , $request->data_fim);

            $relb->where(DB::raw("DATE(venda.data)"),'<=' , $request->data_fim);

        }

       // if ($request->compra){

       //     $rela->where('item_material.adquirido','=' , $request->compra);

        //    $relb->where('item_material.adquirido','=' , $request->compra);

 //       }


        $rela = $rela->where('venda.id_tp_situacao_venda', '3')->get();
        $relb = $relb->where('venda.id_tp_situacao_venda', '3')->get();

        //dd($relb);
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


        return view('relatorios/relatorio-vendas', compact('total_pag', 'data_fim', 'data_inicio', 'rela', 'relb', 'total_din', 'total_deb', 'total_cre', 'total_che', 'total_pix', 'total1', 'total_desconto'));

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

        $nr_ordem = 1;

        $entmat = ModelItemMaterial::leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material', 'item_catalogo_material.id')
                        ->leftjoin('tipo_categoria_material', 'item_catalogo_material.id_categoria_material', 'tipo_categoria_material.id')
                        ->select(DB::raw('count(*) as total'))
                        ->select('item_material.adquirido','item_catalogo_material.nome','tipo_categoria_material.nome AS nomecat', 'item_material.data_cadastro', 'item_material.valor_venda', DB::raw('SUM(item_material.valor_venda) as vlr_venda'))
                        ->groupby('item_material.adquirido','item_catalogo_material.nome','tipo_categoria_material.nome', 'item_material.data_cadastro', 'item_material.valor_venda');

        $data_inicio = $request->data_inicio;
        $data_fim = $request->data_fim;
        $categoria = $request->categoria;
        $compra = $request->compra;

        if ($request->data_inicio){

        $entmat->where(DB::raw("DATE(item_material.data_cadastro"),'>=' , $request->data_inicio);

        }

        if ($request->data_fim){

            $entmat->where(DB::raw("DATE(item_material.data_cadastro"),'<=' , $request->data_fim);
        }

        if ($request->categoria){

            $entmat->where('item_catalogo_material.id_categoria_material','=' , $request->categoria);
        }

        if ($request->compra){

            $entmat->where('item_material.adquirido', '=', $request->compra);

        }


        $entmat = $entmat->get();

        $somaent = $entmat->sum('vlr_venda');

        //dd($somaent);

        $result = DB::select('select id, nome from tipo_categoria_material order by nome');


        return view('relatorios/relatorio-entrada', compact('entmat','somaent','result', 'nr_ordem', 'data_inicio', 'data_fim'));

    }

    public function saida(Request $request) {

        $nr_ordem = 1;

        $saidamat = DB::table('item_material')
        ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material', 'item_catalogo_material.id')
        //$saidamat = ModelItemMaterial::leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material', 'item_catalogo_material.id')
                        ->leftjoin('tipo_categoria_material', 'tipo_categoria_material.id','item_catalogo_material.id_categoria_material')
                        ->leftjoin('venda_item_material', 'item_material.id', 'id_item_material')
                        ->leftjoin('venda', 'venda_item_material.id_venda', 'venda.id')
                        ->select(DB::raw('count(*) as total'))
                        ->select('item_material.adquirido', 'item_catalogo_material.nome','item_material.valor_venda','tipo_categoria_material.nome AS nomecat', 'venda.data', 'valor_venda', DB::raw('sum(item_material.valor_venda) as vlr_venda'))
                        ->where('item_material.id_tipo_situacao', '>', '1')
                        ->groupby('item_material.adquirido', 'item_catalogo_material.nome', 'item_material.valor_venda', 'tipo_categoria_material.nome', 'venda.data');
                        //->get();
                        //dd($saidamat);

        $data_inicio = $request->data_inicio;
        $data_fim = $request->data_fim;
        $categoria = $request->categoria;
        $compra = $request->compra;



        if ($request->data_inicio){

            $saidamat->where(DB::raw("DATE(venda.data)"),'>=' , $request->data_inicio);
        }

        if ($request->data_fim){

            $saidamat->where(DB::raw("DATE(venda.data)"),'<=' , $request->data_fim);
        }

        if ($request->categoria){

            $saidamat->where('item_catalogo_material.id_categoria_material','=' , $request->categoria);
        }

        if ($request->compra){

            $saidamat->where('item_material.adquirido', '=', $request->compra);
        }

        $saidamat = $saidamat->get();

       //dd($saidamat);

        $somasai = $saidamat->sum('vlr_venda');

        $result = DB::select('select id, nome from tipo_categoria_material order by nome');




        return view('relatorios/relatorio-saida', compact('saidamat', 'result', 'somasai','nr_ordem', 'data_inicio', 'data_fim'));

    }

    public function vendas(Request $request)
    {

        //AQUI TODAS AS REGRAS DE FILTROS DE PESQUISA

        $rela = ModelVendas::select('venda.data','venda.id as idv',  'pessoa.nome as nomep', DB::raw('sum(item_material.valor_venda * item_material.valor_venda_promocional) as desconto'), DB::raw('sum(item_material.valor_venda) as vlr_original'), DB::raw('sum(item_material.valor_venda) - sum(item_material.valor_venda * item_material.valor_venda_promocional) as vlr_final') )
                            ->join('venda_item_material', 'venda.id', 'venda_item_material.id_venda')
                            ->join('item_material', 'venda_item_material.id_item_material', 'item_material.id')
                            ->join('pessoa', 'venda.id_pessoa', 'pessoa.id')
                            ->groupby('venda.id', 'pessoa.nome','venda.data');

        //$rel = ModelVendas::select('venda.data','venda.id as idv','tipo_pagamento.nome as nome_tp', 'pagamento.valor as vlr_tp' )
          //                  ->join('venda_item_material', 'venda.id', 'venda_item_material.id_venda')
            //                ->join('pagamento',  'venda.id', 'pagamento.id_venda')
              //              ->join('tipo_pagamento', 'pagamento.id_tipo_pagamento', 'tipo_pagamento.id')
                //            ->groupby('venda.id', 'venda.data','tipo_pagamento.nome', 'pagamento.valor');


        $relb = ModelPagamentos::select('venda.id', 'venda.data', 'tipo_pagamento.nome as nomepg', 'pagamento.valor as valor_p')
                            ->join('venda', 'pagamento.id_venda', 'venda.id')
                            ->join('tipo_pagamento', 'pagamento.id_tipo_pagamento', 'tipo_pagamento.id')
                            ->join('venda_item_material', 'venda.id', 'venda_item_material.id_venda')
                            ->join('item_material', 'venda_item_material.id_item_material', 'item_material.id')
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

        $nr_ordem = 1;

         $saidacat1 = ModelItemMaterial::select('item_material.adquirido', 'tipo_categoria_material.nome AS nome_cat',  DB::raw('sum(item_material.valor_venda * item_material.valor_venda_promocional) as desconto'), DB::raw('sum(item_material.valor_venda) as vlr_original'), DB::raw('(sum(item_material.valor_venda) - sum(item_material.valor_venda * item_material.valor_venda_promocional)) as vlr_final'), DB::raw('count(item_material.id) as qnt_cat'))
                        ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material', 'item_catalogo_material.id')
                        ->leftjoin('tipo_categoria_material', 'tipo_categoria_material.id','item_catalogo_material.id_categoria_material')
                        ->leftjoin('venda_item_material', 'item_material.id', 'id_item_material')
                        ->leftjoin('venda', 'venda_item_material.id_venda', 'venda.id')
                        ->leftJoin('pagamento AS p', 'venda.id', 'p.id_venda')
                        ->where('item_material.id_tipo_situacao', '2')
                        ->whereIn('p.id_tipo_pagamento', [2,3,4,5])
                        ->groupby('item_material.adquirido', 'tipo_categoria_material.nome')
                        ->orderby('tipo_categoria_material.nome');


        $saidacat2 = ModelPagamentos::select('pagamento.id as pid', 'tipo_pagamento.id as tpid', 'tipo_pagamento.nome as nomepg', 'pagamento.valor as valor_p')
                            ->leftjoin('venda', 'pagamento.id_venda', 'venda.id')
                            ->leftjoin('tipo_pagamento', 'pagamento.id_tipo_pagamento', 'tipo_pagamento.id')
                            ->leftjoin('venda_item_material', 'venda.id', 'venda_item_material.id_venda')
                            ->leftjoin('item_material', 'venda_item_material.id_item_material', 'item_material.id')
                            ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material', 'item_catalogo_material.id')
                            ->whereIn('pagamento.id_tipo_pagamento', [2,3,4,5])
                            ->groupby('venda.data', 'pagamento.id', 'tipo_pagamento.id', 'tipo_pagamento.nome', 'pagamento.valor', 'pagamento.id_venda');



        $data_inicio =  $request->data_inicio;
        $data_fim = $request->data_fim;
        $categoria = $request->categoria;
        $compra = $request->compra;



        if ($request->data_inicio){

            $saidacat1->where(DB::raw("DATE(venda.data)"),'>=' , $request->data_inicio);

            $saidacat2->where(DB::raw("DATE(venda.data)"),'>=' , $request->data_inicio);
        }

        if ($request->data_fim){

            $saidacat1->where(DB::raw("DATE(venda.data)"),'<=', $request->data_fim);

            $saidacat2->where(DB::raw("DATE(venda.data)"),'<=', $request->data_fim);

        }

        if ($request->categoria){

            $saidacat1->where('item_catalogo_material.id_categoria_material','=' , $request->categoria);

            $saidacat2->where('item_catalogo_material.id_categoria_material','=' , $request->categoria);
        }

        if ($request->compra){

            $saidacat1->where('item_material.adquirido', '=', $request->compra);

            $saidacat2->where('item_material.adquirido', '=', $request->compra);
        }

        $saidacat1 = $saidacat1->orderby('tipo_categoria_material.nome')->get();

        $saidacat2 = $saidacat2->get();

        //dd($saidacat2);

        $total1 = $saidacat1->sum('vlr_final');
        $total3 = $saidacat1->sum('qnt_cat');
        $total_desconto = $saidacat1->sum('desconto');

        $total_din = $saidacat2->where('tpid', '1')->sum('valor_p');
        $total_deb = $saidacat2->where('tpid', '2')->sum('valor_p');
        $total_cre = $saidacat2->where('tpid', '3')->sum('valor_p');
        $total_che = $saidacat2->where('tpid', '4')->sum('valor_p');
        $total_pix = $saidacat2->where('tpid', '5')->sum('valor_p');

        $total2 = $saidacat2->sum("valor_p");

      //dd($total_deb);

        $result = DB::select('select id, nome from tipo_categoria_material order by nome');




        return view('relatorios/saida-categoria', compact('saidacat1', 'saidacat2', 'result', 'total1', 'total2', 'total3', 'nr_ordem', 'data_inicio', 'data_fim', 'total_deb', 'total_cre', 'total_che', 'total_pix', 'total_desconto', 'total_din'));

    }

}