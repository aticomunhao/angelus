@extends('layouts.master')

@section('title') Form Elements @endsection

@section('headerCss')
    
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                                <hr>                
                <form class="form-horizontal mt-4" method="POST" action="/registrar-venda-pessoa"> 
                @csrf
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label for="" class="col-sm-4 col-form-label">Pessoa</label>
                            <select class="form-control select2" id="pessoa" name="pessoa" required="required">
                                <option value="">Selecione</option>
                                @foreach($pessoa as $pessoas)
                                <option value="{{$pessoas->id}}">{{$pessoas->id}} / {{$pessoas->nome}} / {{$pessoas->cpf}}</option>
                                @endforeach
                            </select>                           
                </form>     
                <hr> 
            </div>     
        </div>
    </div>
    <!-- end col -->
</div>
@endsection

@section('footerScript')

            <script src="{{ URL::asset('/js/pages/cadastro-inicial.init.js')}}"></script>
@endsection

