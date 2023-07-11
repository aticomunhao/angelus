@extends('layouts.master')

@section('title') Gerenciar Devoluções @endsection

@section('content')

<div container>
    <div clas="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">    
                        <h4 class="card-title" style="color: rgb(255, 0, 0)">Gerenciar Devoluções</h4>
                        <form action="/gerenciar-devolucoes" class="form-horizontal mt-4" method="GET" >
                        @csrf
                    <div class="row align-items-center">
                        <div class="col">Início
                            <input type="date"  style="height:35px;" name="data_inicio" value="{{$data_inicio}}">
                        </div>
                        <div class="col">Fim
                            <input type="date" style="height:35px;" name="data_fim"  value="{{$data_fim}}">
                        </div>
                        <div class="col-5">Nome do cliente:
                            <input class="form-control" type="text" name="cliente" id="cliente" value=""/>
                        </div>
                        <div class="col-2">Código venda:
                            <input class="form-control" type="numeric" name="id_venda" id="id_venda" value=""/>
                        </div>
                        <div class="col-2">Código devolução:
                            <input class="form-control" type="numeric" name="id_dev" id="id_dev" value=""/>
                        </div>
                    </div>
                        <br>
                    <div class="row" style="text-align: right;">
                        <div class="col">
                            <input class="btn btn-info" type="submit" value="Pesquisar">
                            <a href="/gerenciar-devolucoes"><input class="btn btn-danger" type="button" value="Limpar"></a>
                            </form>
                            <a href="/criar-devolucao">
                            <input class="btn btn-success" type="button" value="Incluir Devolução">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
                <hr>
                <h4 class="card-title" style="color: rgb(255, 0, 0)">Lista de Devoluções</h4>
            <div class="row">
                <div class="col-12">            
                    <table id="datatable" class="table-resposive-sm table-bordered table-striped table-hover">
                        <thead>
                        <tr style="text-align:center; background-color:#c6e6ce;">
                            <th class="col-1">CÓDIGO DEVOLUÇÃO</th>
                            <th class="col-1">DATA DEVOLUÇÃO</th>
                            <th class="col-2">CLIENTE</th>
                            <th class="col-2">USUÁRIO</th>
                            <th class="col-1">NR VENDA</th>
                            <th class="col-1">DATA VENDA</th>
                            <th class="col-1">STATUS</th>
                            <th class="col-">AÇÕES</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($result as $results)
                        <tr style="text-align:center;">
                            <td>{{$results->id}}</td>
                            <td>{{date( 'd/m/Y' , strtotime($results->data))}}</td>
                            <td>{{$results->nome_c}}</td>
                            <td>{{$results->nome_u}}</td>
                            <td>{{$results->id_venda}}</td>                                        
                            <td>{{date( 'd/m/Y' , strtotime($results->data_venda))}}</td>
                            <td>{{$results->status}}</td>
                            <td>
                            <a href="/devolucao-alterar/{{$results->id}}" style="">
                                <button class="btn btn-warning btn-md"><i class="fas fa-pencil-alt"></i></button>
                            </a>
                            <a href="/gerenciar-devolucoes/excluir/{{$results->id}}" style="" >
                                <button class="btn btn-danger btn-md"><i class="far fa-trash-alt"></i></button>
                            </a>
                            <a href="/gerenciar-substitutos/{{$results->id}}" style="" >
                                <button class="btn btn-info btn-md"><i class="fas fa-thumbs-up"></i></button>
                            </a>
                            <a href="/devolucao/recibo/{{$results->id}}" style="" >
                                <button class="btn btn-primary btn-md"><i class="fas fa-ticket-alt"></i></button>                                                
                            </a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
        <!-- end row -->
@endsection

@section('footerScript')
            <!-- Required datatable js 
            <script src="{{ URL::asset('/libs/datatables/datatables.min.js')}}"></script>-->
            <script src="{{ URL::asset('/libs/jszip/jszip.min.js')}}"></script>
            <script src="{{ URL::asset('/libs/pdfmake/pdfmake.min.js')}}"></script>

            <!-- Datatable init js -->
            <script src="{{ URL::asset('/js/pages/datatables.init.js')}}"></script>
            <script src="{{ URL::asset('/libs/select2/select2.min.js')}}"></script>
            <script src="{{ URL::asset('/js/pages/form-advanced.init.js')}}"></script>

@endsection

