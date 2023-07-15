<input type="hidden" id="idItem" value="{{$item[0]->id}}" >
<div class="row"> 
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Item<i class="mdi mdi-account-badge"></i></h4>
                <hr>
                <div class="card-body"> 
                    <p>Código:<strong> {{$item[0]->id}}</strong></p>
                    <p>Nome item: <strong> {{$item[0]->nome}}</strong> </p>
                    <p>Marca:<strong> {{$item[0]->marca}}</strong> </p>
                    <p>Tamanho:<strong>  {{$item[0]->tamanho}}</strong> </p>
                    <p>Cor: <strong> {{$item[0]->cor}}</strong> </p>
                    <p>Tipo de material: <strong> {{$item[0]->tipo_material}}</strong> </p>
                    <p>Valor Venda: <strong> {{number_format($item[0]->valor_venda, 2, ',', '.')}}</strong></p>
                    <p>Obs: <strong> {{$item[0]->observacao}}</strong> </p>
                    <input type="hidden" id="vlrVenda" value="{{$item[0]->valor_venda}}">
                </div>
            </div>
        </div>
 	</div>
</div>