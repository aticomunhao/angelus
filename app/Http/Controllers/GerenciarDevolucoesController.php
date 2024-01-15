<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class GerenciarDevolucoesController extends Controller
{

    public function index(Request $request){ 

    $result = DB::table('devolucoes')
                            ->select('devolucoes.id','devolucoes.data', 'devolucoes.id_venda', 'devolucoes.data_venda', 'devolucoes.id_pessoa', 'devolucoes.id_usuario', 'devolucoes.id_item_devolvido', 'devolucoes.status', 'p.nome AS nome_c', 'pu.nome AS nome_u')
                            ->leftjoin('venda', 'devolucoes.id_venda', 'venda.id' )
                            ->leftjoin ('pessoa AS p',  'devolucoes.id_pessoa','p.id')
                            ->leftjoin ('usuario',  'devolucoes.id_usuario','usuario.id')
                            ->leftjoin ('pessoa AS pu',  'devolucoes.id_usuario','pu.id')
                            ->leftjoin('item_material', 'devolucoes.id_item_devolvido' ,'item_material.id')
                            ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material' , '=', 'item_catalogo_material.id');
        


        $data_inicio = $request->data_inicio;
        $data_fim = $request->data_fim;

        if ($request->data_inicio){
            $result->where('data','>=' , $request->data_inicio);
        }
        if ($request->data_fim){
            $result->where('data','<=' , $request->data_fim);
        }

        $id_dev = $request->id_dev;
        if ($request->id_dev){
            $result->where('id', '=', $request->id_dev);
        }

        $id_venda = $request->id_venda;
        if ($request->id_venda){
            $result->where('id_venda', '=', $request->id_venda);
        }

        $cliente = $request->cliente;
        if ($request->cliente){
            $result->where('id_pessoa', 'LIKE', "%$request->cliente%");
        }
        $result = $result->orderBy('id', 'DESC')->paginate(100);


        return view('devolucoes/gerenciar-devolucoes', compact('result', 'data_inicio', 'data_fim', 'cliente', 'id_dev', 'id_venda'));


    }

    public function store(Request $request){

        $form = DB::table('venda')
                            ->select('venda.id AS id_venda', 'pessoa.id AS id_p', 'pessoa.nome AS nome_p', 'item_catalogo_material.nome AS nome_i', 'item_material.id AS id_mat', 'venda.data', 'item_material.devolvido')
                            ->leftjoin('pessoa', 'venda.id_pessoa', 'pessoa.id')
                            ->leftjoin('venda_item_material', 'venda.id', 'venda_item_material.id_venda')
                            ->leftjoin('item_material', 'venda_item_material.id_item_material', 'item_material.id')
                            ->leftjoin('item_catalogo_material', 'item_material.id_item_catalogo_material','item_catalogo_material.id');
        

        $cliente = $request->cliente;

        if($request->cliente){
            $form->where('venda.id_pessoa','ilike' , "%$request->cliente%");
        }
        
        $data_venda = $request->data_venda;

        if ($request->data_venda){
            $form->where('venda.data', '=', $request->data_venda);
        }

        $id_venda = $request->id_venda;
        
        if ($request->id_venda){
            $form->where('venda.id', '=', $request->id_venda);
        }

        $form = $form->where('item_material.devolvido', '<>', '1')->where('item_material.id_tipo_situacao', '=', '2')->orderBy('nome_p','DESC','id_venda','DESC')->paginate(100);



        return view('devolucoes/criar-devolucao', compact('form'));

    }


    public function create($id_p, $id_venda,$data,$id_mat){


        $today = Carbon::today();
    
        DB::table('devolucoes')->insert([
            'data' => $today,
            'id_pessoa' => $id_p,
            'id_usuario'=> session()->get('usuario.id_usuario'),
            'data_venda' => $data,
            'id_venda' => $id_venda,
            'id_item_devolvido' => $id_mat,
            'status' => "1",
        ]);

        DB::table('item_material')
        ->Where('item_material.id', '=', $id_mat)
        ->update([
            'devolvido' => "true",
        ]);
        
        return redirect()->action('GerenciarDevolucoesController@index')
        ->with('message', 'O registro da devolução foi criado com sucesso!');

    }


    public function delete($id){

        DB::table ('item_material')
        ->select('item_material.id_tipo_situacao', 'item_material.devolvido')
        ->leftJoin('devolucoes', 'item_material.id', 'devolucoes.id_item_devolvido' )
        ->whereRaw('id_item_devolvido IN (select id_item_devolvido from devolucoes where id ='.$id.')')
        ->update(['item_material.id_tipo_situacao' => 2,
                'item_material.devolvido' => false
        ]);

        DB::delete('delete from devolucoes where id = ?' , [$id]);


    return redirect()->action('GerenciarDevolucoesController@index')
    ->with('warning', 'O registro de devolução foi excluido!');


    }

    public function vincular($id){


       $result = DB::select("select id, data, id_pessoa, id_usuario from devolucoes where id = $id ");

       $item = DB::select("
       select
           im.id as imat,
           ic.nome as nome_dev,
           im.valor_venda
       from item_material im
       left join item_catalogo_material ic on (im.id_item_catalogo_material = ic.id)
       left join devolucoes d on (d.id_item_devolvido = im.id)
       where d.id = $id
        ");

        $substituto  = DB::select("
        select
            im.id as imat,
            ic.nome as nome_item,
            im.valor_venda
            from itens_substitutos isub 
            left join item_material im on (isub.id_item_material = im.id)
            left join item_catalogo_material ic on (im.id_item_catalogo_material = ic.id)
            left join devolucoes d on (d.id_item_devolvido = im.id)
            where isub.id_devolucao = $id
            ");


        //dd($resultDev);

       return view('/devolucoes/gerenciar-substitutos', compact('result', 'item', 'substituto'));


    }

    public function getItem($id){


       $item = DB::select("
            select
                im.id,
                ic.nome nome,
                im.data_cadastro data_cadastro,
                m.nome marca,
                t.nome tamanho,
                c.nome cor,
                tm.nome tipo_material,
                im.valor_venda,
                im.valor_venda_promocional,
                im.liberacao_venda
            from item_material im
            left join item_catalogo_material ic on (im.id_item_catalogo_material = ic.id)
            left join marca m on (im.id_marca = m.id)
            left join tamanho t on (im.id_tamanho = t.id)
            left join cor c on (im.id_cor = c.id)
            left join tipo_material tm on (im.id_tipo_material = tm.id)
            where im.id = $id and im.id_tipo_situacao = 1
        ");
        if ($item){
             return view('devolucoes/area-confirmacao', compact('item'));
        }
        return '<div class="alert alert-danger" role="alert">Nenhum registro encontrado!</div>';
    }

     public function setItemLista($id_item, $id_dev){

        DB::table('itens_substitutos')->insert([
            'id_devolucao' => $id_dev,
            'id_item_material' => $id_item,
        ]);

        $listaItemDev = $this->getListaDev($id_dev);

        DB::table ('item_material')
            ->where('id', $id_item)
            ->update(['id_tipo_situacao' => 8]);

        return view('devolucoes/gerenciar-substitutos', compact('listaItemDev'));
     }


     public function removeItemLista($id_item, $id_dev){
        DB::table ('item_material')
            ->where('id', $id_item)
            ->update(['id_tipo_situacao' => 1]);

        DB::table ('itens_substitutos')
        ->where('id', $id_dev)
        ->where('id_item_material', $id_item)
        ->delete();
        $listaItemDev = $this->getListaDev($id_dev);
        return view('devolucoes/gerenciar-substitutos', compact('listaItemDev'));
    }


    public function cancelarDev($id_dev){
        DB::table ('item_material')
            ->whereRaw('id IN (select id_item_material from itens_substituidos where id_devolucao='.$id_dev.')')
            ->update(['id_tipo_situacao' => 1]);

        DB::table ('itens_substituidos')
        ->where('id_devolucao', $id_dev)
        ->delete();

        $listaItemDev = $this->getListaDev($id_dev);
        return view('devolucoes/gerenciar-devolucoes', compact('listaItemDev'));
    }


    public function concluirDev($id_dev, $vlr_total){
        DB::table ('devolucoes')
            ->where('id', $id_dev)
            ->update(['status' => 2]);

        $listaItemDev = $this->getListaDev(0);

        return view('devolucoes/gerenciar-devolucoes', compact('listaItemDev'));

    }


    public function getListaDev($id_dev){

         $result = DB::select("
            select
            ist.id_devolucao,
            ic.nome nomemat,
            ist.id_item_material,
            1 as qtd
            from itens_substitutos ist
            left join item_material im on ist.id_item_material = im.id
            left join item_catalogo_material ic on im.id_item_catalogo_material = ic.id
            where id_venda =$id_dev");

        return $result;
     }

     public function edit($id_dev){


        $dev = DB::select ("
        Select
        d.id,
        pe.cpf,
        pe.nome as nomepes,
        d.data
        from devolucao d
        left join pessoa pe on (pe.id = d.id_pessoa)
        where d.id=$id_dev
        ");

        $item = DB::select ("
        Select
        vi.id_item_material,
        im.valor_venda,
        ic.nome
        from devolucao d
        left join itens_substitutos ist on (d.id = ist.id_devolucao)
        left join item_material im on (im.id = ist.id_item_material)
        left join item_catalogo_material ic on (ic.id = im.id_item_catalogo_material)
        where v.id=$id_dev
        ");


        return view('devolucao/registrar-substituicao-editar', compact('dev', 'item'));

     }

     public function fimEdicao($id){

        $dev = DB::select ("
        Select
        d.id,
        pe.cpf,
        pe.nome as nomepes,
        d.data
        from devolucao d
        left join pessoa pe on (pe.id = v.id_pessoa)
        where d.id=$id
        ");


        $total = DB::table ('devolucao AS d')
            ->leftjoin('itens_substitutos AS ist', 'd.id', 'ist.id_devolucao')
            ->leftjoin('item_material AS im', 'ist.id_item_material', 'im.id')
            ->where('d.id','=', $id)
            ->sum('im.valor_venda');


        DB::table ('devolucao')
            ->where('id', $id)
            ->update(['status' => 2]);

        return redirect()->action('GerenciarDevolucoesController@index');
      

    }



}
