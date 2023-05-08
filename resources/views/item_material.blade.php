@extends('layouts.master')

@section('title') Gerar código @endsection

@section('content')

        <a href="/gerenciar-cadastro-inicial">
            <input class="btn btn-danger" type="button" value="Cancelar">
        </a>
        <a href="">
            <input class="btn btn-success" onclick="cont();" type="button" value="Imprimir">
        </a>
    </div>
    <script>
            function cont(){
               var conteudo = document.getElementById('print').innerHTML;
               tela_impressao = window.open('about:blank');
               tela_impressao.document.write(conteudo);
               tela_impressao.window.print();
               tela_impressao.window.close();
            }
        </script>
        <div id='print' class='conteudo'>
            <div class="Col" style="font-size: 14px; color:#000; text-align: center;">
            @foreach($itens as $p)
                <strong>
                    {!! DNS1D::getBarcodeSVG($p->id, 'C128', 2, 40)!!}</br>
                    {{$p->nome}}</br>
                    {{number_format($p->valor_venda, 2,',','.')}}</br>
                </strong>
            @endforeach
            </div>
        </div>
@endsection

    {{-- <div class="container text-center" style="margin-top: 50px;">
    <h3 class="mb-5">Barcode Laravel</h3>
    <div>{!! DNS1D::getBarcodeHTML('2021050001', 'C39') !!}</div></br>
    <div>{!! DNS1D::getBarcodeHTML('4445645656', 'POSTNET') !!}</div></br>
    <div>{!! DNS1D::getBarcodeHTML('4445645656', 'PHARMA') !!}</div></br> --}}
    {{-- <div>{!! DNS2D::getBarcodeHTML('4445645656', 'QRCODE') !!}</div></br> --}}

