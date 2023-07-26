<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\ModelCadastroInicial;
use App\Models\ModelItemCatalogo;
use App\Models\ModelCatMaterial;
use App\Models\ModelItemMaterial;
use App\Models\ModelCor;
use App\Models\ModelTamanho;
use App\Models\ModelMarca;

class CadastroInicialController extends Controller
{

   private $objItemMaterial;

    public function __construct(){
        $this->objItemMaterial = new ModelCadastroInicial();
    }

    private function getListaItens(){
        $lista = DB::select("
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
                im.liberacao_venda,
                ic.id_categoria_material as id_cat
            from item_material im
            left join item_catalogo_material ic on (im.id_item_catalogo_material = ic.id)
            left join marca m on (im.id_marca = m.id)
            left join tamanho t on (im.id_tamanho = t.id)
            left join cor c on (im.id_cor = c.id)
            left join tipo_material tm on (im.id_tipo_material = tm.id)
            order by im.data_cadastro desc
        ");
        return $lista;
    }


    public function index(Request $request)
    {        

        $resultCat = DB::select ('select id, nome from tipo_categoria_material');

        //$result = $this->getListaItens();
        $result = DB::table('item_material AS im')
                            ->select('im.data_cadastro','im.id', 'im.ref_fabricante AS ref_fab', 'im.observacao AS obs', 'im.adquirido', 'im.valor_venda', 'im.id_tipo_situacao', 'icm.id_categoria_material AS cat',  'icm.nome AS n1', 'm.nome AS n2', 't.nome AS n3', 'c.nome AS n4', 'tcm.nome AS n5',  'tcm.id AS id_cat','tcm.nome AS nome_cat')
                            ->where('id_tipo_situacao', '1')
                            ->leftjoin('item_catalogo_material AS icm', 'icm.id' , '=', 'im.id_item_catalogo_material')
                            ->leftjoin('tipo_categoria_material AS tcm', 'icm.id_categoria_material' , '=', 'tcm.id')
                            ->leftjoin('marca AS m', 'm.id' , '=', 'im.id_marca')
                            ->leftjoin('tamanho AS t', 't.id' , '=', 'im.id_tamanho')
                            ->leftjoin('cor AS c', 'c.id', '=', 'im.id_cor');
        //$resultCategoria = DB::select ('select id, nome from tipo_categoria_material');
        //$resultSitMat = DB::select ('select id, nome from tipo_situacao_item_material');


        $data_inicio = $request->data_inicio;
        $data_fim = $request->data_fim;
        if ($request->data_inicio){

            $result->where('im.data_cadastro','>=' , $request->data_inicio);
        }
        if ($request->data_fim){
            $result->where('im.data_cadastro','<=' , $request->data_fim);
        }

        $material = $request->material;
        if ($request->material){
            $result->where('icm.nome', 'like', "%$request->material%");
        }

        $obs = $request->obs;
        if ($request->obs){
            $result->where('im.observacao', 'like', "%$request->obs%");
        }

        $ref_fab = $request->ref_fab;
        if ($request->ref_fab){
            $result->where('im.ref_fabricante', '=', $request->ref_fab);
        }

        $doado = $request->doado;
        if ($request->doado){
            $result->where('im.adquirido', '=', "$request->doado");
        }
        $categoria = $request->categoria;
        if ($request->categoria){
            $result->where('tcm.id', '=', "$request->categoria");
        }
                
        $total = $request->compra;
        if ($request->compra){
            $result->where('im.adquirido', '=', "$request->compra");
        }

        
        $result = $result->orderBy('im.id', 'DESC')->paginate(500);

        $contar = $result->count('im.id');

        

        return view('cadastroinicial/gerenciar-cadastro-inicial', compact('obs', 'contar', 'result','categoria', 'data_inicio', 'data_fim', 'material', 'resultCat'));


    }


    public function create()
    {
        $sql ="Select
                    it.id,
                    it.nome,
                    c.nome categoria
                    from item_catalogo_material it
                    join tipo_categoria_material c on it.id_categoria_material = c.id
                ";

        $resultItem = DB::select($sql);

        return view('cadastroinicial/incluir-cadastro-inicial-item', compact('resultItem'));
    }

    public function show($id)
    {
        //
    }




    public function formEditar ($id, $id_cat)
    {

        $itemmat = DB::table('item_material AS im')
                        ->select('im.id AS id_item', 'im.data_cadastro', 'im.valor_venda', 'icm.id AS id_item_cat', 'icm.id_categoria_material AS id_cat_item', 'icm.nome AS nome_item', 'tcm.nome AS nome_categ')
                        ->leftJoin('item_catalogo_material AS icm', 'im.id_item_catalogo_material', 'icm.id' )
                        ->leftJoin('tipo_categoria_material AS tcm', 'icm.id_categoria_material', 'tcm.id')
                        ->where('im.id',$id)
                        ->get();


        $itemlista = DB::table('item_material AS im')
                        ->select('im.id AS id_item', 'im.id_tipo_situacao', 'im.data_cadastro', 'im.valor_venda', 'icm.id_categoria_material AS id_item_cat', 'im.observacao AS obs', 'im.ref_fabricante AS ref_fab', 'icm.nome AS nomeitem', 'tcm.nome AS nome_categ', 'ts.nome AS n1', 'im.id_marca', 'm.nome AS n2', 'im.id_tamanho AS id_tam', 't.nome AS n3', 'im.id_cor', 'c.nome AS n4', 'im.id_tp_sexo AS id_sexo', 'tm.id AS tp_mat', 'tm.nome AS n7', 'sex.nome AS n5', 'im.id_fase_etaria AS fase_e', 'fe.nome as n6')
                        ->leftJoin('item_catalogo_material AS icm', 'im.id_item_catalogo_material', 'icm.id' )
                        ->leftJoin('tipo_categoria_material AS tcm', 'icm.id_categoria_material', 'tcm.id')
                        ->leftjoin('tipo_situacao_item_material AS ts', 'im.id_tipo_situacao', '=', 'ts.id' )
                        ->leftjoin('marca AS m', 'im.id_marca', '=', 'm.id')
                        ->leftjoin('tamanho AS t', 'im.id_tamanho' , '=', 't.id')
                        ->leftjoin('tipo_material AS tm', 'im.id_tipo_material' , '=', 'tm.id')
                        ->leftjoin('cor AS c', 'im.id_cor', '=', 'c.id')
                        ->leftjoin('tipo_sexo AS sex', 'im.id_tp_sexo', '=', 'sex.id')
                        ->leftjoin('fase_etaria AS fe', 'im.id_fase_etaria', '=', 'fe.id')
                        ->where('im.id',$id)
                        ->get();





        $lista = DB::table('item_catalogo_material AS icm')
                        ->select('icm.id AS id_item_cat', 'icm.nome AS nome_item',  'icm.id_categoria_material AS categ_mat', 'tcm.id AS id_categ', 'tcm.nome AS nome_categ')
                        ->leftjoin('tipo_categoria_material AS tcm', 'icm.id_categoria_material', 'tcm.id')
                        ->where('tcm.id',$id_cat)
                        //->orderBy('nome_item','ASC')
                        ->get();

       
        $tamanho = DB::table('tipo_categoria_material AS tcm')
                        ->select('tcm.id', 't.id AS id_tam', 'tcm.id AS id_cat',  't.nome AS n3')
                        ->leftjoin('tamanho AS t', 'tcm.id' , '=', 't.id_categoria_material')
                        ->where('tcm.id', $id_cat)
                        ->orderBy('n3','ASC')
                        ->get();

        $marca = DB::table('tipo_categoria_material AS tcm')
                    ->select('tcm.id', 'm.id AS id_marca', 'tcm.id AS id_cat', 'm.nome AS n2')
                    ->leftjoin('marca AS m', 'tcm.id', '=', 'm.id_categoria_material')
                    ->where('tcm.id', $id_cat)
                    ->orderBy('n2','ASC')
                    ->get();

        $cor = DB::table('tipo_categoria_material AS tcm')
                    //->distinct('tcm.id')
                    ->select('tcm.id', 'tcm.id AS id_cat', 'c.id AS id_cor', 'c.nome AS n4')
                    ->leftjoin('cor AS c', 'tcm.id', '=', 'c.id_categoria_material')
                    ->where('tcm.id', $id_cat)
                    ->orderBy('n4','ASC')
                    ->get();

       
        $tipo = DB::table('tipo_material AS tp')
                        ->select('tp.id AS tp_id', 'tp.nome AS n8')
                        ->get();

        $sexo = DB::table('tipo_sexo AS tp_sex')
                        ->select('tp_sex.id AS sexid', 'tp_sex.nome AS n7')
                        ->get();

        $etaria = DB::table('fase_etaria AS fe')
                        ->select('fe.id AS id', 'fe.nome')
                        ->get();

        //dd($request);

        return view ('cadastroinicial/editar-cadastro-inicial', compact('lista', 'itemlista', 'cor', 'tamanho', 'itemmat', 'marca', 'tipo','sexo', 'etaria' ));
    }

    public function update (Request $request, $id)
    {
        $ativo = isset($request->checkAdq) ? 1 : 0;

         DB::table('item_material')
            ->where('id', $id)
            ->update([
                'id_item_catalogo_material' => $request->input('item_cat'),
                'observacao' => $request->input('obs'),
                'valor_venda' => $request->input('valor'),
                'id_tamanho' => $request->input('tamanho'),
                'id_marca' => $request->input('marca'),
                'id_cor' => $request->input('cor'),
                'id_tipo_material' => $request->input('tp_mat'),
                'id_tp_sexo' => $request->input('sexo'),
                'id_fase_etaria' => $request->input('etaria'),
                'ref_fabricante' => $request->input('ref_fab'),
                'data_validade' => $request->input('dt_validade'),

                'adquirido' => $ativo,
        ]);

        return redirect()->action('CadastroInicialController@index');

    }

    public function destroy($id)
    {
         DB::delete('delete from item_material where id = ?' , [$id]);

        return redirect()->action('CadastroInicialController@index');
    }

    public function getCategoria ($id){

        $sql = "Select
                    c.id,
                    c.nome
                    from item_catalogo_material it
                    join tipo_categoria_material c on it.id_categoria_material = c.id
                where it.id = $id";

        $result = DB::select($sql);
        $html;
        $idCat =$result[0]->id;

        foreach ($result as $valors ) {
            //$html = '<label id="categoria" class="col-sm-2 col-form-label">'.$valors->nome.'</label>';
            $html= '<input type="hidden" id="id_categoria"  value="'.$valors->id.'">';
        }

        $sql2 = "Select id, nome from marca where id_categoria_material = $idCat";
        $result2 = DB::select($sql2);

        $sql3 = "Select id, nome from tamanho where id_categoria_material = $idCat";
        $result3 = DB::select($sql3);

        $sql4 = "Select id, nome from cor where id_categoria_material = $idCat";
        $result4 = DB::select($sql4);

        $sql5 = "Select id, nome from tipo_material where id_categoria_material = $idCat";
        $result5 = DB::select($sql5);

        $sql6 = "Select id, nome from fase_etaria where id_categoria_material = $idCat";
        $result6 = DB::select($sql6);

        $sql7 = "Select id, nome from tipo_sexo";
        $result7 = DB::select($sql7);

        $html.='<div class="table-responsive">';
        $html.='<table class="table table-bordered table-striped mb-0">';
        $html.='<tr><td>Marca</td> <td>'.getCombo($result2,'marca', 0).'</td></tr>';
        $html.='<tr><td>Tamanho</td> <td>'.getCombo($result3,'tamanho', 0).'</td></tr>';
        $html.='<tr><td>Cor</td> <td>'.getCombo($result4,'cor', 0).'</td></tr>';
        $html.='<tr><td>Tipo Material</td> <td>'.getCombo($result5,'tp_mat', 0).'</td></tr>';
        $html.='<tr><td>Fase Etária</td> <td>'.getCombo($result6,'fase_etaria', 0).'</td></tr>';
        $html.='<tr><td>Sexo</td> <td>'.getCombo($result7,'sexo', 0).'</td></tr>';
        $html.='</table>';
        $html.='</div>';

        return $html;
    }

    public function getFormCadastro(Request $request, $id){

        $sql8 = "select d.id, d.nome||' / '||e.nome nome
                from deposito d left join
                tipo_estoque e on e.id = d.id_tp_estoque";
        $result8 = DB::select($sql8);

        $sql9 = "Select id, nome from tipo_embalagem";
        $result9 = DB::select($sql9);

        $sql10 = "Select id, nome from tipo_unidade_medida";
        $result10 = DB::select($sql10);

        $html='<div class="table-responsive">';
        $html.='<table class="table table-bordered table-striped mb-0">';
        $html.='<tr><td>Deposito *</td> <td>'.getCombo($result8,'deposito', 1).'</td></tr>';
        $html.='<tr><td>Embalagem </td> <td>'.getCombo($result9,'embalagem', 0).'</td></tr>';
        $html.='<tr><td>Qtd Embalagem</td> <td><input type="text" name="qtdEmb" id="qtdEmb"></td></tr>';
        $html.='<tr><td>Código Fabricante</td> <td><input type="numeric" name="ref_fab" id="ref_fab"></td></tr>';
        $html.='<tr><td>Unidade Medida </td> <td>'.getCombo($result10,'und_med', 0).'</td></tr>';
        $html.='<tr><td>Comprado</td><td><input type="checkbox" id="checkAdq" name="checkAdq" switch="bool" class="valCheck"/><label for="checkAdq" data-on-label="Sim" data-off-label="Não"></label></td>';

        //dd($_REQUEST);

        if($_REQUEST['adquirido'] = 'true'){

            $html.='<tr><td>Valor aquisição</td><td><input value="0.00" type="number" step="0.01" id="vlr_aqs" name="vlr_aqs" required></td></tr>';
            $html.='</table>';
            $html.='</div>';
        }
        else if ($_REQUEST['adquirido'] = 'false'){

           $html.='</table>';
           $html.='</div>';
        }


        return $html;
    }

    public function getValor($id){

        $sql= "Select valor_minimo, valor_medio, valor_maximo, valor_marca, valor_etiqueta from item_catalogo_material where  id = $id";
        $result = DB::select($sql);

        $html='<div class="table-responsive">';
        $html.='<table class="table table-bordered table-striped mb-0">';
        $html.='<tr><td>Lista valores <br><input type="checkbox" id="checkVal" name="checkVal" switch="bool" checked class="valCheck" /><label for="checkVal" data-on-label="Sim" data-off-label="Não"></label></td>';
        $html.='<td>Avariado<br><input type="checkbox" id="checkAvariado" name="checkAvariado" switch="bool" class="valCheck" /><label for="checkAvariado" data-on-label="Sim" data-off-label="Não"></label></td></tr>';
        $html.='<tr><td>Valor de Venda</td>
                <td>
                    <div id="DivValorInput">
                        <input type="radio" id="valor_minimo" name="valor_venda" value="'.$result[0]->valor_minimo.'">
                        <label for="valor_minimo">Mínimo R$'.$result[0]->valor_minimo.'</label><br>
                        <input type="radio" id="valor_medio" name="valor_venda" value="'.$result[0]->valor_medio.'">
                        <label for="valor_medio">Médio R$'.$result[0]->valor_medio.'</label><br>
                        <input type="radio" id="valor_maximo" name="valor_venda" value="'.$result[0]->valor_maximo.'">
                        <label for="valor_medio">Máximo R$'.$result[0]->valor_maximo.'</label><br>
                        <input type="radio" id="valor_marca" name="valor_venda" value="'.$result[0]->valor_marca.'">
                        <label for="valor_marca">Marca R$'.$result[0]->valor_marca.'</label><br>
                        <input type="radio" id="valor_etiqueta" name="valor_venda" value="'.$result[0]->valor_etiqueta.'">
                        <label for="valor_etiqueta">Etiqueta R$'.$result[0]->valor_etiqueta.'</label><br>
                    </div>
                </td></tr>';
        $html.='</table>';
        $html.='</div>';

        return $html;
    }

    public function getValorVariado($id){

        if($_REQUEST['listaValor'] == 'true' && $_REQUEST['avariado'] =='false' ){

            $sql= "Select valor_minimo, valor_medio, valor_maximo, valor_marca, valor_etiqueta from item_catalogo_material where  id = $id";
            $result = DB::select($sql);

            $html ='<input type="radio" id="valor_minimo" name="valor_venda" value="'.$result[0]->valor_minimo.'">
                    <label for="valor_minimo">Mínimo R$'.$result[0]->valor_minimo.'</label><br>
                    <input type="radio" id="valor_medio" name="valor_venda" value="'.$result[0]->valor_medio.'">
                    <label for="valor_medio">Médio R$'.$result[0]->valor_medio.'</label><br>
                    <input type="radio" id="valor_maximo" name="valor_venda" value="'.$result[0]->valor_maximo.'">
                    <label for="valor_maximo">Máximo R$'.$result[0]->valor_maximo.'</label><br>
                    <input type="radio" id="valor_marca" name="valor_venda" value="'.$result[0]->valor_marca.'">
                    <label for="valor_marca">Marca R$'.$result[0]->valor_marca.'</label><br>
                    <input type="radio" id="valor_etiqueta" name="valor_venda" value="'.$result[0]->valor_etiqueta.'">
                    <label for="valor_etiqueta">Etiqueta R$'.$result[0]->valor_etiqueta.'</label><br>';


                    //dd($_REQUEST['avariado']);

        }else if($_REQUEST['avariado'] =='true'){

            $sql= "Select valor id, valor nome from valor_avariado";
            $result = DB::select($sql);

            $html= getCombo($result,'valor_venda', 1);
        }else{
            $html='<input type="number" step="0.01" id="valor_venda" name="valor_venda" required>';
        }
        return $html;
    }

    public function getFormCadastroFinal($id){

        $sql= "Select id, prateleira nome from localizador";
        $result = DB::select($sql);

        $html='<div class="table-responsive">';
        $html.='<table class="table table-bordered table-striped mb-0">';
        $html.='<tr><td>Quantidade *</td><td><input type="text" id="qtdItens" name="qtdItens" required /></td>';
        $html.='<tr><td>Data validade</td><td><input class="form-control" type="date" value="" id="dt_validade" name="dt_validade"></td>';
        $html.='<tr><td>Localizador</td> <td>'.getCombo($result,'localizador', 0) .'</td></tr>';
        $html.='</table>
                <label class="text-muted">Observação</label>
                <textarea id="textarea" name="observacao" class="form-control" maxlength="225" rows="3" placeholder=""></textarea>
                <div class="col-12 mt-3" style="text-align: right;">
                <div class="row">
                <div class="col">
                <a href="/gerenciar-cadastro-inicial/incluir"><input class="btn btn-danger btn-block" type="button" value="Cancelar">
                </a>
                </div>
                <div class="col">
                <button type="submit" class="btn btn-success btn-block">Confirmar</button>
                </div>
                </div>
                </div>';
        $html.='</div>';

        return $html;
    }

    public function getComposicao ($id){

        $sql = "select
                c.id id_item,
                c.nome nome_item,
                emb.sigla embalagem,
                um.sigla unidade_medida,
                ic.quantidade,
                ic.id
                from composicao_item_catalogo ic
                left join item_catalogo_material c on ic.id_item_catalogo_composto =c.id
                left join tipo_embalagem emb on ic.id_tipo_embalagem = emb.id
                left join tipo_unidade_medida um on ic.id_tipo_unidade_medida = um.id
                where ic.id_item_catalogo =$id";

                $result = DB::select($sql);

                return view('catalogo/dialog-composicao-item', compact('result'));
    }

    public function store(Request $request)
    {

            $Adquirido = isset($request->checkAdq) ? 1 : 0;
            $Avariado = isset($request->checkAvariado) ? 1 : 0;

            for ($i=0; $i < $request->input('qtdItens'); $i++){

            //???????????? Liberacao_venda, id_tipo_situacao, valor_aquisicao,valor_venda_promocional, ?id_usuario?
            DB::table('item_material')->insert([
            'id_item_catalogo_material' => $request->input('item_material'),
            'observacao' => $request->input('observacao'),
            'data_cadastro' => date("m-d-Y"),
            'id_usuario_cadastro'=> session()->get('usuario.id_usuario'),
            'id_tipo_embalagem' => $request->input('embalagem'),
            'id_tipo_unidade_medida' => $request->input('und_med'),
            'quantidade_embalagem' => $request->input('qtdEmb'),
            'adquirido' => $Adquirido,
            'valor_venda' => $request->input('valor_venda'),
            'id_marca' => $request->input('marca'),
            'id_tamanho' => $request->input('tamanho'),
            'id_cor' => $request->input('cor'),
            'id_tipo_material' => $request->input('tp_mat'),
            'id_fase_etaria' => $request->input('fase_etaria'),
            'id_tp_sexo' => $request->input('sexo'),
            'id_deposito' => $request->input('deposito'),
            'valor_aquisicao' => $request->input ('vlr_aqs'),
            'ref_fabricante' => $request->input ('ref_fab'),
            'avariado' => $Avariado,
            'data_validade' => $request->input('dt_validade'),
            'liberacao_venda' => 0,
            'id_tipo_situacao' => '1',
        ]);
        }

        //dd($request);

        return redirect()->action('CadastroInicialController@index');
    }

}
