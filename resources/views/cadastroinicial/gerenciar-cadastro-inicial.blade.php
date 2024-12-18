@extends('layouts.master')

@section('title') Gerenciar Cadastro inicial @endsection


@section('headerCss')


@endsection


@section('content')


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">               
                    <form  class="form-horizontal mt-4" method="GET" >     
                    <div class="row">                        
                        <div class="col-sm">Início:
                            <input type="date" class="form-control" name="data_inicio" value="{{$data_inicio}}">
                        </div>
                        <div class="col-sm">Final:
                            <input type="date" class="form-control" name="data_fim" value="{{$data_fim}}">
                        </div>
                        <div class="col-sm">Nome material:
                            <input class="form-control" type="text" name="material" value="{{$material}}">
                        </div>
                        <div class="col-sm">Observação:
                            <input class="form-control" type="text" name="obs" value="{{$obs}}">
                        </div>
                        <div class="col-sm">Ref Fab:
                            <input class="form-control" type="text" name="ref_fab" value="{{$ref_fab}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm">ID inicial:
                            <input class="form-control" type="numeric" name="identidade1" value="{{$identidade1}}">
                        </div>
                        <div class="col-sm">ID final:
                            <input class="form-control" type="numeric" name="identidade2" value="{{$identidade2}}">
                        </div>
                        <div class="col-sm">Comprado?<br>
                            <select class="form-control" id="compra" name="compra">
                                <option value="">Todos</option>
                                <option value="true" {{ $compra === 'true' ? 'selected' : '' }}>Sim</option>
                                <option value="false" {{ $compra === 'false' ? 'selected' : '' }}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm">Categoria:
                            <select class="form-control custom-select2" id="categoria" name="categoria">
                            <option value="">Todos</option>
                            @Foreach($resultCat as $resultCats)
                                <option value="{{$resultCats->id}}" {{$resultCats->id == $categoria ? 'selected': ''}}>{{$resultCats->nome}}</option>
                            @endForeach
                            </select>
                        </div>
                    </div>
                    <div class="row" style="text-align: right;">
                        <div class="col">
                            <input class="btn btn-light" type="submit" formaction="{{route('cadastroinicial')}}" value="Pesquisar" style="box-shadow: 1px 2px 5px #000000; margin:5px;">
                            <input class="btn btn-light" type="submit" formaction="{{route('codbarras')}}" value="Cód Barras Filtrados" style="box-shadow: 1px 2px 5px #000000; margin:5px;">
                            <a href="/gerenciar-cadastro-inicial"><input class="btn btn-light" type="button" value="Limpar" style="box-shadow: 1px 2px 5px #000000; margin:5px;"></a>                            
                        </form>
                            <a href="/gerenciar-cadastro-inicial/incluir"><input class="btn btn-success" style="font-weight:bold; font-size:15px; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Novo Cadastro +"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR CADASTROS INICIAIS</h4>
                    <div class="card">
                        <div class="card-body">Quantidade filtrada: {{$contar}}
                            <table id="datatable" class="display table-resposive-lg table-bordered table-striped table-hover" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr style="font-size:15px; text-align:center; background-color:#c6e6ce">
                                        <th>CÓDIGO</th>
                                        <th>CATEGORIA</th>
                                        <th>NOME</th>
                                        <th>OBS</th>
                                        <th>REF FABRICA</th>
                                        <th>DATA</th>
                                        <th>MARCA</th>
                                        <th>TAMANHO</th>
                                        <th>COR</th>
                                        <th>VALOR</th>
                                        <th>AÇÕES</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($result as $results)
                                    <tr>
                                        <td>{{$results->id}}</td>
                                        <td>{{$results->nome_cat}}</td>
                                        <td>{{$results->n1}}</td>
                                        <td>{{$results->obs}}</td>
                                        <td>{{$results->ref_fab}}</td>
                                        <td>{{date( 'd/m/Y' , strtotime($results->data_cadastro))}}</td>
                                        <td>{{$results->n2}}</td>
                                        <td>{{$results->n3}}</td>
                                        <td>{{$results->n4}}</td>
                                        <td>{{number_format($results->valor_venda,2,',','.')}}</td>
                                        <td>
                                            @if ($results->id_tipo_situacao == 2)
                                            <a href="/editar-cadastro-inicial/{{$results->id}}/{{$results->id_cat}}"><input class="btn btn-warning btn-sm" type="button" value="Alterar" disabled="true"></a>
                                            <a href="/gerenciar-cadastro-inicial/excluir/{{$results->id}}"><input class="btn btn-danger btn-sm" type="button" value="Excluir" disabled="true"></a>
                                            <a href="/item_material/{{$results->id}}"><input class="btn btn-info btn-sm" type="button" value="Cód Barras" disabled="true"></a>
                                            @else
                                            <a href="/editar-cadastro-inicial/{{$results->id}}/{{$results->id_cat}}"><input class="btn btn-warning btn-sm" type="button" value="Alterar"></a>
                                            <a href="/gerenciar-cadastro-inicial/excluir/{{$results->id}}"><input class="btn btn-danger btn-sm" type="button" value="Excluir"></a>
                                            <a href="/item_material/{{$results->id}}"><input class="btn btn-info btn-sm" type="button" value="Cód Barras"></a>
                                            @endif

                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center">
                            {{$result->withQueryString()->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
$(document).ready(function() {
    $('#categoria').select2({
        placeholder: 'Selecione uma Categoria',
        allowClear: true
    });

    // Ajustar a altura do campo
    $('#categoria').next('.select2-container').find('.select2-selection--single').css({
        height: '33px', // Altura desejada
        display: 'flex',
        'align-items': 'center', // Alinha o texto verticalmente
        'font-size': '12px' // Ajuste do tamanho da fonte
    });
});

</script>

<!--<script>
    $(document).ready( function () {
        $('#datatable').DataTable({
           
        });
    } );
</script>-->

@endsection

@section('footerScript')
 <!-- Required datatable js -->

            <script src="{{ URL::asset('/libs/jszip/jszip.min.js')}}"></script>
            <script src="{{ URL::asset('/libs/pdfmake/pdfmake.min.js')}}"></script>
            

@endsection

