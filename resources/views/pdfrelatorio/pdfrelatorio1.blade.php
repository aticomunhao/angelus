<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Teste</title>



</head>

<body>
 <div class="container-fluid" id="1" style="background:#ffffff;">
            <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">INVENTÁRIO DE ESTOQUE</h4>
                <div class="row">
                <h6 class="font-weight-bold" style="color: blue;  margin-left: 10px;">INVENTÁRIO DE ESTOQUE - no dia <span class="badge badge-secondary">{{ \Carbon\Carbon::parse($data)->format('d/m/Y')}}</span> </h6>
                    <table class="table table-sm table-striped">
                        <thead style="text-align:center; background: #daffe0;">
                            <tr style="text-align:center; font-weight: bold; font-size:12px">
                            <td>NR</td>
                            <td>CATEGORIA</td>
                            <td>NOME</td>
                            <td>COMPRA?</td>
                            <td>ENTRADA</td>
                            <td>VENDA</td>
                            <td>VLR UNID</td>
                            <td>QTD</td>
                            <td>SUBTOTAL</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($resultItens as $index => $rit )
                            <tr style="text-align:center; font-size:10px">
                                <td>{{ $index + 1}}</td>
                                <td style="text-align:center;">{{$rit->ncat}}</td>
                                <td style="text-align:center;">{{$rit->nome}}</td>
                                <td style="text-align:center;">
                                    @if($rit->adquirido == true)
                                        Sim
                                    @else
                                        Não
                                    @endif
                                </td>
                                <td>{{date( 'd-m-Y', strtotime($rit->data_cadastro))}}</td>
                                <td>{{ $rit->data ? date('d-m-Y', strtotime($rit->data)): '-' }}</td>
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
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>TOTAIS</td>
                                <td>{{number_format($total_itens, 0, '', '.')}}</td>
                                <td>{{number_format($total_soma,2,',','.')}}</td>
                            </tr>
                        </tfoot>            
                    </table>
                </div>
                    <h6 class="col-12  font-weight-bold" style="color: blue; margin-left: 10px; text-align:right;">O relatório foi impresso em <span class="badge badge-secondary">{{ \Carbon\Carbon::today()->locale('pt')->isoFormat('DD MMMM YYYY')}}</span> </h6>
            </div>
        </div>
</body>

</html>
       
