@extends('layouts.master')

@section('title') Gerenciar Cadastro inicial @endsection

@section('content')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <form action="{{route('cadastroinicial.index')}}" class="form-horizontal mt-4" method="GET" >
                    <div class="row">
                        <div class="col">Início
                            <input type="date" name="data_inicio" value="{{$data_inicio}}">
                        </div>
                        <div class="col">Final
                            <input type="date" name="data_fim" value="{{$data_fim}}">
                        </div>
                        <div class="col-4">Nome do material
                            <input class="form-control" type="text" name="material" value="{{$material}}">
                        </div>
                        <div class="col">Ref fabrica
                            <input class="form-control" type="numeric" name="ref_fab" value="">
                        </div>
                        <div class="col">Comprado?<br>
                            <select class="form-control" id="" name="compra">
                                <option value="">Todos</option>
                                <option value="true">Sim</option>
                                <option value="false">Não</option>
                            </select>
                        </div>
                        <div class="col-2">Categoria:
                            <select class="form-control" id="" name="categoria" ><option value="">Todos</option>
                            @Foreach($resultCat as $resultCats)
                                <option value="{{$resultCats->id}}" {{$resultCats->id == $categoria ? 'selected': ''}}>{{$resultCats->nome}}</option>
                            @endForeach
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row" style="text-align: right;">
                        <div class="col">
                            <input class="btn btn-secondary" type="submit" value="Pesquisar">
                            <a href="/gerenciar-cadastro-inicial"><input class="btn btn-secondary" type="button" value="Limpar"></a>
                        </form>
                            <a href="/barcode"><input class="btn btn-info" type="button" value="Imprimir Cód Barras"></a>
                            <a href="/gerenciar-cadastro-inicial/incluir"><input class="btn btn-success" style="font-weight:bold; font-size:15px;" type="button" value="Novo Cadastro +"></a>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <h4 class="card-title" style="color:red; text-align: left;">Listar o estoque</h4>
                    <div class="card">
                        <div class="card-body">
                            <table id="datatable" class="table-resposive-sm table-bordered table-striped table-hover" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead >
                                    <tr style="text-align:center; background-color:#c6e6ce">
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
                            {{$result->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footerScript')
 <!-- Required datatable js -->

            <script src="{{ URL::asset('/libs/jszip/jszip.min.js')}}"></script>
            <script src="{{ URL::asset('/libs/pdfmake/pdfmake.min.js')}}"></script>

            <!-- Datatable init js -->
            <script src="{{ URL::asset('/js/pages/cad-tipo-material.init.js')}}"></script>
            <script src="{{ URL::asset('/js/pages/datatables.init.js')}}"></script>

            @endsection

