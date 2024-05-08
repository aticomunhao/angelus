@extends('layouts.master')

@section('title') Relatório de entradas @endsection

@section('content')


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="col-12" style="background:#ffffff;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12">
            <form action="/relatorio-entrada" class="form-horizontal mt-4" method="GET">
            @csrf
                <div class="row">
                    <div class="col">Início
                        <input type="date" class="form-control" name='data_inicio'  value="{{$data_inicio}}" default="$today = Carbon::today();">
                    </div>
                    <div class="col">Fim
                        <input type="date" class="form-control" name='data_fim' value="{{$data_fim}}" default="$today = Carbon::today();">
                    </div>
                    <div class="col">Categoria
                        <select class="form-control select2" id="lista1" name="categoria" placeholder="categoria" onchange="toggleLista('lista1')" multiple="multiple">
                        <option value="">Todos</option>
                        @Foreach($result as $results)
                        <option value="{{$results->id}}">{{$results->nome}}</option>
                        @endForeach
                        </select>
                    </div>
                    <div class="col">Item nome
                        <select class="form-control select2" id="lista2" name="nomeitem" placeholder="nomeitem" onchange="toggleLista('lista2')" multiple="multiple">
                        <option value=" ">Todos</option>
                        @Foreach($itemmaterial as $itemmat)
                        <option value="{{$itemmat->id}}">{{$itemmat->nome}}</option>
                        @endForeach
                        </select>
                    </div>
                    <div class="col">Comprado?<br>
                        <select class="form-control" id="compra" name="compra">
                            <option value="">Todos</option>
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>
                    <div class="col-3">
                            <input class="btn btn-light" type="submit" value="Pesquisar" style="box-shadow: 1px 2px 5px #000000; margin-top:20px;">
                   
                        <a href="/relatorio-entrada"><input class="btn btn-light" type="button" value="Limpar" style="box-shadow: 1px 2px 5px #000000;margin-top:20px;"></a>
                  
                        <a href=""><input class="btn btn-info" onclick="cont();" type="button" value="Imprimir" style="margin-top:20px;"></a>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
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
<br>
<div id='print' class='conteudo'>
<div class="container" style="background:#ffffff;">
<h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">RELATÓRIO DE ENTRADAS POR PERÍODO</h4>
    <div class="row align-items-center">
        <table class="table table-sm table-striped">
            <thead style="text-align:center;">
                <tr style="text-align:center; font-weight: bold; font-size:15px; background: #daffe0;">
                <td>NR</td>
                <td>NOME</td>
                <td>CATEGORIA</td>
                <td>COMPRADO?</td>
                <td>QUANTIDADE</td>
                <td>VALOR</td>
                <td style="text-align:center;">DATA ENTRADA</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($entmat as $entmats )
                <tr style="text-align:center;">
                    <td>{{$nr_ordem++}}</td>
                    <td style="text-align:center;">{{$entmats->nome}}</td>
                    <td style="text-align:center;">{{$entmats->nomecat}}</td>
                    <td >@if($entmats->adquirido == 0)
                        Não
                        @else
                        Sim
                        @endif
                    </td>
                    <td style="text-align:center;">{{$entmats->total}}</td>
                    <td>{{number_format($entmats->valor_venda,2,',','.')}}</td>
                    <td style="text-align:center;">{{ date( 'd/m/Y' , strtotime($entmats->data_cadastro))}}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                    <tr style="text-align:center; font-weight: bold; font-size:15px">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Soma total de entradas</td>
                    <td>{{$somait}}</td>
                    <td>{{number_format($somaent,2,',','.')}}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

    </div>
</div>
<script>
    function toggleLista(id) {
            var listaAtual = document.getElementById(id);
            var outrasListaId = (id === 'lista1') ? 'lista2' : 'lista1';
            var outrasLista = document.getElementById(outrasListaId);

            if (listaAtual.value !== '') {
                outrasLista.disabled = true;
            } else {
                outrasLista.disabled = false;
            }
        }
</script>

@endsection

@section('footerScript')
            <script src="{{ URL::asset('/js/pages/mascaras.init.js')}}"></script>
            <script src="{{ URL::asset('/js/pages/busca-cep.init.js')}}"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
            <script src="{{ URL::asset('/libs/select2/select2.min.js')}}"></script>
            <script src="{{ URL::asset('/js/pages/form-advanced.init.js')}}"></script>
@endsection



