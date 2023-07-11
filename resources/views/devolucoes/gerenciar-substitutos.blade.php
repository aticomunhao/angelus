

@extends('layouts.master')

@section('title') Gerenciar substitutos @endsection

@section('headerCss')
    <link href="{{ URL::asset('/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <h6 style="color: blue;">VINCULAR OS ITENS SUBSTITUTOS</h6>
                        <div class="row align-items-center">
                        @foreach($result as $results)    
                            <div class="col-2">ID DEVOLUÇÃO
                                <input class="form-control" style="font-weight:bold; background:#f3f3f3; color:#000;" type="text" name="id_dev" id="id_dev" value="{{$results->id}}" readonly>
                            </div>
                            <div class="col-2">DATA DEVOLUÇÃO
                                <input class="form-control" style="font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);" value="{{date( 'd/m/Y' , strtotime ($results->data))}}" type="text" name="data_dev" id="data_dev" readonly>
                            </div>
                            <div class="col">NOME CLIENTE
                                <input class="form-control" style="font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);" value="{{$results->id_pessoa}}" name="cliente" id="cliente" type="text" readonly>
                            </div>
                            @endforeach
                        </div>
                        <br>
                        <div class="row align-items-center">
                        @foreach($item as $items)
                            <div class="col-2">ID ITEM DEVOLVIDO
                                <input class="form-control" style="font-weight:bold; background:#f3f3f3; color:#000;" type="text" name="id_item" id="id_item" value="{{$items->imat}}">
                            </div>
                            <div class="col-7">ITEM DEVOLVIDO
                                <input class="form-control" style="font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);" value="{{$items->nome_dev}}" type="text" name="nome_dev" id="nome_dev"  readonly>
                            </div>
                            <div class="col">VALOR ITEM DEVOLVIDO
                                <input class="form-control" style="font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);" value="{{number_format($items->valor_venda,2,',','.')}}" type="text" name="valor" id="valor" readonly>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <hr>
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-6">
                                <button onclick="btnbuscaitem" id="btnbuscaitem" type="button" class="btn btn-dark">Buscar item catálogo</button>
                                <button onclick="btncodigobarra"  id="btncodigobarra" type="button" class="btn btn-info">Buscar item Cód barras</button>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="col" id="DivConfirmaItem">
                        <div class="container">
                            <div class="row">
                                <table class="table-sm col-12 table-bordered">
                                    <thead class="thead-light">
                                    <h6 style="color: blue;">ITENS SUBSTITUTOS</h6>
                                    <br>
                                        <tr style="background: #f3f3f3;">
                                            <th scope="col" class="col-2">ID ITEM</th>
                                            <th scope="col" class="col-6">ITENS SUBSTITUTOS</th>
                                            <th scope="col" class="col-1">QTD</th>
                                            <th scope="col" class="col-2">VALOR</th>
                                            <th scope="col" class="col-1">AÇÕES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $tot = floatval("0"); $qtde = 0 ?>
                                        @foreach($substituto as $substitutos)
                                        <tr>
                                            <td scope="row">{{$substitutos->id}}</td>
                                            <td>{{$substitutos->nome}}</td>
                                            <!-- <td>0</td> -->
                                            <td>{{$substitutos->qtd}}</td>
                                            <?php $tot += floatval($substitutos->valor_venda); $qtde++; ?>
                                            <td>R$ {{number_format($substitutos->valor_venda,2,'.','.')}}</td>
                                            <td>
                                                <button type="button" value="{{$substitutos->id}}"  class="btn btn-danger btn-custom btnRemoveItem"><i class="far fa-trash-alt"></i></button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfooter>
                                        <td colspan="2">TOTAL:</td>
                                        <td>{{$qtde}}</td>
                                        <td>R$ <span id="vlrTotalVenda">{{number_format($tot,2,'.','.')}}</span></td>
                                        <td></td>
                                    </tfooter>
                                </table>                                                        
                            </div>
                        </div>  
                    </div>
                    <br/>
                    <div class="container" id="3">
                        <div class="row">
                            <div class="col-12 mt-3" style="text-align: right;">
                                <a href="/gerenciar-devolucoes" id="5" type="button" class="btn btn-danger">Cancelar venda</button>
                                <!-- <button type="submit" class="btn btn-success">Salvar</button> -->
                                <a href="/gerenciar-devolucoes" id="4" type="button" class="btn btn-success">Salvar e concluir</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('devolucoes/popUp-buscaritem')
      <!--
    **********************************************************************************************************************************
    * MODAL
    **********************************************************************************************************************************
    -->
    <div class="modal fade" id="divModal" data-backdrop="static" >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">&nbsp;</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img src="/images/loading02.gif" width="50px"><span>&nbsp;Carregando...</span>
                </div>
                <div class="modal-footer">
                    &nbsp;
                </div>
            </div>
        </div>
    </div>



@endsection

@section('footerScript')

            <script src="{{ URL::asset('/libs/select2/select2.min.js')}}"></script>
            <script src="{{ URL::asset('/js/pages/form-advanced.init.js')}}"></script>
            <!-- Required datatable js -->
            <script src="{{ URL::asset('/libs/datatables/datatables.min.js')}}"></script>
            <script src="{{ URL::asset('/libs/jszip/jszip.min.js')}}"></script>
            <script src="{{ URL::asset('/libs/pdfmake/pdfmake.min.js')}}"></script>

            <!-- Datatable init js -->
            <script src="{{ URL::asset('/js/pages/datatables.init.js')}}"></script>
            <script src="{{ URL::asset('/js/pages/registrar-devolucao.init.js')}}"></script>

            <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
            <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection
