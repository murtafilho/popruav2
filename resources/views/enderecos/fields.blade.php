<!-- Logradouro Id Field -->
<!-- Tipo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tipo', 'Tipo:') !!}
    {!! Form::select('tipo',$tipos, null, ['class' => 'form-control','placeholder'=>'Selecionar']) !!}
</div>

<!-- Logradouro Field -->
<div class="form-group col-sm-6">
    {!! Form::label('logradouro', 'Logradouro:') !!}
    {!! Form::text('logradouro', null, ['class' => 'form-control']) !!}
</div>

<!-- Bairro Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bairro', 'Bairro:') !!}
    {!! Form::text('bairro', null, ['class' => 'form-control']) !!}
</div>

<!-- Regional Field -->
<div class="form-group col-sm-6">
    {!! Form::label('regional', 'Regional:') !!}
    {!! Form::select('regional',$regionais, null, ['class' => 'form-control','placeholder'=>'Selecionar']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('enderecos.index') !!}" class="btn btn-default">Cancel</a>
</div>
