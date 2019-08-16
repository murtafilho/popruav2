<div class="col-sm-6">
    <h3>Ponto vistoriado: {!! $vistoria->logradouro.' '.$vistoria->numero !!}</h3>
<table class="table table-responsive table-bordered table-striped">
    <tr>
        <td>ID#</td>
        <td>{{$vistoria->id}}</td>
    </tr>
    <tr>
        <td>Data abordagem</td>
        <td>{{$vistoria->data_abordagem}}</td>
    </tr>
    <tr>
        <td>Resultado da ação</td>
        <td>{{$vistoria->resultado_acao}}</td>
    </tr>
    <tr>
        <td>Tipo de abordagem</td>
        <td>{{$vistoria->tipo_abordagem}}</td>
    </tr>
    <tr>
        <td>Complexidade</td>
        <td>{{$vistoria->complexidade}}</td>
    </tr>
    <tr>
        <td>Numero reduzido?</td>
        <td>{{$vistoria->num_reduzido}}</td>
    </tr>
    <tr>
        <td>Catador reciclados?</td>
        <td>{{$vistoria->catador_reciclados}}</td>
    </tr>
    <tr>
        <td>Houve resistência</td>
        <td>{{$vistoria->resistencia}}</td>
    </tr>
    <tr>
        <td>Fixação antiga?</td>
        <td>{{$vistoria->fixacao_antiga}}</td>
    </tr>
    <tr>
        <td>Estrutura abrigo provisório</td>
        <td>{{$vistoria->estrutura_abrigo_provisorio}}</td>
    </tr>
    <tr>
        <td>Exesso de objetos?</td>
        <td>{{$vistoria->resistencia}}</td>
    </tr>
    <tr>
        <td>Trafico de ilícitos?</td>
        <td>{{$vistoria->trafico_ilicitos}}</td>
    </tr>
    <tr>
        <td>Presença de menores ou idosos?</td>
        <td>{{$vistoria->menores_idosos}}</td>
    </tr>
    <tr>
        <td>Presença de deficientes?</td>
        <td>{{$vistoria->deficiente}}</td>
    </tr>
    <tr>
        <td>Agrupamento químico</td>
        <td>{{$vistoria->resistencia}}</td>
    </tr>
    <tr>
        <td>Saúde mental?</td>
        <td>{{$vistoria->saude_mental}}</td>
    </tr>
    <tr>
        <td>Presença de animais?</td>
        <td>{{$vistoria->resistencia}}</td>
    </tr>
    <tr>
        <td>Encaminhamento 1</td>
        <td>{{$vistoria->encaminhamento1}}</td>
    </tr>
    <tr>
        <td>Encaminhamento 2</td>
        <td>{{$vistoria->encaminhamento2}}</td>
    </tr>
    <tr>
        <td>Encaminhamento 3</td>
        <td>{{$vistoria->encaminhamento3}}</td>
    </tr>
    <tr>
        <td>Encaminhamento 4</td>
        <td>{{$vistoria->encaminhamento4}}</td>
    </tr>
    <tr>
        <td>Material apreendido?</td>
        <td>{{$vistoria->material_apreendido}}</td>
    </tr>
    <tr>
        <td>Material descartado?</td>
        <td>{{$vistoria->material_descartado}}</td>
    </tr>
    <tr>
        <td>Tipo abrigo desmontado</td>
        <td>{{$vistoria->tipo_abrigo_desmontado}}</td>
    </tr>
    <tr>
        <td>Quantidade recolhimento</td>
        <td>{{$vistoria->qtd_kg}}</td>
    </tr>

    <tr>
        <td>Movimento migratório</td>
        <td>{{$vistoria->movimento_migratorio}}</td>
    </tr>
    <tr>
        <td>Observação</td>
        <td>{{$vistoria->observacao}}</td>
    </tr>
</table>
</div>

