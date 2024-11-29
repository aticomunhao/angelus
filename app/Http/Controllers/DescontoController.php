<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class DescontoController extends Controller
{
    public function index(Request $request){

        $result = DB::table('descontos')
                    ->leftjoin('tipo_categoria_material', 'descontos.id_tp_categoria', 'tipo_categoria_material.id')
                    ->leftjoin('percentagem', 'descontos.porcentagem', 'percentagem.valor')
                    ->leftjoin('usuario', 'descontos.id_usuario', 'usuario.id')
                    ->leftjoin('pessoa', 'usuario.id_pessoa', 'pessoa.id')
                    ->select('descontos.id', 'tipo_categoria_material.nome AS nome1', 'data_inicio', 'data_fim', 'porcentagem', 'percentagem.codigo AS percentual', 'pessoa.nome AS nome2', 'descontos.ativo', 'descontos.data_registro');
                    
                             
                    $data_inicio = $request->data_inicio;
            
                    $data_fim = $request->data_fim;
            
                    if ($request->data_inicio){
            
                        $result->whereDate('descontos.data_registro','>=' , $request->data_inicio);
                    }
            
                    if ($request->data_fim){
            
                        $result->whereDate('descontos.data_registro','<=' , $request->data_fim);
                    }           
                    
            
                    $result = $result->orderBy('descontos.data_registro', 'DESC')->paginate(10);


        return view ('/descontos/gerenciar-desconto', compact('result', 'data_inicio', 'data_fim'));

    }

    public function create(){

        $categoria = DB::select("select id, nome from tipo_categoria_material");

        $percent = DB::select("select id, codigo, valor from percentagem");

        return view ('/descontos/criar-desconto', compact('categoria', 'percent'));

    }


    public function store(Request $request){

        $inativo = isset($request->inativo) ? 1 : 0;

        if ($request->cat_item > 0){

            DB::table('descontos')->insert([
                'id_tp_categoria' => $request->input('cat_item'),
                'data_inicio' => $request->input('data_inicio'),
                'data_fim' => $request->input('data_fim'),
                'porcentagem' => $request->input('porcentagem'),
                'id_usuario' => $request->input('id_usuario'),
                'data_registro' => $request->input('data_registro'),
                'ativo' => $inativo,
            ]);

        }else{
            DB::table('descontos')->insert([
                'id_tp_categoria' => null,
                'data_inicio' => $request->input('data_inicio'),
                'data_fim' => $request->input('data_fim'),
                'porcentagem' => $request->input('porcentagem'),
                'id_usuario' => $request->input('id_usuario'),
                'data_registro' => $request->input('data_registro'),
                'ativo' => $inativo,
            ]);

        }


        return redirect()->action('DescontoController@index')
        ->with('message', 'A configuração do desconto foi criada com sucesso!');

    }

    public function edit($id){


        $categoria = DB::table('tipo_categoria_material')->get();

        $percent = DB::select("select id, codigo, valor from percentagem");


        $result = DB::table('descontos')
                    ->leftjoin('tipo_categoria_material', 'descontos.id_tp_categoria', 'tipo_categoria_material.id')
                    ->leftjoin('usuario', 'descontos.id_usuario', 'usuario.id')
                    ->leftjoin('pessoa', 'usuario.id_pessoa', 'pessoa.id')
                    ->select('descontos.id', 'tipo_categoria_material.id AS idcat', 'tipo_categoria_material.nome AS nome1', 'data_inicio', 'data_fim', 'porcentagem', 'pessoa.nome AS nome2', 'descontos.ativo', 'descontos.data_registro')
                    ->where('descontos.id', $id)
                    ->get();

        return view ('/descontos/desconto-alterar', compact('result', 'categoria', 'percent'));

    }

    public function update(Request $request, $id){

        DB::table('descontos')
        ->where('id', $id)
        ->update([
            'id_tp_categoria' => $request->input('cat_item'),
            'data_inicio' => $request->input('data_inicio'),
            'data_fim' => $request->input('data_fim'),
            'porcentagem' => $request->input('porcentagem'),
            'id_usuario' => $request->input('id_usuario'),
            'data_registro' => $request->input('data_registro'),
        ]);

        return redirect()->action('DescontoController@index')
        ->with('message', 'A configuração do desconto foi alterada com sucesso!');
    }


    public function destroy($id){

        $desconto = DB::table('descontos')
                    ->where('id',$id)
                    ->value('porcentagem');

        $categoria = DB::table('descontos')
                    ->where('id',$id)
                    ->value('id_tp_categoria');


        DB::table('item_material')
        ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material', 'item_catalogo_material.id')
        ->leftJoin('tipo_categoria_material', 'item_catalogo_material.id_categoria_material', 'tipo_categoria_material.id')
        ->leftJoin('descontos', 'tipo_categoria_material.id', 'descontos.id_tp_categoria')
        ->where('descontos.id',$id)
        ->where('item_catalogo_material.id_categoria_material', $categoria)
        ->where ('descontos.porcentagem', $desconto)
        ->where ('item_material.id_tipo_situacao','<', 2)
        ->update([
        'item_material.valor_venda_promocional' => 0,
        ]);

        DB::table('descontos')
        ->where('id', $id)
        ->delete();

        return redirect()->action('DescontoController@index')
        ->with('danger', 'A configuração do desconto foi excluida com sucesso!');


    }

    public function active(Request $request, $id){

        ////for($i = 0.01; $i < 1; $i+=0.01){
        //    echo $i.', ';
        //}
        //dd($request->session()->all());
       //$registered = Session::registered()->get();

        $desconto = DB::table('descontos')
                    ->where('id',$id)
                    ->value('porcentagem');

        $categoria = DB::table('descontos')
                    ->where('id',$id)
                    ->value('id_tp_categoria');

        $data_inicio = DB::table('descontos')
                    ->where('id',$id)
                    ->value('data_inicio');

        $data_fim = DB::table('descontos')
                    ->where('id',$id)
                    ->value('data_fim');

        $usuario = $request->session()->get('usuario.id_usuario');

        $sessao = session()->get('usuario.depositos');

        $array_sessao = explode(",", $sessao);

        $data_atual = (\Carbon\carbon::now()->toDateTimeString());

       // dd($desconto);

        if ($data_inicio == null && $data_fim == null && $categoria == null){

            DB::table('item_material')
            ->where(function ($query) use ($array_sessao) {
                $query->whereNull('item_material.id_deposito')
                      ->orWhereIn('item_material.id_deposito', $array_sessao);
            })
            ->where ('item_material.id_tipo_situacao','<', 2)
            ->update([
            'item_material.valor_venda_promocional' => $desconto,
            ]);

            $teste = DB::table('descontos')
                ->where('descontos.id',$id)
                ->update([
                    'ativo' => 'true',
                    'id_usuario' => $usuario,
                    'data_registro' => $data_atual
                    ]);


            return redirect()->action('DescontoController@index')
                ->with('message', 'A configuração do desconto black friday foi ativada!');
        }
        elseif ($data_inicio == null && $data_fim == null){

                DB::table('item_material')
                ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material', 'item_catalogo_material.id')
                ->leftJoin('tipo_categoria_material', 'item_catalogo_material.id_categoria_material', 'tipo_categoria_material.id')
                ->leftJoin('descontos', 'tipo_categoria_material.id', 'descontos.id_tp_categoria')
                ->where('descontos.id',$id)
                ->where(function ($query) use ($array_sessao) {
                    $query->whereNull('item_material.id_deposito')
                          ->orWhereIn('item_material.id_deposito', $array_sessao);
                })
                ->where('item_catalogo_material.id_categoria_material', $categoria)
                ->where ('descontos.porcentagem', $desconto)
                ->where ('item_material.id_tipo_situacao','<', 2)
                ->update([
                'item_material.valor_venda_promocional' => $desconto,
                ]);
    
                $teste = DB::table('descontos')
                    ->where('descontos.id',$id)
                    ->update([
                        'ativo' => 'true',
                        'id_usuario' => $usuario,
                        'data_registro' => $data_atual
                        ]);
    
    
                return redirect()->action('DescontoController@index')
                    ->with('message', 'A configuração do desconto foi ativada!');


        }
        elseif ($data_inicio > 0 && $data_fim == null){

              DB::table('item_material')
              ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material', 'item_catalogo_material.id')
              ->leftJoin('tipo_categoria_material', 'item_catalogo_material.id_categoria_material', 'tipo_categoria_material.id')
              ->leftJoin('descontos', 'tipo_categoria_material.id', 'descontos.id_tp_categoria')
              ->where('descontos.id',$id)
              ->where(function ($query) use ($array_sessao) {
                $query->whereNull('item_material.id_deposito')
                      ->orWhereIn('item_material.id_deposito', $array_sessao);
            })
              ->where('item_catalogo_material.id_categoria_material', $categoria)
              ->where('descontos.porcentagem', $desconto)
              ->where('item_material.data_cadastro','>', $data_inicio)
              ->where('item_material.id_tipo_situacao','<', 2)
              ->update([
                'valor_venda_promocional' => $desconto
             ]);

             DB::table('descontos')
             ->where('descontos.id',$id)
             ->update([
                        'ativo' => 'true',
                        'id_usuario' => $usuario,
                        'data_registro' => $data_atual
                 ]);


              return redirect()->action('DescontoController@index')
                  ->with('message', 'A configuração do desconto foi ativada!');
        }
        elseif ($data_inicio == null && $data_fim > 0){

            DB::table('item_material')
            ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material', 'item_catalogo_material.id')
            ->leftJoin('tipo_categoria_material', 'item_catalogo_material.id_categoria_material', 'tipo_categoria_material.id')
            ->leftJoin('descontos', 'tipo_categoria_material.id', 'descontos.id_tp_categoria')
            ->where('descontos.id',$id)
            ->where(function ($query) use ($array_sessao) {
                $query->whereNull('item_material.id_deposito')
                      ->orWhereIn('item_material.id_deposito', $array_sessao);
            })
            ->where('item_catalogo_material.id_categoria_material', $categoria)
            ->where('descontos.porcentagem', $desconto)
            ->where('item_material.data_cadastro','<', $data_fim)
            ->where('item_material.id_tipo_situacao','<', 2)
            ->update([
              'valor_venda_promocional' => $desconto
           ]);

           DB::table('descontos')
           ->where('descontos.id',$id)
           ->update([
                'ativo' => 'true',
                'id_usuario' => $usuario,
                'data_registro' => $data_atual
               ]);


            return redirect()->action('DescontoController@index')
                ->with('message', 'A configuração do desconto foi ativada!');
      }
        elseif ($data_inicio > 0 && $data_fim > 0){


            DB::table('item_material')
            ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material', 'item_catalogo_material.id')
            ->leftJoin('tipo_categoria_material', 'item_catalogo_material.id_categoria_material', 'tipo_categoria_material.id')
            ->leftJoin('descontos', 'tipo_categoria_material.id', 'descontos.id_tp_categoria')
            ->where('descontos.id',$id)
            ->where(function ($query) use ($array_sessao) {
                $query->whereNull('item_material.id_deposito')
                      ->orWhereIn('item_material.id_deposito', $array_sessao);
            })
            ->where('item_catalogo_material.id_categoria_material', $categoria)
            ->where('descontos.porcentagem', $desconto)
            ->where('item_material.data_cadastro', '>', $data_inicio)
            ->where('item_material.data_cadastro', '<', $data_fim)
            ->where('item_material.id_tipo_situacao','<', 2)
            ->update([
                'valor_venda_promocional' => $desconto
            ]);

            DB::table('descontos')
            ->where('descontos.id',$id)
            ->update([
                'ativo' => 'true',
                'id_usuario' => $usuario,
                'data_registro' => $data_atual
                ]);

            return redirect()->action('DescontoController@index')
                  ->with('message', 'A configuração do desconto foi ativada!');

        }

    }

    public function inactive(Request $request, $id){

        $desconto = DB::table('descontos')
                    ->where('id',$id)
                    ->value('porcentagem');

        $categoria = DB::table('descontos')
                    ->where('id',$id)
                    ->value('id_tp_categoria');

        $data_inicio = DB::table('descontos')
                    ->where('id',$id)
                    ->value('data_inicio');

        $data_fim = DB::table('descontos')
                    ->where('id',$id)
                    ->value('data_fim');

        $usuario = $request->session()->get('usuario.id_usuario');

        $sessao = session()->get('usuario.depositos');

        $array_sessao = explode(",", $sessao);

        $data_atual = (\Carbon\carbon::now()->toDateTimeString());

        if ($data_inicio == null && $data_fim == null && $categoria == null){

            DB::table('item_material')
            ->where(function ($query) use ($array_sessao) {
                $query->whereNull('item_material.id_deposito')
                      ->orWhereIn('item_material.id_deposito', $array_sessao);
            })
            ->where('item_material.id_tipo_situacao','<', 2)
            ->where('item_material.valor_venda_promocional', $desconto)
            ->update([
            'valor_venda_promocional' => 0
            ]);

            DB::table('descontos')
                ->where('descontos.id',$id)
                ->update([
                    'ativo' => 'false',
                    'id_usuario' => $usuario,
                    'data_registro' => $data_atual
                    ]);


            return redirect()->action('DescontoController@index')
                ->with('warning', 'A configuração do desconto foi inativada!');

        }

        elseif ($data_inicio == null && $data_fim == null){

            DB::table('item_material')
            ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material', 'item_catalogo_material.id')
            ->leftJoin('tipo_categoria_material', 'item_catalogo_material.id_categoria_material', 'tipo_categoria_material.id')
            ->leftJoin('descontos', 'tipo_categoria_material.id', 'descontos.id_tp_categoria')
            ->where('descontos.id',$id)
            ->where(function ($query) use ($array_sessao) {
                $query->whereNull('item_material.id_deposito')
                      ->orWhereIn('item_material.id_deposito', $array_sessao);
            })
            ->where('item_catalogo_material.id_categoria_material', $categoria)
            ->where ('descontos.porcentagem', $desconto)
            ->where ('item_material.id_tipo_situacao','<', 2)
            ->update([
            'valor_venda_promocional' => 0
            ]);

            DB::table('descontos')
                ->where('descontos.id',$id)
                ->update([
                    'ativo' => 'false',
                    'id_usuario' => $usuario,
                    'data_registro' => $data_atual
                    ]);


            return redirect()->action('DescontoController@index')
                ->with('warning', 'A configuração do desconto foi inativada!');

        }
        elseif ($data_inicio > 0 && $data_fim == null){

              DB::table('item_material')
              ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material', 'item_catalogo_material.id')
              ->leftJoin('tipo_categoria_material', 'item_catalogo_material.id_categoria_material', 'tipo_categoria_material.id')
              ->leftJoin('descontos', 'tipo_categoria_material.id', 'descontos.id_tp_categoria')
              ->where('descontos.id',$id)
              ->where(function ($query) use ($array_sessao) {
                $query->whereNull('item_material.id_deposito')
                      ->orWhereIn('item_material.id_deposito', $array_sessao);
            })
              ->where('item_catalogo_material.id_categoria_material', $categoria)
              ->where('descontos.porcentagem', $desconto)
              ->where('item_material.data_cadastro','>', $data_inicio)
              ->where('item_material.id_tipo_situacao','<', 2)
              ->update([
                'valor_venda_promocional' => 0
             ]);

             DB::table('descontos')
             ->where('descontos.id',$id)
             ->update([
                'ativo' => 'false',
                'id_usuario' => $usuario,
                'data_registro' => $data_atual
                 ]);



              return redirect()->action('DescontoController@index')
                  ->with('warning', 'A configuração do desconto foi inativada!');
        }
        elseif ($data_inicio == null && $data_fim > 0){

            DB::table('item_material')
            ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material', 'item_catalogo_material.id')
            ->leftJoin('tipo_categoria_material', 'item_catalogo_material.id_categoria_material', 'tipo_categoria_material.id')
            ->leftJoin('descontos', 'tipo_categoria_material.id', 'descontos.id_tp_categoria')
            ->where('descontos.id',$id)
            ->where(function ($query) use ($array_sessao) {
                $query->whereNull('item_material.id_deposito')
                      ->orWhereIn('item_material.id_deposito', $array_sessao);
            })
            ->where('item_catalogo_material.id_categoria_material', $categoria)
            ->where('descontos.porcentagem', $desconto)
            ->where('item_material.data_cadastro','<', $data_fim)
            ->where('item_material.id_tipo_situacao','<', 2)
            ->update([
              'valor_venda_promocional' => 0
           ]);

           DB::table('descontos')
           ->where('descontos.id',$id)
           ->update([
                'ativo' => 'false',
                'id_usuario' => $usuario,
                'data_registro' => $data_atual
               ]);



            return redirect()->action('DescontoController@index')
                ->with('warning', 'A configuração do desconto foi inativada!');
      }
        elseif ($data_inicio > 0 && $data_fim > 0){


            DB::table('item_material')
            ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material', 'item_catalogo_material.id')
            ->leftJoin('tipo_categoria_material', 'item_catalogo_material.id_categoria_material', 'tipo_categoria_material.id')
            ->leftJoin('descontos', 'tipo_categoria_material.id', 'descontos.id_tp_categoria')
            ->where('descontos.id',$id)
            ->where(function ($query) use ($array_sessao) {
                $query->whereNull('item_material.id_deposito')
                      ->orWhereIn('item_material.id_deposito', $array_sessao);
            })
            ->where('item_catalogo_material.id_categoria_material', $categoria)
            ->where('descontos.porcentagem', $desconto)
            ->where('item_material.data_cadastro', '>', $data_inicio)
            ->where('item_material.data_cadastro', '<', $data_fim)
            ->where('item_material.id_tipo_situacao','<', 2)
            ->update([
                'valor_venda_promocional' => 0
            ]);

            DB::table('descontos')
            ->where('descontos.id',$id)
            ->update([
                'ativo' => 'false',
                'id_usuario' => $usuario,
                'data_registro' => $data_atual
                ]);


            return redirect()->action('DescontoController@index')
                  ->with('warning', 'A configuração do desconto foi inativada!');

        }
    }
}
