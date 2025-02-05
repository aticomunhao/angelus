@extends('layouts.master')

@section('title') Form Elements @endsection

@section('headerCss')
    <link href="{{ URL::asset('/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title" style="color: rgb(255, 0, 0)">Alterar Desconto</h4>
                    <hr>
                    <div class="col-3">Código do Desconto
                        <input type="numeric" name='id' value="{{$result[0]->id}}">
                    </div>
                        <form class="form-horizontal mt-4" method="POST" action="/modifica-desconto/{{$result[0]->id}}">
                        @csrf
                        <div class="form-group row">
                            <div class="col">Categoria
                                <select class="form-control select2" id="" name="cat_item" required="required">
                                    <option value="0">Todos</option>
                                    @foreach($categoria as $categorias)
                                    <option value="{{$categorias->id}}">{{$categorias->nome}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">Porcentagem
                                <select class="form-control select2" id="porcentagem" name="porcentagem">
                                @foreach ($percent as $percents)
                                    <option value="{{$percents->valor}}">{{$percents->codigo}}</option>
                                @endforeach
                                </select>
                            </div>
                            <div class="col-3">Data Cadastro - De

                                <input type="date" class="form-control" name='data_inicio' value="" >
                            </div>

                            <div class="col-3">Data Cadastro - Até

                                <input type="date" class="form-control" name='data_fim' value="" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-4">Usuário
                                <input class="form-control" value="{{session()->get('usuario.id_usuario')}}" name="id_usuario" id="id_usuario" type="hidden">
                                <input class="form-control" value="{{session()->get('usuario.nome')}}" name="nome_usuario" id="nome_usuario" type="text" placeholder="Vendedor" readonly>
                            </div>
                            <div class="col-4">Data registro
                                <input class="form-control" name="data_registro" id="" type="text"
                                value="{{ \Carbon\carbon::now()->toDateTimeString() . PHP_EOL}}">
                            </div>
                        </div>
                        <div class="col-12 mt-3" style="text-align: right;">
                            <a href="/gerenciar-desconto"><input class="btn btn-danger btn-md" style="font-weight:bold; margin-left: 15px;" type="button" value="Cancelar"></a>
                            <input class="btn btn-success" type="submit" value="Confirmar">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScript')
       
@endsection

