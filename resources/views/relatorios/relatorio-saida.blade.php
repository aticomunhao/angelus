@extends('layouts.master')

@section('title') Relatório de saídas @endsection

@section('content')


<div class="col12" style="background:#ffffff;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12">
            <form action="/relatorio-saidas" class="form-horizontal mt-4" method="GET">
            @csrf
                <div class="row">
                    <div class="col">Início
                        <input type="date" class="form-control" name='data_inicio' value="{{$data_inicio}}" default="$today = Carbon::today();">
                    </div>
                    <div class="col">Final
                        <input type="date" class="form-control" name='data_fim' value="{{$data_fim}}" default="$today = Carbon::today();">
                    </div>
                    <div class="col-2">Categoria
                        <select class="form-control" id="cat" name="categoria" placeholder="categoria" >
                        <option value="">Todos</option>
                        @Foreach($result as $results)
                        <option value="{{$results->id}}">{{$results->nome}}</option>
                        @endForeach
                        </select>
                    </div>
                    <div class="col">Comprado?<br>
                        <select class="form-control" id="compra" name="compra">
                            <option value="">Todos</option>
                            <option value="true">Sim</option>
                            <option value="false">Não</option>
                        </select>
                    </div>
                    <div class="col-1"><br>
                        <input class="btn btn-info" type="submit" value="Pesquisar">
                    </div>
                    </form>
                    <div class="col-1"><br>
                        <a href="/relatorio-saidas"><input class="btn btn-warning" type="button" value="Limpar"></a>
                    </div>                
                    <div class="col-1"><br>
                        <a href="/gerenciar-vendas"><input class="btn btn-danger" type="button" value="Cancelar"></a>
                    </div>
                    <div class="col-1"><br>
                        <a href=""><input class="btn btn-success" onclick="cont();" type="button" value="Imprimir"></a>
                    </div>
                </div>
            
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
<h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">RELATÓRIO DE SAÍDAS POR PERÍODO</h4>
    <div class="row align-items-center">
        <table class="table table-sm table-striped">
            <thead style="text-align:center;">
                <tr style="text-align:center; font-weight: bold; font-size:15px; background: #daffe0;">
                <td>NR</td>
                <td>CATEGORIA</td>
                <td>NOME</td>                
                <td>COMPRADO?</td>
                <td>VALOR</td>
                <td style="text-align:center;">DATA SAÍDA</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($saidamat as $saidamats )
                <tr style="text-align:center;">
                    <td>{{$nr_ordem++}}</td>
                    <td style="text-align:left;">{{$saidamats->nomecat}}</td>
                    <td style="text-align:center;">{{$saidamats->nome}}</td>
                    <td style="text-align:center;">
                    @if($saidamats->adquirido == true)
                        Sim
                    @else
                        Não
                    @endif
                    </td>
                    <td>{{number_format($saidamats->valor_venda,2,',','.')}}</td>
                    <td style="text-align:center;">{{ date( 'd/m/Y' , strtotime($saidamats->data))}}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                    <tr style="text-align:center; font-weight: bold; font-size:15px">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Soma total de saídas</td>
                    <td>{{number_format($somasai,2,',','.')}}</td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

    </div>
</div>

@endsection

@section('footerScript')
            <script src="{{ URL::asset('/js/pages/mascaras.init.js')}}"></script>
            <script src="{{ URL::asset('/js/pages/busca-cep.init.js')}}"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
            <script src="{{ URL::asset('/libs/select2/select2.min.js')}}"></script>
            <script src="{{ URL::asset('/js/pages/form-advanced.init.js')}}"></script>
@endsection



