@extends('layouts.master')

@section('title') Relatório de vendas @endsection

@section('content')



    <div class="container">
        <div class="row" style="text-align: left;">
            <div class="col-12">
                <form action="/relatorio-vendas" class="form-horizontal mt-4" method="GET" >
                    @csrf
                <div class="row align-items-center" style="position: sticky">
                    <div class="col">Início                        
                        <input type="date" class="form-control" name="data_inicio" value="{{$data_inicio}}">
                    </div>
                    <div class="col">Final                        
                        <input type="date" class="form-control" name="data_fim"  value="{{$data_fim}}">
                    </div>    
                    <div class="col">Comprado?<br>
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
                    <div class="col">
                        <input class="btn btn-light" type="submit" value="Pesquisar" style="box-shadow: 1px 2px 5px #000000; margin-top:20px;">
                    </div>
                    </form>
                    <div class="col">
                        <a href="/relatorio-vendas"><input class="btn btn-light" type="button" value="Limpar" style="box-shadow: 1px 2px 5px #000000; margin-top:20px;"></a>                    
                    </div>
                    <div class="col">
                        <a href=""><input class="btn btn-info" onclick="cont();" type="button" value="Imprimir"></a>                    
                    </div>                       
                </div>
            </div>
        </div>
        <hr>
        <div id='print' class='conteudo'>
        <div class="row">
            <div class="col-12">
                <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">RELATÓRIO DE VENDAS POR PERÍODO</h4>
                <br>
                <div class="container" style="background:#ffffff;">
                    <div class="row">
                        <table class="table table-sm table-striped table-bordered">
                            <thead style='font-size:12px; background:#ffffff; text-align:center;vertical-align:middle'>
                                <tr>
                                    <th colspan="1">CÓDIGO</th>
                                    <th colspan="1">DATA</th>
                                    <th colspan="1">CLIENTE</th>
                                    <th colspan="1">VALOR ORIGINAL</th>
                                    <th colspan="1">VALOR DESCONTO</th>
                                    <th colspan="1">VALOR FINAL</th>
                                </tr>
                            </thead>
                            <tbody style='font-size:10px; text-align:center;vertical-align:middle'>
                            @foreach($rela as $ra)
                                <tr>
                                    <td>{{$ra->idv}}</td>
                                    <td>{{ date( 'd/m/Y' , strtotime($ra->data))}}</td>
                                    <td>{{$ra->nomep}}</td>
                                    <td>{{number_format($ra->vlr_original,2,',','.')}}</td>
                                    <td>{{number_format($ra->desconto,2,',','.')}}</td>
                                    <td>{{number_format($ra->vlr_final,2,',','.')}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            @if($rela->currentPage() === $rela->lastPage())
                            <tfoot style='background:#ffffff;'>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align:right;font-size:12px;font-weight: bold;">TOTAL</th>
                                    <th style="text-align:center;font-size:12px;font-weight: bold; background-color:yellow">{{number_format($soma_origem,2,',','.')}}</th>
                                    <th style="text-align:center;font-size:12px;font-weight: bold; background-color:indianred; color:#ffffff;">{{number_format($total_desconto,2,',','.')}}</th>
                                    <td style="text-align:center;font-size:12px;font-weight: bold; background-color:forestgreen; color:#ffffff;">{{number_format($total1,2,',','.')}}</td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                        <div class="d-flex justify-content-center">
                        {{$rela->withQueryString()->links()}}
                    </div>
                </div>
            </div>
        </div>
        <hr>
        @if($rela->currentPage() === $rela->lastPage())
        <div class="container" style="background:#ffffff;">
            <div class="row">            
                <table class="table table-sm table-striped">
                <h4 class="card-title" class="card-title" style="font-size:15px; text-align: left; color: gray; font-family:calibri">CÁLCULOS DO PERÍODO</h4>
                    <tbody style='text-align:center;vertical-align:middle; font-size:10px;'>
                        <tr>
                            <td></td><td></td><td  style="text-align:right; font-weight: bold;">TOTAL DESCONTOS -></td><td style="text-align:center;">{{number_format($total_desconto,2,',','.')}}</td></tr>
                        <tr style="text-align:right;"><td></td><td style="font-weight: bold;"></td><td style="text-align:right; font-weight: bold;">DISCRIMINAÇÃO DOS PAGAMENTOS</td></tr>
                        <tr><td></td><td  style=" text-align:right;"></td><td style=" text-align:right;"><i class="fa-solid fa-money-bill-1-wave"></i> Em Dinheiro:</td><td style="font-weight: bold;">{{number_format($total_din,2,',','.')}}</td></tr>
                        <tr><td></td><td style=" text-align:right;"></td><td style=" text-align:right;"><i class="fab fa-cc-mastercard"></i> No Débito:</td><td style="font-weight: bold;">{{number_format($total_deb,2,',','.')}}</td></tr>
                        <tr><td></td><td style=" text-align:right;"></td><td style=" text-align:right;"><i class="fab fa-cc-visa"></i> No Crédito:</td><td style="font-weight: bold;">{{number_format($total_cre,2,',','.')}}</td></tr>
                        <tr><td></td><td style=" text-align:right;"></td><td style=" text-align:right;"><i class="fa-solid fa-money-check"></i> Em Cheque:</td><td style="font-weight: bold;">{{number_format($total_che,2,',','.')}}</td></tr>
                        <tr><td></td><td style=" text-align:right;"></td><td style=" text-align:right;"><i class="fa-brands fa-pix"></i> Em Pix:</td><td style="font-weight: bold;">{{number_format($total_pix,2,',','.')}}</td></tr>
                        <tr style="text-align:right;font-size:12px;font-weight:bold;">
                        <td></td><td></td><td>TOTAL VENDIDO NO PERÍODO -></td>
                        <td style="text-align:right;font-size:12px;">{{number_format($total_pag,2,',','.')}}</td>
                        </tr>
                    </tbody>
                </table>
            
            </div>
            <div class="row">
                <div class="col-12">
                    <h6 class="font-weight-bold" style="font-size:12px; color: blue;  margin-left: 10px; text-align: right;">O relatório foi extraído em <span class="badge badge-secondary">{{ \Carbon\carbon::now()->subHour(1)->format('d / m / Y H:i:s')}}</span> </h6>
                </div>

            </div>
            <br><br><br><br>
            <div class="row">
                <div class="col-12" style="font-size: 12px; text-align:center; line-height:0%">
                    <h4 style="font-size: 15px; text-align: center; color:black;">{{session()->get('usuario.nome')}}</h4>Responsável pela informação
                </div>
            </div>
            @endif
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
@endsection


@section('footerScript')

@endsection



