@section('css')
<style>
.hover{
        background-color:#3C8DBC;
        color:white
    }
</style>
@endsection

<table class="table table-responsive" id="vistorias-table">
    <thead>

    <th colspan="">Operações</th>
    <th>#ID</th>
    <th>Data</th>
    <th>Tipo</th>
    <th>Ponto</th>
    <th>Resultado ação</th>
    <th>Nomes Pessoas</th>
    <th>Num.Pessoas</th>
    <th>Tipo abordagem</th>
    <th>Tipo abrigo desmontado</th>
    <th>Classificação
    <th>

    </thead>
    <tbody>
    @foreach($vistorias as $vistoria)
        <tr>
            <td>
                {!! Form::open(['route' => ['vistorias.destroy', $vistoria->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('vistorias.show', [$vistoria->id]) !!}" class='btn btn-default btn-xs'><i
                                class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('vistorias.edit', [$vistoria->id]) !!}" class='btn btn-default btn-xs'><i
                                class="glyphicon glyphicon-edit"></i></a>
                    <a href="{!! route('fotos', [$vistoria->id]) !!}" class='btn btn-default btn-xs'><i
                                class="glyphicon glyphicon-camera"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Tem certeza?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
            <td>{!! $vistoria->id !!}</td>
            <td>{!!  \Carbon\Carbon::parse($vistoria->data_abordagem)->format('d-m-Y') !!}</td>
            <td>{!! $vistoria->tipo !!}</td>
            <td>{!! $vistoria->logradouro.' '.$vistoria->numero!!}</td>
            <td>{!! $vistoria->resultado_acao !!}</td>
            <td>{!! $vistoria->nomes_pessoas !!}</td>
            <td>{!! $vistoria->quantidade_pessoas !!}</td>
            <td>{!! $vistoria->tipo_abordagem !!}</td>
            <td>{!! $vistoria->tipo_abrigo_desmontado !!}</td>
            <td>{!! $vistoria->complexidade !!}</td>
        </tr>
    @endforeach
    </tbody>
</table>
{!! $vistorias->appends(Request::except('page'))->links() !!}
@section('scripts')
<script>
$('tbody tr').hover(function() {
    $(this).addClass('hover');
}, function() {
    $(this).removeClass('hover');
});
</script>
    
@endsection
