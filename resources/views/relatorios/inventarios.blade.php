@extends('layouts.master')

@section('title')Inventário @endsection

@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">               
                    <form action="/inventarios" class="form-horizontal mt-4" method="GET">
                    @csrf  
                    <div class="row"> 
                        <div class="col-2">Data
                            <input type="date" class="form-control" name='data' value="{{$data}}" required>
                        </div>
                        <div class="col">Categoria
                            <select class="form-control select2" id="lista1" name="categoria" placeholder="categoria" onchange="toggleLista('lista1')" multiple="multiple">
                            <option value="">Todos</option>
                            @Foreach($resultCategorias as $results)
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
                        <div class="col">
                            <input class="btn btn-light" type="submit" value="Pesquisar" style="box-shadow: 1px 2px 5px #000000;margin-top:20px;">
                        
                            <a href="/inventarios"><input class="btn btn-light" type="button" value="Limpar" style="box-shadow: 1px 2px 5px #000000;margin-top:20px;"></a>
                        
                            <a href=""><input class="btn btn-info" onclick="cont();" type="button" value="Imprimir" style="margin-top:20px;"></a>
                        </div>                        
                    </div>
                    </form>
                    <script>
                        function cont(){
                        var conteudo = document.getElementById('print').innerHTML;
                        tela_impressao = window.open('about:blank');
                        tela_impressao.document.write(conteudo);
                        tela_impressao.window.print();
                        tela_impressao.window.close();
                        }
                    </script>
    
                    <hr>
                    <div id='print' class='conteudo'>
                    <div class="container" style="background:#ffffff;">
                    <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">INVENTÁRIO DE ESTOQUE</h4>
                        <div class="row">
                        <h6 class="font-weight-bold" style="color: blue;  margin-left: 10px;">RELAÇÃO DOS MATERIAIS EM ESTOQUE - no dia <span class="badge badge-secondary">{{ \Carbon\Carbon::parse($data)->format('d/m/Y')}}</span> </h6>
                            <table class="table table-sm table-striped">
                                <thead style="text-align:center; background: #daffe0;">
                                    <tr style="text-align:center; font-weight: bold; font-size:15px">
                                    <td>NR</td>
                                    <td>NOME</td>
                                    <td>VALOR UNITÁRIO</td>
                                    <td>QUANTIDADE</td>
                                    <td>SUBTOTAL</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($resultItens as $rit )
                                    <tr style="text-align:center;">
                                        <td>{{$nr_ordem++}}</td>
                                        <td style="text-align:center;">{{$rit->nome}}</td>
                                        <td>{{number_format($rit->valor_venda,2,',','.')}}</td>
                                        <td>{{$rit->qtd}}</td>
                                        <td>{{number_format($rit->qtd * $rit->valor_venda,2,',','.')}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>

                                <tfoot style="background: #daffe0;">
                                        <tr style="text-align:center; font-weight: bold; font-size:15px">
                                        <td></td>
                                        <td></td>
                                        <td>TOTAIS</td>
                                        <td>{{$total_itens}}</td>
                                        <td>{{number_format($total_soma,2,',','.')}}</td>
                                    </tr>
                                </tfoot>
                            </table>
                            <h6 class="col-12  font-weight-bold" style="color: blue; margin-left: 10px; text-align:right;">O relatório foi impresso em <span class="badge badge-secondary">{{ \Carbon\Carbon::today()->locale('pt')->isoFormat('DD MMMM YYYY')}}</span> </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
            <script src="{{ URL::asset('/js/pages/mascaras.init.js')}}"></script>
            <script src="{{ URL::asset('/js/pages/busca-cep.init.js')}}"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
            <script src="{{ URL::asset('/js/pages/form-advanced.init.js')}}"></script>
@endsection


