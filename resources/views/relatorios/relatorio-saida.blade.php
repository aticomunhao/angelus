@extends('layouts.master')

@section('title') Saídas Material @endsection

@section('headerCss')
@endsection

@section('content')


        <div class="container">
            <div class="row align-items-center">
                <form  class="form-horizontal mt-4" method="GET">
                @csrf
                <div class="row">
                    <div class="col">Início
                        <input type="date" class="form-control" name='data_inicio' value="{{ isset($data_inicio) ? $data_inicio : date('Y-m-d') }}">
                    </div>
                    <div class="col">Final
                        <input type="date" class="form-control" name='data_fim' value="{{$data_fim}}" default="$today = Carbon::today();">
                    </div>
                    <div class="col-2">Categoria
                        <select class="form-control select2" id="lista1" name="categoria[]" placeholder="categoria" onchange="toggleLista('lista1')" multiple="multiple">
                        <option value="">Todos</option>
                        @Foreach($result as $results)
                        <option value="{{$results->id}}" {{ in_array($results->id, request()->get('categoria', [])) ? 'selected' : '' }}>{{ $results->nome }}</option>
                        @endForeach
                        </select>
                    </div>
                    <div class="col-2">Item nome
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
                    <div class="col-auto"><br>
                        <input class="btn btn-light" formaction="/relatorio-saidas" type="submit" value="Pesquisar" style="box-shadow: 1px 2px 5px #000000;">
                                     
                        <a href="/relatorio-saidas"><input class="btn btn-light" type="button" value="Limpar" style="box-shadow: 1px 2px 5px #000000;"></a>
   
                        <input class="btn btn-info" formaction="{{route('sai.pdf')}}" type="submit" value="Gerar PDF" style="box-shadow: 1px 2px 5px #000000;">
                    </div>
                    </form>
                </div>                
            </div>       
            <br>
            <div class="row align-items-center" id="1">
            <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">SAÍDAS DE MATERIAL NO PERÍODO DE {{date( 'd/m/Y' , strtotime($data_inicio))}} até {{ $data_fim ? date('d/m/Y', strtotime($data_fim)) : '-' }}</h4>
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
                        <td style="text-align:center;">DATA SAÍDA</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($saidamat as $saidamats )
                        <tr style="text-align:center;">
                            <td>{{$loop->iteration}}</td>
                            <td style="text-align:center;">{{$saidamats->nomecat}}</td>
                            <td style="text-align:center;">{{$saidamats->nomemat}}</td>
                            <td style="text-align:center;">{{$saidamats->ref_fabricante}}</td>
                            <td style="text-align:center;">
                            @if($saidamats->adquirido == true)
                                Sim
                            @else
                                Não
                            @endif
                            </td>
                            <td>{{$saidamats->qtdsaida}}</td>
                            <td>{{number_format($saidamats->valor_venda,2,',','.')}}</td>
                            <td style="text-align:center;">{{ date( 'd/m/Y' , strtotime($saidamats->data))}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                            <tr style="text-align:center; font-weight: bold; font-size:15px; background-color:yellow">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>Soma total de saídas</td>
                            <td>{{number_format($somaqtd,0,'','.')}}</td>
                            <td>{{number_format($somasai,2,',','.')}}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
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
//     function toggleDisable(selectedId, otherId) {
//         const selectedValues = $(`#${selectedId}`).val();
//         if (selectedValues && selectedValues.length > 0) {
//             $(`#${otherId}`).prop('disabled', true).select2();
//         } else {
//             $(`#${otherId}`).prop('disabled', false).select2();
//         }
//     }

//     // Eventos de mudança
//     $('#lista1').on('change', function () {
//         toggleDisable('lista1', 'lista2');
//     });

//     $('#lista2').on('change', function () {
//         toggleDisable('lista2', 'lista1');
//     });
 });
</script>


@endsection

@section('footerScript')

@endsection



