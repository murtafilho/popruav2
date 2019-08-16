@include('enderecos._search')
@if($enderecos->count()>0)
<table class="table table-responsive table-hover" id="enderecos-table">
    <thead>
        <th>Tipo</th>
        <th>Logradouro</th>
        <th>Bairro</th>
        <th>Regional</th>
        <th colspan="3">Ação</th>
    </thead>
    <tbody>
    @foreach($enderecos as $endereco)
        <tr>
            <td>{!! $endereco->tipo !!}</td>
            <td>{!! $endereco->logradouro !!}</td>
            <td>{!! $endereco->bairro !!}</td>
            <td>{!! $endereco->regional !!}</td>
            <td>
                {!! Form::open(['route' => ['enderecos.destroy', $endereco->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('enderecos.show', [$endereco->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('enderecos.edit', [$endereco->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Tem certeza?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{!! $enderecos->appends(Request::except('page'))->links() !!}
    @endif