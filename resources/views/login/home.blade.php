
@extends('layouts.master')

@section('title') Início @endsection

@section('content')

<div class="container-fluid" style="background-color:#5C7CB6; font-family:Arial, Helvetica, sans-serif; padding:5px; text-shadow: 1px 1px black; height: 30px; font-weight: bold; color: #fff;">
    "Olá, seja bem-vindo(a) {{session()->get('usuario.nome', 'usuario.perfis')}}"
</div>



    <div class="row marcador align-items-center">
        <div class= "col mx-auto text-center" style="margin-top: 250px;">
    <img class="img-responsive" src="{{ URL::asset('/images/logo.jpg')}}" width="250">
        </div>
    </div>




@endsection
