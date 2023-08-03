@extends('layouts.master')

@section('title') Editar Cadastro inicial @endsection

@section('headerCss')
    <link href="{{ URL::asset('/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title" style="color:blue">ALTERAR CADASTRO INICIAL</h4>
                <div class="form-group row">
                    <div class="col-2">Código:
                        <input class="form-control text-center" type="text" id="" name="id_item" required="required" value="{{number_format($itemmat[0]->id_item,' 0','','')}}" placeholder="ID"readonly style="background:#f3f3f3; color:rgb(0, 0, 0);font-weight:bold;">
                    </div>
                    <div class="col-6">Nome / Categoria:
                        <input class="form-control text-center" type="text" id="" name="nome" required="required" value="{{($itemmat[0]->nome_item)}} / {{$itemmat[0]->nome_categ}}" placeholder="ID"readonly style="background:#f3f3f3; color:rgb(0, 0, 0);font-weight:bold;">
                    </div>
                    <div class="col-2">Data cadastro:
                        <input class="form-control text-center" type="text" id="" name="data_cadastro" required="required" value="{{date( 'd/m/Y' , strtotime($itemmat[0]->data_cadastro))}}" placeholder="nome" readonly style="background:#f3f3f3; color:rgb(0, 0, 0);font-weight:bold;">
                    </div>
                    <div class="col-2">Valor venda:
                        <input class="form-control text-center" type="text" id="" name="valor_venda" required="required" value="{{number_format($itemmat[0]->valor_venda,' 2',',','')}}" placeholder="ID"readonly style="background:#f3f3f3; color:rgb(0, 0, 0);font-weight:bold;">
                    </div>
                </div>
            </div>
        </div>

    <div class="col-12">
        <form class="form-horizontal mt-4" method="POST" action="/gerenciar-cadastro-inicial/alterar/{{$itemlista[0]->id_item}}">
        @method('PUT')
        <div class="card">
            <div class="card-body">
                @csrf
                <div class="row align-items-end" style="background:#fff;">
                    <div class="col">
                        <label for="nome_item" class="col-sm-5 content-md-center col-form-label">Nome/Categoria:</label>
                        <select class="form-control select2" id="" name="item_cat" default-value="teste"  required="required">
                            <option value="{{($itemmat[0]->id_item_cat)}}">{{($itemmat[0]->nome_item)}} / {{($itemmat[0]->nome_categ)}}</option>
                            @foreach($lista as $listas)                            
                            <option value="{{$listas->id_item_cat}}">{{$listas->nome_item}}/{{$listas->nome_categ}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">Observação:
                            <input class="form-control" type="text" id="" name="obs" value="{{($itemlista[0]->obs)}}">
                    </div>
                </div>
                <br>
                <div class="row align-items-end" style="background:#fff;">
                    <div class="col-2">
                        <div class="form-group row">
                            <div class="col">Valor da venda:
                            <input class="form-control" type="numeric" id="" name="valor" value="{{number_format($itemlista[0]->valor_venda,'2','.','')}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group row">
                            <div class="col">Ref fabricante:
                            <input class="form-control" type="text" id="" name="ref_fab" value="{{number_format($itemlista[0]->ref_fab,'0','.','')}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group row">
                            <div class="col">Tamanho:
                                <select class="form-control" id="" name="tamanho" >
                                    <option value="{{($itemlista[0]->id_tam)}}">{{$itemlista[0]->n3}}</option>
                                    @foreach($tamanho as $tamanhos)
                                    <option value="{{$tamanhos->id_tam}}">{{$tamanhos->n3}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group row">
                            <div class="col">Marca:
                                <select class="form-control" id="" name="marca" >
                                    <option value="{{($itemlista[0]->id_marca)}}">{{$itemlista[0]->n2}}</option>
                                    @foreach($marca as $marcas)
                                    <option value="{{$marcas->id_marca}}">{{$marcas->n2}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group row">
                            <div class="col">Cor:
                                <select class="form-control" id="" name="cor">
                                    <option value="{{($itemlista[0]->id_cor)}}">{{$itemlista[0]->n4}}</option>
                                    @foreach($cor as $cors)
                                    <option value="{{$cors->id_cor}}">{{$cors->n4}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group row">
                            <div class="col">Tipo material:
                                <select class="form-control" id="" name="tp_mat" >
                                    <option value="{{($itemlista[0]->tp_mat)}}">{{$itemlista[0]->n7}}</option>
                                    @foreach($tipo as $tipos)
                                    <option value="{{$tipos->tp_id}}">{{$tipos->n8}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row align-items-end" style="background:#fff;">
                    <div class="col">
                        <div class="form-group row">
                            <div class="col">Sexo:
                                <select class="form-control" id="" name="sexo" >
                                    <option value="{{($itemlista[0]->id_sexo)}}">{{$itemlista[0]->n5}}</option>
                                   @foreach($sexo as $sexos)
                                    <option value="{{$sexos->sexid}}">{{$sexos->n7}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group row">
                            <div class="col">Fase etária:
                                <select class="form-control" id="" name="etaria" >
                                    <option value="{{($itemlista[0]->fase_e)}}">{{$itemlista[0]->n6}}</option>
                                   @foreach($etaria as $etarias)
                                    <option value="{{$etarias->id}}">{{$etarias->nome}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group row">
                            <div class="col">Data validade:
                            <input class="form-control" type="date" id="" name="dt_validade" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group row">Comprado?
                            <div class="col">
                                <input type="checkbox" id="checkAdq" name="checkAdq" switch="bool"/>
                                <label for="checkAdq" data-off-label="Não" data-on-label="Sim" ></label>
                            </div>
                         </div>
                    </div>
                </div>
                <div class="row" style="background:#fff;">
                    <div class="col">
                            <a href="/gerenciar-cadastro-inicial"><input class="btn btn-danger btn-md btn-block" style="font-weight:bold; margin-left: 15px;" type="button" value="Cancelar"></a>
                    </div>
                    <div class="col">
                        <div class="form-group row">
                            <input class="btn btn-info btn-md btn-block" style="font-weight:bold; margin-left: 15px;" type="submit" value="Confirmar">
                        </div>
                    </div>
                </div>
            </div>
        </div></form>
    </div>

@endsection

@section('footerScript')
            <script src="{{ URL::asset('/js/pages/mascaras.init.js')}}"></script>
            <script src="{{ URL::asset('/js/pages/busca-cep.init.js')}}"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
            <script src="{{ URL::asset('/libs/select2/select2.min.js')}}"></script>
            <script src="{{ URL::asset('/js/pages/form-advanced.init.js')}}"></script>
@endsection

