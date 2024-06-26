@extends('layouts.master')

@section('title') Cadastrar usuário @endsection

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title" style="color: rgb(255, 0, 0)">Usuário <i class="ti-user"></i></h4>
                    <hr>
                    <div class="card-body">
                        <p>NOME:<strong> {{$result[0]->nome}}</strong></p>
                        <p>CPF: <strong> {{$result[0]->cpf}}</strong> </p>
                        <p>IDENTIDADE:<strong>  {{$result[0]->identidade}}</strong> </p>
                        <p>DATA NASCIMENTO:<strong>  {{$result[0]->data_nascimento}}</strong> </p>
                        <p>EMAIL: <strong> {{$result[0]->email}}</strong> </p>
                    </div>

                <form id="meuFormulario"  class="form-horizontal mt-4" method="POST" action="/cad-usuario/inserir">
                @csrf
                <input type="hidden" name="idPessoa" value="{{$result[0]->id}}">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <tr>
                                <td>
                                    Ativo
                                </td>
                                <td>
                                    <input type="checkbox" id="ativo" name="ativo" switch="bool" checked />
                                    <label for="ativo" data-on-label="Sim" data-off-label="Não"></label>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    Bloqueado
                                </td>
                                <td>
                                    <input type="checkbox" id="bloqueado" name="bloqueado" switch="bool" checked />
                                    <label for="bloqueado" data-on-label="Sim" data-off-label="Não"></label>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card" >
                <div class="card-body" >
                    <div class="row">
                        <div class="col-sm">
                            <h4 class="card-title" style="color: rgb(255, 0, 0)">Selecionar Perfis <i class="ti-key" ></i></h4>
                        </div>
                    </div>
                    <hr>
                    <div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                @foreach($resultPerfil as $resultPerfils)
                                <tr>
                                    <td>
                                        {{$resultPerfils->nome}}
                                    </td>
                                    <td>
                                        <input type="checkbox" class="perfil" id="{{$resultPerfils->nome}}" name="{{$resultPerfils->nome}}" value="{{$resultPerfils->id}}" />
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </div><br><br>
                        <h4 class="card-title" style="color: rgb(255, 0, 0)">Selecionar Depósito <i class="ti-unlock" ></i> </h4>
                        <hr>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mb-0">
                                  @foreach($resultDeposito as $resultDepositos)
                                    <tr>
                                        <td>
                                            {{$resultDepositos->nome}}
                                        </td>
                                        <td>
                                            <input type="checkbox" class="dep" id="{{$resultDepositos->nome}}" name="{{$resultDepositos->nome}}" value="{{$resultDepositos->id}}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <button type="submit" class="btn btn-success btn-block">Cadastrar</button>
                    </div>
                    <div class="col">
                        <a href="/gerenciar-usuario">
                            <input class="btn btn-danger btn-block" type="button" value="Cancelar">
                        </a>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>

    <script>

    document.getElementById('meuFormulario').addEventListener('submit', function(event) {
        var checkboxes = document.querySelectorAll('input[class="dep"]:checked');

        if (checkboxes.length === 0) {
            alert('Por favor, selecione pelo menos um depósito.');
            event.preventDefault(); // Evita o envio do formulário
        }
    });

    document.getElementById('meuFormulario').addEventListener('submit', function(event) {
        var checkboxes = document.querySelectorAll('input[class="perfil"]:checked');

        if (checkboxes.length === 0) {
            alert('Por favor, selecione pelo menos um perfil.');
            event.preventDefault(); // Evita o envio do formulário
        }
    });

</script>

@endsection

@section('footerScript')
            <!-- Required datatable js -->
           <script src="{{ URL::asset('/libs/datatables/datatables.min.js')}}"></script>
            <script src="{{ URL::asset('/libs/jszip/jszip.min.js')}}"></script>
            <script src="{{ URL::asset('/libs/pdfmake/pdfmake.min.js')}}"></script>

            <!-- Datatable init js -->
            <script src="{{ URL::asset('/js/pages/datatables.init.js')}}"></script>
            <script src="{{ URL::asset('/libs/select2/select2.min.js')}}"></script>
            <script src="{{ URL::asset('/js/pages/form-advanced.init.js')}}"></script>

@endsection
