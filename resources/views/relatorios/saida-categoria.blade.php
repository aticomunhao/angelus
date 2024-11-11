@extends('layouts.master')

@section('title') Saídas por categoria @endsection

@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">               
                <form action="/saida-categoria" class="form-horizontal mt-4" method="GET">
                @csrf   
                <div class="row">
                    <div class="col">Início
                        <input type="date" class="form-control" name='data_inicio' value="{{$data_inicio}}" default="$today = Carbon::today();">
                    </div>
                    <div class="col">Final
                        <input type="date" class="form-control" name='data_fim' value="{{$data_fim}}" default="$today = Carbon::today();">
                    </div>
                    <div class="col">Categoria
                        <select class="form-control select2" id="cat" name="categoria" placeholder="categoria" multiple="multiple" >
                        @Foreach($result as $results)
                        <option value="{{$results->id}}">{{$results->nome}}</option>
                        @endForeach
                        </select>
                    </div>
                    <div class="col-sm">Comprado?<br>
                    <select class="form-control" id="compra" name="compra">
                            <option value="" {{ $compra === null ? 'selected' : '' }}>Todos</option>
                            <option value="true" {{ $compra === 'true' ? 'selected' : '' }}>Sim</option>
                            <option value="false" {{ $compra === 'false' ? 'selected' : '' }}>Não</option>
                        </select>
                    </div>
                    <div class="col">Depósito:
                        <select class="form-control" id="" name="deposito">
                            <option value="">Todos</option>    
                            @foreach($deposito as $dep)
                            <option value="{{$dep->iddep}}" {{request('deposito') == $dep->iddep ? 'selected' : ''}}>{{$dep->nome}}</option>
                            @endforeach
                        </select>
                    </div>       
                    <div class="col"><br>
                        <input class="btn btn-light" type="submit" style="font-weight:bold; font-size:15px; box-shadow: 1px 2px 5px #000000;color:blue; margin:5px;" value="Pesquisar">
                    </div>
                    <div class="col"><br>
                        <a href="/saida-categoria"><input class="btn btn-light" style="font-weight:bold; font-size:15px; box-shadow: 1px 2px 5px #000000; color:red; margin:5px;" type="button" value="Limpar"></a>
                    </div>
                </form>                    
                    <div class="col"><br>
                        <a href=""><input class="btn btn-info" onclick="cont();" type="button" style="font-weight:bold; font-size:15px; box-shadow: 1px 2px 5px #000000; margin:5px;" value="Imprimir"></a>
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
        <div id='print' class='conteudo'> 
            <div class="row">
                <div class="col-12">       
                <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">SAÍDAS POR CATEGORIA no período de {{ date( 'd/m/Y' , strtotime($data_inicio))}} até {{ date( 'd/m/Y' , strtotime($data_fim))}}</h4>
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-hover table-dark"  style="font-size:15px;">
                                <thead style="text-align:center;">
                                    <tr class="table-success" style="color:#000;">
                                        <td>NR</td>
                                        <td>CATEGORIA</td>                
                                        <td>COMPRADO?</td>
                                        <td>QUANT</td>
                                        <td>TOTAL</td>
                                    </tr>
                                </thead>
                                <tbody  class="table-sm">
                                    @foreach ($saidacat1 as $saidamats )
                                    <tr style="text-align:center;">
                                        <td>{{$nr_ordem++}}</td>
                                        <td style="text-align:left;">{{$saidamats->nome_cat}}</td>
                                        <td style="text-align:center;">
                                        @if($saidamats->adquirido == true)
                                            Sim
                                        @else
                                            Não
                                        @endif
                                        </td>
                                        <td>{{$saidamats->qnt_cat}}</td>
                                        <td>{{number_format($saidamats->vlr_final,2,',','.')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-success" style="color:#000;">
                                    <tr style="text-align:center; font-weight: bold; font-size:15px">
                                    <td></td>
                                    <td></td>
                                    <td>Soma total de saídas</td>
                                    <td>{{number_format($total3,0,',','.')}}</td>
                                    <td>{{number_format($total1,2,',','.')}}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>      
            <hr>      
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">   
                            <table class="table table-hover table-dark" style="font-size:15px;">
                                <tbody style="text-align:center; vertical-align:middle;">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td  style="text-align:right; font-weight: bold;">TOTAL DESCONTOS -></td>
                                        <td style="text-align:left;">{{number_format($total_desconto,2,',','.')}}</td>
                                    </tr>
                                    <tr class="table-light" style="color:#000;">
                                        <td></td>
                                        <td style="font-weight: bold;"></td>
                                        <td style="text-align:right; font-weight: bold;">DISCRIMINAÇÃO DOS PAGAMENTOS</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td  style=" text-align:right;"></td>
                                        <td style=" text-align:right;"><i class="fa-solid fa-money-bill-1-wave"></i> Em Dinheiro:</td>
                                        <td style="text-align:left;">{{number_format($total_din,2,',','.')}}</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td style=" text-align:right;"></td><td style=" text-align:right;"><i class="fab fa-cc-visa"></i> No Débito:</td>
                                        <td style="text-align:left;">{{number_format($total_deb,2,',','.')}}</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td style=" text-align:right;"></td>
                                        <td style=" text-align:right;"><i class="fab fa-cc-visa"></i> No Crédito:</td>
                                        <td style="text-align:left;">{{number_format($total_cre,2,',','.')}}</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td style=" text-align:right;"></td>
                                        <td style=" text-align:right;"><i class="fa-solid fa-money-check"></i> Em Cheque:</td>
                                        <td style=" text-align:left;">{{number_format($total_che,2,',','.')}}</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td style=" text-align:right;"></td>
                                        <td style=" text-align:right;"><i class="fa-brands fa-pix"></i> Em Pix:</td>
                                        <td style=" text-align:left;">{{number_format($total_pix,2,',','.')}}</td>
                                    </tr>
                                    <tr class="table-success" style="color:#000; text-align:right;font-size:14px;font-weight:bold;">
                                        <td></td>
                                        <td></td>
                                        <td>TOTAL VENDIDO NO PERÍODO -></td>
                                        <td style="text-align:left;font-size:15px;">{{number_format($total2,2,',','.')}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> 
        </div>    
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



