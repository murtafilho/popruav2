<!-- Data Abordagem Field -->
<div class="form-group col-sm-1">
    {!! Form::label('data_abordagem', 'Data*') !!}
    @if(isset($now))
        {!! Form::text('data_abordagem', $now, ['class' => 'form-control datepicker','required'=>'required']) !!}
    @else
        {!! Form::text('data_abordagem', null, ['class' => 'form-control datepicker','required'=>'required']) !!}
    @endif
</div>

<div class="form-group col-sm-3" required>
    {!! Form::label('ponto_id', 'Ponto*') !!}
    <select class="form-control" name="ponto_id" id="ponto_id" required>
        @if(isset($ponto))
            <option value="{{$ponto->id}}">{{$ponto->endereco->logradouro.' '.$ponto->numero.' - '.$ponto->endereco->bairro}}</option>
        @endif
    </select>
</div>

<div class="form-group col-sm-1">

    {!! Form::label('tipo_abordagem_id', 'Abordagem*') !!}
    {!! Form::select('tipo_abordagem_id',
     $tipo_abordagem,
     null, ['class' => 'form-control','placeholder'=>'Selecionar','required'=>'required']) !!}
</div>

<!-- Resultado Acao Field -->
<div class="form-group col-sm-2">
    {!! Form::label('resultado_acao_id', 'Resultado Acao*') !!}
    {!! Form::select('resultado_acao_id',
    $resultados_acoes,null
    , ['class' => 'form-control','placeholder'=>'Selecionar','required'=>'required']) !!}
</div>

<!-- Nomes Pessoas Field -->
<div class="form-group col-sm-3">
    {!! Form::label('nomes_pessoas', 'Nomes Pessoas:') !!}
    {!! Form::text('nomes_pessoas', null, ['class' => 'form-control']) !!}
</div>

<!-- Quantidade Pessoas Field -->
<div class="form-group col-sm-1">
    {!! Form::label('quantidade_pessoas', 'Qt. Pessoas:') !!}
    {!! Form::number('quantidade_pessoas', null, ['class' => 'form-control']) !!}
</div>

<!-- Nivel Complexidade Field -->
<div class="row"></div>
<!-- Casal Field -->
<div class="form-group col-sm-2"> 
    {{ Form::hidden('casal', false) }}
    {{ Form::checkbox('casal', true) }}
    {!! Form::label('casal', 'Casal') !!}
</div>

<!-- Num Reduzido Field -->
<div class="form-group col-sm-2">
    {{ Form::hidden('num_reduzido', false) }}
    {{ Form::checkbox('num_reduzido', true) }}
    {!! Form::label('num_reduzido', 'Num reduzido') !!}
</div>


<!-- Catador Reciclados Field -->
<div class="form-group col-sm-2">
    
    {{ Form::hidden('catador_reciclados', false) }}
    {{ Form::checkbox('catador_reciclados', true) }}
    {!! Form::label('catador_reciclados', 'Catador reciclados') !!}
</div>


<!-- Resistencia Field -->
<div class="form-group col-sm-2">
    {{ Form::hidden('resistencia', false) }}
    {{ Form::checkbox('resistencia', true) }}
    {!! Form::label('resistencia', 'Resistência') !!}
</div>


<!-- Fixacao Antiga Field -->
<div class="form-group col-sm-2">
    {{ Form::hidden('fixacao_antiga', false) }}
    {{ Form::checkbox('fixacao_antiga', true) }}
    {!! Form::label('fixacao_antiga', 'Fixacao antiga') !!}
</div>

<!-- Estrutura Abrigo Provisorio Field -->
<div class="form-group col-sm-2">
    {{ Form::hidden('estrutura_abrigo_provisorio', false) }}
    {{ Form::checkbox('estrutura_abrigo_provisorio', true) }}
    {!! Form::label('estrutura_abrigo_provisorio', 'Estrutura abrigo provisório') !!}
</div>

<!-- Excesso Objetos Field -->
<div class="form-group col-sm-2">
    {{ Form::hidden('excesso_objetos', false) }}
    {{ Form::checkbox('excesso_objetos', true) }}
    {!! Form::label('excesso_objetos', 'Excesso Objetos') !!}
</div>

<!-- Trafico Ilicitos Field -->
<div class="form-group col-sm-2">
    {{ Form::hidden('trafico_ilicitos', false) }}
    {{ Form::checkbox('trafico_ilicitos', true) }}
    {!! Form::label('trafico_ilicitos', 'Trafico Ilicitos') !!}
</div>

<!-- Menores Idosos Field -->
<div class="form-group col-sm-2">
    {{ Form::hidden('menores_idosos', false) }}
    {{ Form::checkbox('menores_idosos', true) }}
    {!! Form::label('menores_idosos', 'Menores idosos') !!}
</div>

<!-- Deficiente Field -->
<div class="form-group col-sm-2">
    {{ Form::hidden('deficiente', false) }}
    {{ Form::checkbox('deficiente', true) }}
    {!! Form::label('deficiente', 'Deficiente') !!}
</div>

<!-- Agrupamento Quimico Field -->
<div class="form-group col-sm-2">
    {{ Form::hidden('agrupamento_quimico', false) }}
    {{ Form::checkbox('agrupamento_quimico', true) }}
    {!! Form::label('agrupamento_quimico', 'Agrupamento quimico') !!}
</div>

<!-- Saude Mental Field -->
<div class="form-group col-sm-2">
    {{ Form::hidden('saude_mental', false) }}
    {{ Form::checkbox('saude_mental', true) }}
    {!! Form::label('saude_mental', 'Saude mental') !!}
</div>

<!-- Animais Field -->
<div class="form-group col-sm-2">
    {{ Form::hidden('animais', false) }}
    {{ Form::checkbox('animais', true) }}
    {!! Form::label('animais', 'Animais') !!}
</div>

<div class="row"></div>

<!-- E1 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('e1_id', 'Encaminhamento 1') !!}
    {!! Form::select('e1_id', $encaminhamentos ,null, ['class' => 'form-control','placeholder'=>'Selecionar...']) !!}
</div>

<!-- E2 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('e2', 'Encaminhamento 2') !!}
    {!! Form::select('e2_id', $encaminhamentos ,null, ['class' => 'form-control','placeholder'=>'Selecionar...']) !!}
</div>

<!-- E3 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('e3_id', 'Encaminhamento 3') !!}
    {!! Form::select('e3_id', $encaminhamentos ,null, ['class' => 'form-control','placeholder'=>'Selecionar...']) !!}
</div>

<!-- E4 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('e4_id', 'Encaminhamento 4') !!}
    {!! Form::select('e4_id', $encaminhamentos ,null, ['class' => 'form-control','placeholder'=>'Selecionar...']) !!}
</div>

<!-- Material Apreendido Field -->
<div class="form-group col-sm-6">
    {!! Form::label('material_apreendido', 'Material Apreendido:') !!}
    {!! Form::text('material_apreendido', null, ['class' => 'form-control']) !!}
</div>

<!-- Material Descartado Field -->
<div class="form-group col-sm-6">
    {!! Form::label('material_descartado', 'Material Descartado:') !!}
    {!! Form::text('material_descartado', null, ['class' => 'form-control']) !!}
</div>

<!-- Tipo Abrigo Desmontado Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tipo_abrigo_desmontado_id', 'Tipo Abrigo Desmontado') !!}
    {!! Form::select('tipo_abrigo_desmontado_id',$tipo_abrigo_desmontado, null, ['class' => 'form-control','placeholder'=>'Selecionar...']) !!}
</div>

<!-- Qtd Kg Field -->
<div class="form-group col-sm-6">
    {!! Form::label('qtd_kg', 'Qt. Kg:') !!}
    {!! Form::number('qtd_kg', null, ['class' => 'form-control']) !!}
</div>


<!-- Movimento Migratorio Field -->
<div class="form-group col-sm-6">
    {!! Form::label('movimento_migratorio', 'Movimento Migratorio') !!}
    {!! Form::text('movimento_migratorio', null, ['class' => 'form-control']) !!}
</div>

<!-- Observacao Field -->
<div class="form-group col-sm-6">
    {!! Form::label('observacao', 'Observacao:') !!}
    {!! Form::text('observacao', null, ['class' => 'form-control']) !!}
</div>


<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('vistorias.index') !!}" class="btn btn-default">Cancel</a>
</div>

@section('scripts')
    <script>
        $(function () {
            $('.datepicker').datepicker({dateFormat: 'dd-mm-yy'});

            select2($('#ponto_id'), "{{route('autocomplete.ponto')}}", "Selecionar");

        })
    </script>

@endsection
