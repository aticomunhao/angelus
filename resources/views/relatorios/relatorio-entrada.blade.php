@extends('layouts.master')

@section('title') Entradas Material @endsection


@section('headerCss')

@endsection

@section('content')

<div class="col-12" style="background:#ffffff;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12">
            <form action="/relatorio-entrada" class="form-horizontal mt-4" method="GET">
            @csrf
                <div class="row">
                    <div class="col">Início
                        <input type="date" class="form-control" name='data_inicio'  value="{{ isset($data_inicio) ? $data_inicio : date('Y-m-d') }}">
                    </div>
                    <div class="col">Fim
                        <input type="date" class="form-control" name='data_fim' value="{{$data_fim}}" default="$today = Carbon::today();">
                    </div>
                    <div class="col">Categoria
                        <select class="form-control select2" id="lista1" name="categoria[]" placeholder="categoria" onchange="toggleLista('lista1')" multiple="multiple">
                        <option value="">Todos</option>
                        @Foreach($result as $results)
                        <option value="{{$results->id}}" {{ in_array($results->id, request()->get('categoria', [])) ? 'selected' : '' }}>{{ $results->nome }}</option>
                        @endForeach
                        </select>
                    </div>
                    <div class="col">Item nome
                        <select class="form-control select2" id="lista2" name="nomeitem[]" placeholder="nomeitem" onchange="toggleLista('lista2')" multiple="multiple">
                        <option value=" ">Todos</option>
                        @Foreach($itemmaterial as $itemmat)
                        <option value="{{$itemmat->id}}" {{ in_array($itemmat->id, request()->get('nomeitem', [])) ? 'selected' : '' }}>{{ $itemmat->nome }}</option>
                        @endForeach
                        </select>
                    </div>
                    <div class="col">Comprado?<br>
                        <select class="form-control" id="compra" name="compra">
                            <option value="null">Todos</option>
                            <option value="true" {{ $compra === 'true' ? 'selected' : '' }}>Sim</option>
                            <option value="false" {{ $compra === 'false' ? 'selected' : '' }}>Não</option>
                        </select>
                    </div>     
                    <div class="col-3">
                            <input class="btn btn-light" type="submit" value="Pesquisar" style="box-shadow: 1px 2px 5px #000000; margin-top:20px;">
                   
                        <a href="/relatorio-entrada"><input class="btn btn-light" type="button" value="Limpar" style="box-shadow: 1px 2px 5px #000000;margin-top:20px;"></a>
                  
                        <!--<a href=""><input class="btn btn-info" onclick="cont();" type="button" value="Imprimir" style="margin-top:20px;"></a>-->
                    </div>
                </div>
            </form>
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
<br>
<div id='print' class='conteudo'>
<div class="container" style="background:#ffffff;">
<h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">ENTRADAS DE MATERIAL POR PERÍODO</h4>
    <div class="row align-items-center">
        <table class="table table-sm table-striped">
            <thead style="text-align:center;">
                <tr style="text-align:center; font-weight: bold; font-size:15px; background: #daffe0;">
                <td>NR</td>
                <td>CATEGORIA</td>
                <td>NOME</td>
                <td>REF FABR</td>   
                <td>COMPRADO?</td>
                <td>QUANTIDADE</td>
                <td>VALOR</td>
                <td style="text-align:center;">DATA ENTRADA</td>
                </tr>
            </thead>
            @php
                $nr_ordem = $entmat->firstItem(); // Define o contador inicial
            @endphp
            <tbody>
                @foreach ($entmat as $entmats )
                <tr style="text-align:center;">
                    <td>{{$nr_ordem++}}</td>
                    <td style="text-align:center;">{{$entmats->nomecat}}</td>
                    <td style="text-align:center;">{{$entmats->nome}}</td> 
                    <td style="text-align:center;">{{$entmats->ref_fabricante}}</td>                    
                    <td >@if($entmats->adquirido == 0)
                        Não
                        @else
                        Sim
                        @endif
                    </td>
                    <td style="text-align:center;">{{$entmats->total}}</td>
                    <td>{{number_format($entmats->valor_venda,2,',','.')}}</td>
                    <td style="text-align:center;">{{ date( 'd/m/Y' , strtotime($entmats->data_cadastro))}}</td>
                    </tr>
                @endforeach
            </tbody>
            @if($entmat->currentPage() === $entmat->lastPage())
            <tfoot>
                    <tr style="text-align:center; font-weight: bold; font-size:15px; background-color:yellow">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Soma total de entradas</td>
                    <td>{{number_format($somait,0,'','.')}}</td>
                    <td>{{number_format($somaent,2,',','.')}}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
        <div class="d-flex justify-content-center">
        {{$entmat->withQueryString()->links()}}
        </div>
    </div>
</div>
<script>

$(document).ready(function () {
    // Inicializa o Select2 com múltiplas seleções e sem fechar automaticamente
    $('#lista1, #lista2').select2({
        placeholder: 'Selecione uma ou mais opções',
        allowClear: true,
        closeOnSelect: false // Impede o fechamento automático ao selecionar
    });

    // Mantém o dropdown aberto após a seleção
    $('#lista1, #lista2').on('select2:select', function (e) {
        $(this).select2('open'); // Reabre o dropdown após selecionar uma opção
    });

    // Função para ativar/desativar selects
    function toggleDisable(selectedId, otherId) {
        const selectedValues = $(`#${selectedId}`).val();
        if (selectedValues && selectedValues.length > 0) {
            $(`#${otherId}`).prop('disabled', true).select2();
        } else {
            $(`#${otherId}`).prop('disabled', false).select2();
        }
    }

    // Eventos de mudança
    $('#lista1').on('change', function () {
        toggleDisable('lista1', 'lista2');
    });

    $('#lista2').on('change', function () {
        toggleDisable('lista2', 'lista1');
    });
});

</script>

@endsection

@section('footerScript')

@endsection



