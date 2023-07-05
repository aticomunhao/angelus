@extends('layouts.master')

@section('title') Incluir substitutos @endsection

@section('headerCss')
    <link href="{{ URL::asset('/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')                                
                                
                                <h6 style="font-weight:bold; color: blue;">LISTA DE SUBSTITUTOS</h6>
                                            <table class="table-sm col-12 table-bordered">
                                                <thead class="thead-light" style="background: #f3f3f3;">

                                                    <tr style="text-align: center;">
                                                        <th scope="col" class="col-2">ID</th>
                                                        <th scope="col" class="col-6">PRODUTO</th>
                                                        <th scope="col" class="col-1">QTD</th>
                                                        <th scope="col" class="col-2">VALOR</th>
                                                        <th scope="col" class="col-1">AÇÕES</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $tot = floatval("0"); $qtde = 0 ?>
                                                    @foreach($item as $itens)
                                                    <tr>
                                                        <td>{{$itens->id_item}}</td>
                                                        <td>{{$itens->nome}}</td>
                                                        <td>{{1}}</td>
                                                        <?php $tot += floatval($itens->valor_devolucao); $qtde++; ?>
                                                        <td>{{number_format($itens->valor_venda,2,'.','.')}}</td>
                                                        <td>
                                                            <button type="button" value="{{$itens->id_item_material}}"  class="btn btn-danger btn-custom btnRemoveItem" onclick="setInterval('Atualizar()',1000)"><i class="far fa-trash-alt"></i></button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfooter>
                                                    <td colspan="2">TOTAL:</td>
                                                    <td>{{$qtde}}</td>
                                                    <td><span id="vlrTotalVenda">{{number_format($tot,2,'.','.')}}</span></td>
                                                    <td>&nbsp;</td>
                                                </tfooter>
                                            </table>
                                        </div>
                                </div>
                                <br/>
                                <div class="container">
                                    <div class="row">
                                        <div class="col-12 mt-3" style="text-align: right;">
                                            @foreach ($devolucao as $dv )
                                            <a href="/gerenciar-substitutos" id="" type="button" class="btn btn-danger">Cancelar</a>
                                            <a href="/registrar-substitutos-fimedicao/{{$dv->id}}" id="" type="button" class="btn btn-success" style="color: #000">Salvar e concluir</a>
                                            @endforeach
                                        </div>
                                    </div>
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
            <script src="{{ URL::asset('/js/pages/registrar-venda.init.js')}}"></script>

            <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
            <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection
