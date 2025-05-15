<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;

class PDFController extends Controller
{
   /*

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function generatePDF(Request $request)

    {

         //$request->session()->put('data', "2022-03-10");
        $sessao = session()->get('usuario.depositos');
        $array_sessao = explode(",", $sessao);
        $data_atual = (\Carbon\carbon::now()->toDateTimeString());
        $nr_ordem = 1;
        $vendas = DB::select ('select id_item_material from venda_item_material');
    
        $resultCategorias = DB::select ("select distinct(tcm.id), tcm.id, tcm.nome from tipo_categoria_material tcm left join item_material im on (im.id_item_catalogo_material = tcm.id) order by tcm.nome");   
    
        $itemmaterial = DB::select ("select distinct(icm.nome), id, nome from item_catalogo_material icm order by nome"); 

    $resultItens = DB::table('item_material AS im')
                                        ->select('icm.nome', 'im.adquirido','im.adquirido', 'im.ref_fabricante as fab', 'im.data_cadastro', 'v.data', 'tcm.nome AS ncat', 'im.valor_venda', DB::raw('count(*) as qtd'), DB::raw('sum(valor_venda) as total'))
                                        ->leftjoin('item_catalogo_material AS icm', 'im.id_item_catalogo_material','icm.id')
                                        ->leftJoin('tipo_categoria_material AS tcm', 'icm.id_categoria_material', 'tcm.id' )
                                        ->leftjoin('venda_item_material AS vim','im.id','vim.id_item_material')
                                        ->leftjoin('venda AS v','vim.id_venda', 'v.id')
                                        ->where(function ($query) use ($array_sessao) {
                                            $query->whereNull('im.id_deposito')
                                                  ->orWhereIn('im.id_deposito', $array_sessao);
                                        })
                                        ->groupBy('im.ref_fabricante', 'icm.nome', 'im.adquirido', 'im.data_cadastro', 'v.data', 'tcm.nome', 'im.valor_venda');
    
    
        $data = $request->data;
        $categoria = $request->categoria;
        $item = $request->item;
        $compra = $request->compra;
        $cod_fab = $request->cod_fab;
    
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
            $resultItens->whereIn('icm.id', $item);
        }

        if ($cod_fab !== null){
            $resultItens->where('im.ref_fabricante', $cod_fab);
        }
    
        $resultData = $resultItens->get();
        
        $total_itens = $resultData->sum('qtd');
    
        $total_soma = $resultData->sum('total');
    
        $resultItens = $resultItens->orderBy('tcm.nome', 'asc','im.ref_fabricante', 'icm.nome', 'asc')->get(); 

    $data = ['data' => $data,
            'compra' => $compra,
            'resultItens' => $resultItens,
            'total_itens' => $total_itens,
            'total_soma' => $total_soma,
            'cod_fab' => $cod_fab,
            'itemmaterial' => [],
            'resultCategorias' => []];

    

    $options = new Options();

    $options->set('defaultFont', 'Courier');
    $options->set('paper_size', 'a5');
    $options->set('defaultFontSize', 10);
    $options->set('orientation', 'portrait');
    $options->set('margin_top', 10);
    $options->set('margin_bottom', 10);
    $options->set('margin_left', 10);
    $options->set('margin_right', 10);

    
    $pdf = new Dompdf($options);

    $htmlCompleto = View::make('relatorios.inventario-fabricante', $data)->render();

    // procurar a div com id="1"
    $inicio = strpos($htmlCompleto, 'id="1"');

    // verificar se encontrou
    if ($inicio !== false) {
        // encontrar o início da tag <div ... id="1">
        $tagInicio = strrpos(substr($htmlCompleto, 0, $inicio), '<div');
        $htmlCortado = substr($htmlCompleto, $tagInicio);
        
        // opcional: adicionar estrutura básica do HTML para o DOMPDF funcionar corretamente
        $htmlFinal = "<html><head><style>body { font-family: Courier; }</style></head><body>" . $htmlCortado . "</body></html>";
    } else {
        $htmlFinal = $htmlCompleto; // fallback
    }

    $pdf->loadHtml($htmlFinal);
    $pdf->render();

    return $pdf->stream('documento.pdf');

    }
}
