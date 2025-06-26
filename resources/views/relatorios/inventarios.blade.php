@extends('layouts.master')

@section('title')Inventário @endsection

@section('headerCss')

@endsection

@section('content')



<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">               
                    <form action="/inventarios" class="form-horizontal mt-4" method="GET">
                    @csrf  
                    <div class="row"> 
                        <div class="col-2">Data
                            <input type="date" class="form-control" name='data' value="{{ isset($data) ? $data : date('Y-m-d') }}">
                        </div>
                        <div class="col-sm">Comprado?<br>
                            <select class="form-control" id="compra" name="compra">
                                <option value="null">Todos</option>
                                <option value="true" {{ $compra === 'true' ? 'selected' : '' }}>Sim</option>
                                <option value="false" {{ $compra === 'false' ? 'selected' : '' }}>Não</option>
                            </select>
                        </div>
                        <div class="col">Categoria
                            <select class="form-control select2" id="lista1" name="categoria[]" multiple>
                                <option value="">Todos</option>
                                    @Foreach($resultCategorias as $results)
                                    <option value="{{$results->id}}" {{ in_array($results->id, request()->get('categoria', [])) ? 'selected' : '' }}>{{ $results->nome }}</option>
                                    @endForeach
                            </select>
                        </div>
                        <div class="col">Item nome
                            <select class="form-control select2" id="lista2" name="item[]" multiple>
                                <option value="">Todos</option>
                                @Foreach($itemmaterial as $itemmat)
                                <option value="{{$itemmat->id}}" {{ in_array($itemmat->id, request()->get('item', [])) ? 'selected' : '' }}>{{ $itemmat->nome }}
                                </option>
                                @endForeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exclui_vendido" class="d-block">Exclui vendido</label>
                            <div class="custom-control custom-switch" style="text-align: center;">
                                <input type="checkbox" class="custom-control-input" id="exclui_vendido" name="exclui_vendido" value="1" {{ request('exclui_vendido') == 1 ? 'checked' : '' }}>
                                <label class="custom-control-label" for="exclui_vendido"></label>
                            </div>
                        </div>
                        <div class="col">
                            <input class="btn btn-light" type="submit" value="Pesquisar" style="box-shadow: 1px 2px 5px #000000;margin-top:20px;">
                        
                            <a href="/inventarios"><input class="btn btn-light" type="button" value="Limpar" style="box-shadow: 1px 2px 5px #000000;margin-top:20px;"></a>
                            <input class="btn btn-info" formaction="{{route('inv.pdf')}}" type="submit" acti value="Gerar PDF" style="box-shadow: 1px 2px 5px #000000;margin-top:20px;">
                        
                        </div>                        
                    </div>
                    </form>    
                    <hr>
                    <div class="container-fluid" style="background:#ffffff;" id="1">
                    <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">INVENTÁRIO DE ESTOQUE</h4>
                        <div class="row">
                        <h6 class="font-weight-bold" style="color: blue;  margin-left: 10px;">INVENTÁRIO DE ESTOQUE - no dia <span class="badge badge-secondary">{{ \Carbon\Carbon::parse($data)->format('d/m/Y')}}</span> </h6>
                            <table class="table table-sm table-striped">
                                <thead style="text-align:center; background: #daffe0;">
                                    <tr style="text-align:center; font-weight: bold; font-size:15px">
                                    <td>NR</td>
                                    <td>CATEGORIA</td>
                                    <td>CÓDIGO</td>
                                    <td>NOME</td>
                                    <td>COMPRA?</td>
                                    <td>ENTRADA</td>
                                    <td>VENDA</td>
                                    <td>VLR UNID</td>
                                    <td>QTD</td>
                                    <td>SUBTOTAL</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($resultItens as $index => $rit)
                                    <tr style="text-align:center;">
                                        <td>{{($resultItens->currentPage() - 1) * $resultItens->perPage() + $index + 1}}</td>
                                        <td style="text-align:center;">{{$rit->ncat}}</td>
                                        <td style="text-align:center;">{{$rit->idmat}}</td>
                                        <td style="text-align:center;">{{$rit->nome}}</td>
                                        <td style="text-align:center;">
                                            @if($rit->adquirido == true)
                                                Sim
                                            @else
                                                Não
                                            @endif
                                        </td>
                                        <td>{{date( 'd-m-Y', strtotime($rit->data_cadastro))}}</td>
                                        <td>{{ $rit->data ? date('d-m-Y', strtotime($rit->data)): '-' }}</td>
                                        <td>{{number_format($rit->valor_venda,2,',','.')}}</td>
                                        <td>{{$rit->qtd}}</td>
                                        <td>{{number_format($rit->qtd * $rit->valor_venda,2,',','.')}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                                @php
                                    $isPdf = request()->has('pdf'); // ou outro indicador que tu queiras
                                @endphp
                               @if($isPdf || (method_exists($resultItens, 'currentPage') && $resultItens->currentPage() === $resultItens->lastPage()))
                                <tfoot style="background: #daffe0;">
                                        <tr style="text-align:center; font-weight: bold; font-size:15px">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>TOTAIS</td>
                                        <td>{{number_format($total_itens, 0, '', '.')}}</td>
                                        <td>{{number_format($total_soma,2,',','.')}}</td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                            <div class="d-flex justify-content-center">
                            {{$resultItens->withQueryString()->links()}}
                            </div>
                            <h6 class="col-12  font-weight-bold" style="color: blue; margin-left: 10px; text-align:right;">O relatório foi impresso em <span class="badge badge-secondary">{{ \Carbon\Carbon::today()->locale('pt')->isoFormat('DD MMMM YYYY')}}</span> </h6>
                        </div id="fim">
                    </div>
                </div>
            </div>
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


