@extends('layouts.master')

@section('title') Editar Cadastro inicial em Lote @endsection


@section('headerCss')


@endsection


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form  class="form-horizontal mt-4" method="GET" >
                <div class="card-body">

                    <div class="row d-flex align-items-center">
                        <div class="col-auto">Início:
                            <input type="date" style="width: 140px;" class="form-control" name="data_inicio" value="{{$data_inicio}}">
                        </div>
                        <div class="col-auto">Final:
                            <input type="date" style="width: 140px;" class="form-control" name="data_fim" value="{{$data_fim}}">
                        </div>
                        <div class="col">Nome material:
                            <input class="form-control" type="text" name="material" value="{{$material}}">
                        </div>

                        <div class="col-2">Categoria:
                            <select class="form-control custom-select2" id="categoria" name="categoria">
                            <option value="">Todos</option>
                            @Foreach($resultCat as $resultCats)
                                <option value="{{$resultCats->id}}" {{$resultCats->id == $categoria ? 'selected': ''}}>{{$resultCats->nome}}</option>
                            @endForeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <input class="btn btn-outline-info" type="submit" style="font-size: 14px;" formaction="{{route('filtra.edit')}}" value="Pesquisar">

                            <a href="/filtrar-para-editar"><input class="btn btn-outline-warning btn-md"  style="font-size: 14px;" type="button" value="Limpar"></a>

                            <button type="button" class="btn btn-outline-warning btn-md" data-toggle="modal" data-target="#filtrar" class="btn btn-outline-danger btn-sm" data-placement="top"
                                        title="Filtrar">+ Filtros<i class="mdi mdi-filter" style="font-size: 1rem; color:#303030;"></i></button>
                            <!-- Modal -->
                            <div class="modal fade" id="filtrar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog ">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color:#11ce70;">
                                            <h5 class="modal-title" id="exampleModalLabel"
                                                style=" color:rgb(255, 255, 255)">Filtro complementar
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-size: 30px;">&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row form-group">
                                                    <div class="col">
                                                    <label for="1">Código inicial:</label>
                                                    <input class="form-control" type="numeric" name="identidade1" value="{{$identidade1}}">
                                                </div>
                                                <div class="col">
                                                    <label for="2">Código final:</label>
                                                    <input class="form-control" type="numeric" name="identidade2" value="{{$identidade2}}">
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-sm">
                                                    <label for="3">Observação:</label>
                                                    <input class="form-control" type="text" name="obs" value="{{$obs}}">
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-sm">
                                                    <label for="4">Ref Fabricante:</label>
                                                    <input class="form-control" type="text" name="ref_fab" value="{{$ref_fab}}">
                                                </div>
                                                <div class="col-sm">
                                                    <label for="5">Comprado?</label>
                                                    <select class="form-control" id="compra" name="compra">
                                                        <option value="">Todos</option>
                                                        <option value="true" {{ $compra === 'true' ? 'selected' : '' }}>Sim</option>
                                                        <option value="false" {{ $compra === 'false' ? 'selected' : '' }}>Não</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer mt-2">
                                            <button type="button" class="btn btn-danger btn-md" data-dismiss="modal">Cancelar</button>
                                            <input class="btn btn-success btn-md" type="submit" formaction="/filtrar-para-editar" value="Pesquisar">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Fim Modal-->

                        </div>
                    </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">MODIFICAR CADASTROS INICIAIS EM LOTE</h4>
                        </div>
                        <form method="POST" action="{{ route('cadastro-inicial.lote.update') }}">
                        @csrf
                        <div class="col" style="text-align: right;">
                            <button type="submit" class="btn btn-outline-success" >Executar alteração</button>
                        </div>
                    </div>
                    <div class="card">
                            Itens filtrados: {{$contar}}
                            <table id="datatable" class="display table-resposive-lg table-bordered table-striped table-hover" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                              <thead>
                                <tr style="font-size:15px; text-align:center; background-color:#b3edf1">
                                    <th><input type="checkbox" id="select-all"></th>
                                    <th>CÓDIGO</th>
                                    <th>CATEGORIA</th>
                                    <th>NOME</th>
                                    <th class="col-3">OBSERVAÇÃO</th>
                                    <th class="col-1">REF FABRICA</th>
                                    <th>CADASTRO</th>
                                    <th>MARCA</th>
                                    <th>TAMANHO</th>
                                    <th>COR</th>
                                    <th>COMPRA</th>
                                    <th class="col-1">VALOR</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th> <input type="text" id="novo-obs" class="form-control form-control-sm col-input" placeholder="Novo texto"></th>
                                    <th><input type="text" id="novo-ref" class="form-control form-control-sm col-input" placeholder="Nova Ref">
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><input type="text" id="novo-valor" class="form-control form-control-sm col-input" placeholder="Novo Valor"></th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($result as $results)
                                <tr style="text-align: center;">
                                    <td style="text-align:center;">
                                        <input type="checkbox" class="row-check" name="rows[{{ $results->id }}][update]" value="1">
                                        <input type="hidden" name="rows[{{ $results->id }}][id]" value="{{ $results->id }}">
                                    </td>
                                    <td>{{ $results->id }}</td>
                                    <td>{{ $results->nome_cat }}</td>
                                    <td>{{ $results->n1 }}</td>
                                    <td><input type="text" name="rows[{{ $results->id }}][obs]" class="form-control form-control-sm" value="{{ $results->obs }}"></td>
                                    <td><input type="text" name="rows[{{ $results->id }}][ref_fab]" class="form-control form-control-sm" value="{{ $results->ref_fab }}"></td>
                                    <td>{{ date('d/m/Y', strtotime($results->data_cadastro)) }}</td>
                                    <td>{{ $results->n2 }}</td>
                                    <td>{{ $results->n3 }}</td>
                                    <td>{{ $results->n4 }}</td>
                                    <td> @if($results->adquirido == true)
                                                Sim
                                            @else
                                                Não
                                            @endif
                                    </td>
                                    <td><input type="text" name="rows[{{ $results->id }}][valor_venda]" style="text-align: center;" class="form-control form-control-sm" value="{{ number_format($results->valor_venda,2,',','.') }}"></td>
                                </tr>
                                @endforeach
                            </tbody>
                            </table>
                        </form>
                            {{-- <div class="d-flex justify-content-center">
                            {{$result->withQueryString()->links()}}
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const selectAll = document.getElementById("select-all");
    const rowChecks = document.querySelectorAll(".row-check");

    if (selectAll) {
        // Quando clicar no "Selecionar todos"
        selectAll.addEventListener("change", function () {
            rowChecks.forEach(chk => {
                chk.checked = selectAll.checked;
            });
        });

        // Quando marcar/desmarcar individualmente, atualiza o estado do "select all"
        rowChecks.forEach(chk => {
            chk.addEventListener("change", function () {
                const allChecked = document.querySelectorAll(".row-check:checked").length === rowChecks.length;
                selectAll.checked = allChecked;
            });
        });
    }
});
</script>

    <script>
        $(document).ready(function() {
            $('#categoria').select2({
                placeholder: 'Selecione uma Categoria',
                allowClear: true
            });

            // Ajustar a altura do campo
            $('#categoria').next('.select2-container').find('.select2-selection--single').css({
                height: '33px', // Altura desejada
                display: 'flex',
                'align-items': 'center', // Alinha o texto verticalmente
                'font-size': '12px' // Ajuste do tamanho da fonte
            });
        });

    </script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('form[action="{{ route('cadastro-inicial.lote.update') }}"]') || document.querySelector('form');
  const novoObs = document.getElementById('novo-obs');
  const novoRef = document.getElementById('novo-ref');
  const novoValor = document.getElementById('novo-valor');
  const tableEl = document.getElementById('datatable');

  // detect DataTables (jQuery)
  let dt = null;
  try {
    if (window.jQuery && $.fn.dataTable && tableEl) {
      dt = $('#datatable').DataTable();
    }
  } catch (err) {
    dt = null;
  }

  form.addEventListener('submit', function (e) {
    // obtém os nós das linhas filtradas (DataTables-aware) ou todas as linhas <tr>
    let nodes = [];
    if (dt) {
      // rows({search:'applied'}) pega todas as linhas que passaram no filtro (todas as pages)
      const result = dt.rows({ search: 'applied' }).nodes();
      // result pode ser um array-like; convertemos para Array
      nodes = Array.from(result);
    } else {
      nodes = Array.from(document.querySelectorAll('#datatable tbody tr'));
    }

    // conta quantos marcados entre as linhas filtradas
    const checkedCount = nodes.filter(n => n.querySelector && n.querySelector('.row-check:checked')).length;
    const applyAll = (checkedCount === 0);

    // se for aplicar a todos e seu backend precisa saber disso (ex: server-side DataTables),
    // adicionamos um campo hidden para sinalizar
    if (applyAll) {
      if (!form.querySelector('input[name="apply_all_filtered"]')) {
        const h = document.createElement('input');
        h.type = 'hidden';
        h.name = 'apply_all_filtered';
        h.value = '1';
        form.appendChild(h);
      }
    } else {
      // remove caso exista e não seja necessário
      const existing = form.querySelector('input[name="apply_all_filtered"]');
      if (existing) existing.remove();
    }

    // aplica os valores
    nodes.forEach(n => {
      if (!n.querySelector) return;
      const checkbox = n.querySelector('.row-check');
      if (!checkbox) return;

      const shouldApply = applyAll || checkbox.checked;
      if (!shouldApply) return;

      // inputs na linha
      const obsInput = n.querySelector('input[name*="[obs]"]');
      const refInput = n.querySelector('input[name*="[ref_fab]"]');
      const valorInput = n.querySelector('input[name*="[valor_venda]"]');

      if (novoObs && novoObs.value.trim() !== '' && obsInput) obsInput.value = novoObs.value;
      if (novoRef && novoRef.value.trim() !== '' && refInput) refInput.value = novoRef.value;
      if (novoValor && novoValor.value.trim() !== '' && valorInput) valorInput.value = novoValor.value;

      // se estamos aplicando a todos, garantimos que o checkbox será enviado ao backend
      if (applyAll) {
        checkbox.checked = true;
        checkbox.setAttribute('checked', 'checked');
      }
    });

    // deixa o submit continuar (não fazemos preventDefault)
  });
});
</script>




@endsection

@section('footerScript')
 <!-- Required datatable js -->

            <script src="{{ URL::asset('/libs/jszip/jszip.min.js')}}"></script>
            <script src="{{ URL::asset('/libs/pdfmake/pdfmake.min.js')}}"></script>


@endsection
