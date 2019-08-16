@extends('layouts.app')

@section('content')
<div class="content">
    <form class="form-inline" action="{{route('processar.migracao')}}">
        <div class="form-group col-sm-3" required>
            {!! Form::label('ponto_id', 'Inserir o #ID do ponto de transferência:') !!}
            <input type="number" name="ponto_id" id="ponto_id" class="form-control">
            <button type="submit" class="form-control btn btn-danger">Confirmar</button>
        </div>
    </form>

    <br><br>
    <h3>As vistorias abaixo serão migradas para o ponto de ID# indicado acima</h3>
    <br>
    @include('vistorias.table')
</div>
@endsection
