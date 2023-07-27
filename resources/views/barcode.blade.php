@extends('layouts.master')

@section('title') Gerar código @endsection

@section('content')

    <div>
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
          @foreach($lista as $listas)
                <strong>
                    {!! DNS1D::getBarcodeSVG($listas->id_item, 'C128', 2, 40)!!}</br>
                    {{$listas->n1}}-{{$listas->n2}}-{{$listas->obs}}</br>
                    {{number_format($listas->valor_venda, 2,',','.')}}
                </strong><br>
            @endforeach
        </div>
    </div>
    @endsection

{{--
 <div class="container text-center" style="margin-top: 50px;">
    <h3 class="mb-5">Barcode Laravel</h3>
    <div>{!! DNS1D::getBarcodeHTML('2021050001', 'C39') !!}</div></br>
    <div>{!! DNS1D::getBarcodeHTML('4445645656', 'POSTNET') !!}</div></br>
    <div>{!! DNS1D::getBarcodeHTML('4445645656', 'PHARMA') !!}</div></br>
    <div>{!! DNS2D::getBarcodeHTML('4445645656', 'QRCODE') !!}</div></br>

<table>
    <div class="row">
       @foreach($itens as $p)
        <div class="col-md-3"><strong>
            {{"Os produtos do bazar são usados,"}}
            </br>
            {{"experimente-os. Não fazemos troca."}}
            </br>
            {!! DNS1D::getBarcodeSVG($p->id, 'EAN13', 2, 40)!!}
            </br>
            {{$p->nome}}</br>
            {{$p->valor_venda}}
        </strong>
        </div>
        @endforeach
    </div>
</table>
--}}
