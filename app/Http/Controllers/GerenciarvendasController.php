<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ModelItemCatalogo;
use App\Models\ModelCatMaterial;
use App\Models\ModelVendas;
use Illuminate\Pagination\Paginator;


class GerenciarVendasController extends Controller
{

    private $objItemCatalogo;
    private $objTipoMaterial;
    private $objVendas;
    //private $totalPage = 3;

    public function __construct(){
        $this->objItemCatalogo = new ModelItemCatalogo();
        $this->objTipoMaterial = new ModelCatMaterial();
        $this->objVendas = new ModelVendas();
    }

    public function index(Request $request){

        $resultSitVenda = DB::select ('select id, nome from tipo_situacao_venda');

        $sessao = session()->get('usuario.depositos');

        $array_sessao = explode(",", $sessao);

        $result = DB::table('venda AS v')
        ->select ('v.id', 'v.data', 'v.id_pessoa', 'im.id_deposito', 'v.id_usuario', 'v.id_tp_situacao_venda', 'p.nome AS nome_cliente', 'pu.nome AS nome_usuario',  'v.valor', 't.nome as sit_venda', 't.id AS idt')
        ->leftjoin ('pessoa AS p',  'v.id_pessoa', 'p.id')
        ->leftjoin ('usuario AS u',  'u.id', 'v.id_usuario')
        ->leftjoin ('usuario_deposito AS ud',  'u.id', 'ud.id_usuario')
        ->leftjoin ('pessoa AS pu', 'u.id_pessoa', 'pu.id')
        ->leftjoin ('tipo_situacao_venda AS t', 't.id', 'v.id_tp_situacao_venda')
        ->leftJoin('venda_item_material AS vim', 'v.id', 'vim.id_venda')
        ->leftJoin('item_material AS im', 'vim.id_item_material', 'im.id')
        ->where(function ($query) use ($array_sessao) {
            $query->whereNull('im.id_deposito')
                  ->orWhereIn('im.id_deposito', $array_sessao);
        })
        ->groupBy('v.id', 'v.data', 'v.id_pessoa', 'im.id_deposito', 'p.nome', 'pu.nome', 't.nome', 't.id');

        $situacao = $request->situacao;

        $cliente = $request->cliente;

        $data_inicio = $request->data_inicio;

        $data_fim = $request->data_fim;

        $id_venda = $request->id_venda;

        if ($request->situacao){
            $result->where('v.id_tp_situacao_venda', $request->situacao);
        }

        if ($request->cliente){
            $result->where('p.nome', 'ilike', ("%$request->cliente%"));
        }

        if ($request->id_venda){
            $result->where('v.id', '=', $id_venda);
        }

        if ($request->data_inicio){

            $result->whereDate('v.data','>=' , $request->data_inicio);
        }

        if ($request->data_fim){

            $result->whereDate('v.data','<=' , $request->data_fim);
        }

        

        $result = $result->orderBy('v.id', 'DESC')->paginate(100);

        $contar = $result->count();

       return view('vendas/gerenciar-vendas', compact('result','data_inicio', 'data_fim','contar', 'resultSitVenda', 'situacao', 'cliente'));
    }




    public function store(Request $request){
        //$ativo = isset($request->ativo) ? 1 : 0;
        //$composicao = isset($request->composicao) ? 1 : 0;

        DB::table('venda')->insert([

            'data' => $request->input('data_venda'),
            'id_pessoa' => $request->input('cliente'),
            'id_usuario' => $request->input('vendedor'),
            'valor' => $request->input('valor_venda'),
            'id_tp_situacao_venda' => $request->input('sit_venda'),
        ]);
        $result= $result= $this->getListaVendasAll();
    }




    public function edit($id){

        $resultVenda = $this->objVendas->all();

        $result =DB::table('venda')->where('id',$id)->get();
        return view('vendas/gerenciar-vendas', compact('resultVenda', 'result'));

        $resultVenda = DB::select("select id_pessoa, id_usuario from venda where id = $id ");

        return view('/vendas/gerenciar-vendas/alterar', compact("resultVendas"));


    }


    public function delete($id){


            DB::table ('item_material')
            ->whereRaw('id IN (select id_item_material from venda_item_material where id_venda='.$id.')')
            ->update(['id_tipo_situacao' => 1]);

            DB::delete('delete from venda where id = ?' , [$id]);


        return redirect()->action('GerenciarvendasController@index');

    }

// Ação de finalizar o pagamento da venda e
// registrar as situações na tabela item material e venda

    public function finalizar ($id){

        $alerta = DB::select ("
        Select
        v.id,
        v.data
        from venda v
        where v.id=$id
        ");

        ///Cálculo do total de desconto
        $desconto = DB::table ('item_material')
                ->leftjoin('venda_item_material', 'item_material.id', 'venda_item_material.id_item_material')
                ->leftjoin('venda', 'venda_item_material.id_venda', 'venda.id')
                ->where ('venda_item_material.id_venda', '=', $id)
                ->sum(DB::raw('item_material.valor_venda * item_material.valor_venda_promocional'));

        $desconto = round($desconto);

 

        $total_preco = DB::table ('venda')
        ->leftjoin('venda_item_material', 'venda.id', '=', 'venda_item_material.id_venda')
        ->leftjoin('item_material', 'venda_item_material.id_item_material', '=', 'item_material.id')
        ->where ('id_venda', '=', $id)
        ->sum('item_material.valor_venda');

        $total_preco =  round($total_preco); 

        //dd($total_preco);

        $valor = ($total_preco - $desconto);

        //dd($valor);

        $valor = round($valor);

        $pago =  DB::table ('venda')
        ->leftjoin('pagamento', 'venda.id', 'pagamento.id_venda' )
        ->where ('pagamento.id_venda', $id)
        ->sum('pagamento.valor');

        $pago = round($pago);


        //dd($valor == $pago );
        

        $sit_ven =  DB::table ('venda')
        ->where ('id', '=', $id)
        ->value('id_tp_situacao_venda');


       // dd($sit_ven, $pago, $valor);
        //dd($sit_ven == 2 && $pago = $valor);

        if ($sit_ven == 1){


            return redirect()
            ->back()
            ->with('warning', 'Altere a situação para "Pagar" clicando em "Finalizar" na tela de "Alterar venda.');
            //return view ('vendas/alerta-venda', compact('alerta'));

        }elseif ($valor > $pago){

                return redirect()
                ->back()
                ->with('warning', 'Existe um erro entre o valor da venda e o valor pago.');
                //return view ('vendas/alerta-venda', compact('alerta'));


        } elseif ($sit_ven == 3 && $pago == $valor) {

            return redirect()
                ->back()
                ->with('warning', 'Esta venda foi finalizada e não pode ser paga novamente.');

        } elseif ($sit_ven == 2 && $pago == $valor){

            DB::table ('venda')
            ->where('id', $id)
            ->update(['id_tp_situacao_venda' => 3]);


            return redirect()
                ->action('GerenciarvendasController@index')
                ->with('message', 'O pagamento foi realizado com sucesso e a venda finalizada.');

        }

        
        return redirect()
        ->action('GerenciarvendasController@index')
        ->with('warning', 'Ocorreu um erro não identificado.');

    }



}
