<!-- Des Rote Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Des_Rote', 'Des Rote:') !!}
    {!! Form::text('Des_Rote', null, ['class' => 'form-control']) !!}
</div>

<!-- Sgl Rote Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Sgl_Rote', 'Sgl Rote:') !!}
    {!! Form::text('Sgl_Rote', null, ['class' => 'form-control']) !!}
</div>

<!-- Sit Rote Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Sit_Rote', 'Sit Rote:') !!}
    {!! Form::text('Sit_Rote', null, ['class' => 'form-control']) !!}
</div>

<!-- Sec Rote Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Sec_Rote', 'Sec Rote:') !!}
    {!! Form::text('Sec_Rote', null, ['class' => 'form-control']) !!}
</div>

<!-- Cls Rote Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Cls_Rote', 'Cls Rote:') !!}
    {!! Form::text('Cls_Rote', null, ['class' => 'form-control']) !!}
</div>

<!-- Tip Estb Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Tip_Estb', 'Tip Estb:') !!}
    {!! Form::text('Tip_Estb', null, ['class' => 'form-control']) !!}
</div>

<!-- Cod Func Incl Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Cod_Func_Incl', 'Cod Func Incl:') !!}
    {!! Form::text('Cod_Func_Incl', null, ['class' => 'form-control']) !!}
</div>

<!-- Dat Incl Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Dat_Incl', 'Dat Incl:') !!}
    {!! Form::date('Dat_Incl', null, ['class' => 'form-control']) !!}
</div>

<!-- Cod Func Altr Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Cod_Func_Altr', 'Cod Func Altr:') !!}
    {!! Form::text('Cod_Func_Altr', null, ['class' => 'form-control']) !!}
</div>

<!-- Dat Altr Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Dat_Altr', 'Dat Altr:') !!}
    {!! Form::date('Dat_Altr', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('roteiros.index') !!}" class="btn btn-default">Cancel</a>
</div>
