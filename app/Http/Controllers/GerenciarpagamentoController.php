<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ModelPagamentos;
use App\Models\ModelVendas;
use Facade\Ignition\DumpRecorder\Dump;
use phpDocumentor\Reflection\Types\Float_;
use PhpParser\Node\Stmt\ElseIf_;
use Psy\VarDumper\Dumper;
use Symfony\Component\Console\Helper\Dumper as HelperDumper;


class GerenciarpagamentoController extends Controller
{

    private $objPagamentos;

    public function __construct(){
        $this->objPagamentos = new ModelPagamentos();
    }


    private function getListaPagamentosAll(){
        $lista = DB::select("
                        Select
                        p.id,
                        p.id_venda,
                        p.valor
                        from pagamento p
                        left join venda v on (v.id = p.id_venda)
                    ");
        return $lista;
    }



    public function show(Request $request, $id){

        ///Recupera os dados da  venda e cliente
        $vendas = DB::select ("
                            Select
                            v.id as idv,
                            pe.cpf,
                            pe.nome as nomepes,
                            v.data
                            from venda v
                            left join pessoa pe on (pe.id = v.id_pessoa)
                            where v.id=$id
                        ");

        ///Conta o número de itens da lista
        $total_itens = DB::table ('venda_item_material')
                    ->get('venda_item_material.id_venda','=', $id)
                    ->where('id_venda', '=', $id)
                    ->count('id_venda');


        ///Soma o total de itens da lista
        $total_venda = DB::table ('venda')
                    ->leftjoin('venda_item_material', 'venda.id', '=', 'venda_item_material.id_venda')
                    ->leftjoin('item_material', 'venda_item_material.id_item_material', '=', 'item_material.id')
                    ->where ('id_venda', '=', $id)
                    ->sum('item_material.valor_venda');

        $total_venda = floatval($total_venda);


        ///Total devido em dinheiro
        $total_especie = DB::table ('venda')
                    ->leftjoin('pagamento','pagamento.id_venda', '=', 'venda.id' )
                    ->where ('id_venda', '=', $id)
                    ->where ('pagamento.id_tipo_pagamento', '=', 1)
                    ->sum('pagamento.valor');

        $total_especie = floatval($total_especie);


        ///Valor a devolver em dinheiro
        $devolver = ($request->vlr_r) - $total_especie;

        ///Tipos de pagamento para exibir na lista de seleção
        $tipos_pagamento = DB::select ('select id, nome from tipo_pagamento');

        ///Soma TOTAL dos pagamentos
        $total_pago = DB::table ('pagamento')
                ->where ('id_venda', '=', $id)
                ->sum('valor');
        
        $total_pago = floatval($total_pago);
        
        //dd($total_pago);

        ///Cálculo do total de desconto
        $desconto = DB::table ('item_material')
                ->leftjoin('venda_item_material', 'item_material.id', 'venda_item_material.id_item_material')
                ->leftjoin('venda', 'venda_item_material.id_venda', 'venda.id')
                ->where ('venda_item_material.id_venda', '=', $id)
                ->sum(DB::raw('ROUND(item_material.valor_venda * item_material.valor_venda_promocional, 2)'));
        
        $desconto = floatval($desconto);

        //dd($desconto);

        ///Cálculo do total do preço da venda com desconto se houver
        $total_original = $total_venda;

        //dd($desconto);

         ///Cálculo do valor ainda não pago
         $nao_pago = floatval(bcsub(bcsub($total_original, $desconto, 2), $total_pago, 2));
         
//dd($nao_pago);
        ///Cálculo do total do preço da venda com desconto se houver
        $total_preco = bcsub($total_original, $desconto, 2);

        //dd($total_pago);

        ///Cálculo de possível troco
        $troco = $total_pago - $total_preco;

        ///Recupera os dados da  venda e cliente
        $itens_compra = DB::select ("
                                Select
                                im.valor_venda * im.valor_venda_promocional as desconto,
                                vim.id_venda,
                                vim.id_item_material,
                                im.id as idm,
                                ic.nome as nomemat,
                                im.valor_venda
                                from venda_item_material vim
                                left join venda v on (vim.id_venda = v.id)
                                left join item_material im on (im.id = vim.id_item_material)
                                left join item_catalogo_material ic on (ic.id = im.id_item_catalogo_material)
                                where v.id=$id
                                group by im.valor_venda, im.valor_venda_promocional, vim.id_venda, vim.id_item_material, im.id, ic.nome,
                                im.valor_venda
                            ");


        ///Pagamentos vinculados em uma venda
        $pagamentos= DB::select ("
                            Select
                            v.id as idv,
                            p.id as pid,
                            p.id_tipo_pagamento,
                            p.valor,
                            tp.nome
                            from pagamento p
                            left join venda v on (v.id = p.id_venda)
                            left join tipo_pagamento tp on (p.id_tipo_pagamento = tp.id)
                            where v.id=$id
                        ");

                        //dd($total_pago < $total_preco);
                        //dd($total_pago, $total_preco);

        return view ('vendas/gerenciar-pagamentos', compact('pagamentos','vendas','total_itens', 'total_preco', 'itens_compra',
        'tipos_pagamento', 'total_pago', 'troco', 'total_especie', 'devolver', 'desconto', 'total_original', 'nao_pago'));

    }


    public function inserir (Request $request, $id){

        $vendas = DB::select ("
                            Select
                            v.id,
                            v.data
                            from venda v
                            where v.id=$id
                        ");

        $total_preco = DB::table ('venda')
                    ->leftjoin('venda_item_material', 'venda.id', '=', 'venda_item_material.id_venda')
                    ->leftjoin('item_material', 'venda_item_material.id_item_material', '=', 'item_material.id')
                    ->where ('id_venda', '=', $id)
                    ->sum('item_material.valor_venda');

        //dd($total_preco);


        ///Cálculo do total de desconto
        $desconto = DB::table ('item_material')
        ->leftjoin('venda_item_material', 'item_material.id', 'venda_item_material.id_item_material')
        ->leftjoin('venda', 'venda_item_material.id_venda', 'venda.id')
        ->where ('venda_item_material.id_venda', '=', $id)
        ->sum(DB::raw('ROUND(item_material.valor_venda * item_material.valor_venda_promocional, 2)'));
        
        $desconto = floatval($desconto);

        ///Soma TOTAL dos pagamentos
        $total_pago = DB::table ('pagamento')
                    ->where ('id_venda', '=', $id)
                    ->sum('valor');
        

        $resto = (($total_preco - bcsub($desconto,2)) - $total_pago);


       $novo_valor = ($request->valor);

        if ($novo_valor <= $resto ) {

                DB::table('pagamento')->insert([
                    'id_venda' => ($id),
                    'valor' => $novo_valor,
                    'id_tipo_pagamento' => $request->input('forma')
                    ]);
                
                
                return redirect()->back()
                ->with('message', 'O valor foi incluído com sucesso.');

        } elseif($novo_valor > $resto){

                return redirect()->back()

                ->with('warning', 'O valor selecionado ultrapassa a soma do valor dos itens.');
        }


    }


    public function destroy($id){

        DB::delete('delete from pagamento where id = ?', [$id]);

        return redirect()->back();

    }

}
