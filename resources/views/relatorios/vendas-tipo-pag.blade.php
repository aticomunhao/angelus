@extends('layouts.master')

@section('title') Relatório de vendas por tipo pagamento @endsection

@section('content')

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">       
                    <form action="/vendas-valor" class="form-horizontal mt-4" method="GET" >
                        @csrf
                    <div class="row">
                        <div class="col">Início:                        
                            <input type="date" class="form-control" name="data_inicio" id="data_inicio" 
                            value="{{ isset($data_inicio) ? $data_inicio : date('Y-m-d') }}">
                        </div>
                        <div class="col">Final:                       
                            <input type="date" class="form-control" name="data_fim"  value="{{$data_fim}}">
                        </div>
                        <div class="col">Tipos:
                            <select class="form-control select2" id="lista1" name="tp_pag[]" multiple>
                                <option value="">Todos</option>
                                    @Foreach($tipo_pag as $tp)
                                    <option value="{{$tp->tid}}" {{ in_array($tp->tid, request()->get('tp_pag', [])) ? 'selected' : '' }}>{{ $tp->tnome }}</option>
                                    @endForeach
                            </select>
                        </div>    
                        <div class="col">
                            <input class="btn btn-light" type="submit" value="Pesquisar" style="box-shadow: 1px 2px 5px #000000;margin-top:20px;">
                      
                            <a href="/vendas-valor"><input class="btn btn-light" type="button" value="Limpar" style="box-shadow: 1px 2px 5px #000000;margin-top:20px;"></a>                                      
                        </div>
                    </div>
                    </form>
                </div> 
            </div>
        </div>
    </div>
</div>

<div id='print' class='conteudo'>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">       
                        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">RELATÓRIO DE VENDAS POR TIPO PAGAMENTO</h4>
                        <h4 class="card-title" style="font-size: 12px; text-align: center; color:black;">O período do relatório é: {{ \Carbon\Carbon::parse("$data_inicio")->format('d/m/Y')}} até {{ \Carbon\Carbon::parse("$data_fim")->format('d/m/Y')}}</h4>

                        <div class="row">                   
                            <table class="table table-sm table-striped table-bordered">
                                <thead style="text-align:center;vertical-align:middle;">
                                    <tr style="font-size:15px; background-color:#d2dba1; color:#ffffff;">
                                        <th colspan="1">CÓDIGO</th>
                                        <th colspan="1">DATA</th>
                                        <th colspan="1">CLIENTE</th>
                                        <th colspan="1">TIPO PAGAMENTO</th>
                                        <th colspan="1">VALOR TIPO</th>
                                    </tr>
                                </thead>                      
                                <tbody style="font-size:12px; text-align:center;vertical-align:middle">
                                @php
                                    $currentIdv = null; // Variável para controlar o ID atual
                                    $totalVlrFinal = 0; // Variável para somar vlr_final do ID atual
                                    $exibirDados = true; //  Controle para exibir os dados somente na primeira linha de cada grupo
                                @endphp

                                @foreach ($rela as $ra)
                                    @if ($currentIdv !== $ra->idv)
                                        @if ($currentIdv !== null)
                                            <!-- Exibe o total do ID anterior antes de mudar -->
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>                    
                                        <td style="background-color:paleturquoise;font-size:12px; text-align:right;"><strong>T Venda:</strong></td>
                                        <td style="background-color:paleturquoise;font-size:12px; text-align:right;"><strong>{{ number_format($totalVlrFinal, 2, ',', '.') }}</strong></td>
                                    </tr>
                                @endif
                                @php
                                    // Atualiza o ID atual e reseta o total
                                    $currentIdv = $ra->idv;
                                    $totalVlrFinal = 0;
                                    $exibirDados = true; // Exibe os dados para o novo grupo
                                @endphp
                                @endif

                                <!-- Soma o valor ao total do ID atual -->
                                @php $totalVlrFinal += $ra->pagvalor; @endphp

                                    <!-- Exibe os valores de cada registro -->
                                    <tr>
                                        <td>{{$exibirDados ? $ra->idv : '' }}</td>
                                        <td>{{$exibirDados ? date('d/m/Y', strtotime($ra->data)) : '' }}</td>
                                        <td>{{$exibirDados ? $ra->nomep : '' }}</td>
                                        <td>{{ $ra->tpnome }}</td>
                                        <td>{{ number_format($ra->pagvalor, 2, ',', '.') }}</td>
                                    </tr>

                                    @php
                                        $exibirDados = false; // Define para não exibir novamente os dados no mesmo grupo
                                    @endphp

                                @endforeach

                                    <!-- Exibe o total do último ID -->
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td style="background-color:paleturquoise;font-size:12px; text-align:right;"><strong>T Venda:</strong></td>
                                        <td style="background-color:paleturquoise;font-size:12px; text-align:right;"><strong>{{ number_format($totalVlrFinal, 2, ',', '.') }}</strong></td>
                                    </tr>
                                </tbody>
                                @if($rela->currentPage() === $rela->lastPage())
                                <tfoot style='background:#ffffff;'>
                                    <tr>
                                        <td></td>                        
                                        <td></td>
                                        <th></th>
                                        <th style="background-color:yellow;text-align:right;font-size:14px;font-weight: bold;">TOTAL</th>
                                        <td style="background-color:yellow;text-align:center;font-size:14px;font-weight: bold;"><strong>{{ number_format($total_pag, 2, ',', '.') }}</strong></td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                <div class="d-flex justify-content-center">
                {{$rela->withQueryString()->links()}}
            </div>
        </div>   
    </div>
</div>
<script>
function cont(){
var conteudo = document.getElementById('print').innerHTML;
tela_impressao = window.open('about:blank');
tela_impressao.document.write(conteudo);
tela_impressao.window.print();
tela_impressao.window.close();
}
</script>
<script>

$(document).ready(function () {
    // Inicializa o Select2 com múltiplas seleções e sem fechar automaticamente
    $('#lista1').select2({
        placeholder: 'Selecione uma ou mais opções',
        allowClear: true,
        closeOnSelect: false // Impede o fechamento automático ao selecionar
    });

    // Mantém o dropdown aberto após a seleção
    $('#lista1').on('select2:select', function (e) {
        $(this).select2('open'); // Reabre o dropdown após selecionar uma opção
    });

});

</script>
@endsection


@section('footerScript')

@endsection



