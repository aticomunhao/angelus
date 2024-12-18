@extends('layouts.master')

@section('title') Incluir Desconto @endsection

@section('headerCss')
    <link href="{{ URL::asset('/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div container>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title" style="color: rgb(255, 0, 0)">Cadastrar Devolução</h4>
                    <hr>        
                    <form class="form-horizontal mt-4" action="/criar-devolucao" method="any" >
                    @csrf
                    <div class="form-group row">
                        <div class="col-5">Cliente
                            <select class="form-control select2" id="cliente" name="cliente">
                                <option value="">Selecione</option>
                                @foreach($form as $forms)
                                <option value="{{$forms->id_p}}">{{$forms->nome_p}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2" style="text-align:center;">Data Venda
                            <input type="date"  class="form-control" name='data_venda'style="height:65%;" value="">
                        </div>    
                        <div class="col">Código Venda
                            <select class="form-control select2" type="numeric" id="id_venda" name="id_venda">
                                <option value="">Selecione</option> 
                            @foreach ($form as $forms)
                                <option value="{{$forms->id_venda}}">{{$forms->id_venda}}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="col" style="text-align: right;"><br>
                            <a href="/criar-devolucao">
                                    <input class="btn btn-danger" type="button" value="Limpar">
                            </a>
                            <button type="submit" class="btn btn-info">Pesquisar</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <h4 class="card-title" style="color:red;">Lista de dados de Vendas</h4>
    <div class="row">
        <div class="col-12">
            <div class="card">
                
                    <table id="datatable" class="table-resposive-sm table-bordered table-striped table-hover">
                        <thead>
                        <tr style="text-align:center; background-color:#c6e6ce;">
                            <th class="col-3">CLIENTE</th>
                            <th class="col-1">NR VENDA</th>
                            <th class="col-1">DATA VENDA</th>
                            <th class="col-3">ITEM A DEVOLVER</th>
                            <th class="col-1">AÇÕES</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($form as $forms)
                        <tr style="text-align:center;">
                            <td>{{$forms->nome_p}}</td>
                            <td>{{$forms->id_venda}}</td>                                        
                            <td>{{date( 'd/m/Y' , strtotime($forms->data))}}</td>
                            <td>{{$forms->nome_i}}</td>
                            <td>                          
                            <a href="/incluir-devolucao/{{$forms->id_p}}/{{$forms->id_venda}}/{{date( 'Y-m-d' , strtotime($forms->data))}}/{{$forms->id_mat}}">
                                <input class="btn btn-outline-danger btn-sm" type="button" style="font-size:11px;" value="Devolver">
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

<script>
    $(document).ready(function() {
    $('#cliente, #id_venda').select2({
        placeholder: 'Selecione uma Categoria',
        allowClear: true
    });

    // Ajustar a altura do campo
    $('#cliente, #id_venda').next('.select2-container').find('.select2-selection--single').css({
        height: '33px', // Altura desejada
        display: 'flex',
        'align-items': 'center', // Alinha o texto verticalmente
        'font-size': '12px' // Ajuste do tamanho da fonte
    });
});

</script>

@endsection

@section('footerScript')


@endsection

