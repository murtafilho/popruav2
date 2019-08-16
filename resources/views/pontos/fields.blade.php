<div class="form-group col-sm-6">
    {!! Form::label('endereco_id', 'Endereçamento*') !!}
    <select class="form-control" name="endereco_id" id="endereco_id" required>
        @if(isset($ponto))
            <option value="{{$ponto->endereco->id}}">{{$ponto->endereco->logradouro.' - '.$ponto->endereco->bairro}}</option>
        @endif
    </select>
</div>

<div class="form-group col-sm-6">
    {!! Form::label('numero', 'Numero*') !!}
    {!! Form::text('numero', null, ['class' => 'form-control','required'=>'required']) !!}
</div>

<!-- Caracteristica Abrigo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('caracteristica_abrigo_id', 'Caracteristica Abrigo*') !!}
    {!! Form::select('caracteristica_abrigo_id',
     $caracteristica_abrigo,
     null, ['class' => 'form-control','placeholder'=>'Selecionar','required'=>'required']) !!}
</div>

<!-- Complemento Field -->
<div class="form-group col-sm-6">
    {!! Form::label('complemento', 'Complemento:') !!}
    {!! Form::text('complemento', null, ['class' => 'form-control']) !!}
</div>

<!-- Endereco Id Field -->


<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('pontos.index') !!}" class="btn btn-default">Cancel</a>
</div>
@section('scripts')
<script>

    $( document ).ready(function() {
        select2($('#endereco_id'),"{{route('autocomplete.endereco')}}","Selecionar");
    });
</script>

@endsection