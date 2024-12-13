@extends('layouts.master')

@section('title') Relatório de vendas por tipo pagamento @endsection

@section('content')


<div class="col-12" style="background:#ffffff;">
    <div class="container">
        <div class="row" style="text-align: left;">
            <div class="col-12" style="background:#ffffff;">
                <form action="/vendas-valor" class="form-horizontal mt-4" method="GET" >
                    @csrf
                <div class="row">
                    <div class="col">Início                        
                        <input type="date" class="form-control" name="data_inicio" value="{{$data_inicio}}">
                    </div>
                    <div class="col">Final                        
                        <input type="date" class="form-control" name="data_fim"  value="{{$data_fim}}">
                    </div>    
                    <div class="col">
                        <input class="btn btn-info" type="submit" value="Pesquisar">
                    </div>
                    <div class="col">
                        <a href="/vendas-valor">    
                        <input class="btn btn-warning" type="button" value="Limpar">
                        </a>
                    </form>                   
                </div>
            </div>
        </div>
    </div>

    <hr>
    <div id='print' class='conteudo'>
    <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">RELATÓRIO DE VENDAS POR TIPO PAGAMENTO</h4>
        <h4 style="font-size: 12px; text-align: center; color:black;">O período do relatório é: {{ \Carbon\Carbon::parse("$data_inicio")->format('d/m/Y')}} até {{ \Carbon\Carbon::parse("$data_fim")->format('d/m/Y')}}</h4>
        <br>
        <div class="container" style="background:#ffffff;">
            <div class="row">                   
                <table class="table table-sm table-striped table-bordered">
                    <thead style='font-size:12px; background:#ffffff; text-align:center;vertical-align:middle'>
                        <tr>
                            <th colspan="1">CÓDIGO</th>
                            <th colspan="1">DATA</th>
                            <th colspan="1">CLIENTE</th>
                            <th colspan="1">TIPO PAGAMENTO</th>
                            <th colspan="1">VALOR TIPO</th>
                        </tr>
                    </thead>                      
                    <tbody style="font-size:10px; text-align:center;vertical-align:middle">
    @php
        $currentIdv = null; // Variável para controlar o ID atual
        $totalVlrFinal = 0; // Variável para somar vlr_final do ID atual
    @endphp

    @foreach ($rela as $ra)
        @if ($currentIdv !== $ra->idv)
            @if ($currentIdv !== null)
                <!-- Exibe o total do ID anterior antes de mudar -->
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>                    
                    <td style="background-color:paleturquoise;font-size:10px; text-align:right;"><strong>T Venda:</strong></td>
                    <td style="background-color:paleturquoise;font-size:10px; text-align:right;"><strong>{{ number_format($totalVlrFinal, 2, ',', '.') }}</strong></td>
                </tr>
            @endif
            @php
                // Atualiza o ID atual e reseta o total
                $currentIdv = $ra->idv;
                $totalVlrFinal = 0;
            @endphp
        @endif

        <!-- Soma o valor ao total do ID atual -->
        @php $totalVlrFinal += $ra->pagvalor; @endphp

        <!-- Exibe os valores de cada registro -->
        <tr>
            <td>{{ $ra->idv }}</td>
            <td>{{ date('d/m/Y', strtotime($ra->data)) }}</td>
            <td>{{ $ra->nomep }}</td>
            <td>{{ $ra->tpnome }}</td>
            <td>{{ number_format($ra->pagvalor, 2, ',', '.') }}</td>
        </tr>
    @endforeach

    <!-- Exibe o total do último ID -->
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td style="background-color:paleturquoise;font-size:10px; text-align:right;"><strong>T Venda:</strong></td>
        <td style="background-color:paleturquoise;font-size:10px; text-align:right;"><strong>{{ number_format($totalVlrFinal, 2, ',', '.') }}</strong></td>
    </tr>
</tbody>

                    <tfoot style='background:#ffffff;'>
                        <tr>
                            <td></td>                        
                            <td></td>
                            <th></th>
                            <th style="background-color:yellow;text-align:right;font-size:12px;font-weight: bold;">TOTAL</th>
                            <td style="background-color:yellow;text-align:center;font-size:12px;font-weight: bold;"><strong>{{ number_format($total_pag, 2, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
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
@endsection


@section('footerScript')

@endsection



