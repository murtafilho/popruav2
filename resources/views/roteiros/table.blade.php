

<table class="table table-responsive table-bordered" id="roteiros-table">
    <thead>
        <th>GRUPO</th> 
        <th>SUBGRUPO</th>
        <th>ITEM</th>

    </thead>
    <tbody>
    @foreach($roteiros as $roteiro)
        <tr>
        <td>{{$roteiro->Des_Grp}}</td>
        <td>{{$roteiro->Des_Subg_Item}}</td>
        <td>{{$roteiro->Des_Item}}</td>
        
        <td><a class="btn btn-primary btn-block" href="{{route('componente', $roteiro->Idn_Item)}}" role="button">ITEM {{$roteiro->Idn_Item}}</a></td>
        </tr>
    @endforeach
    </tbody>
</table>
{!! $roteiros->appends(Request::except('page'))->links() !!}

@section('scripts')
<script src="{{asset('/js/jQuery.highlight.js')}}"></script>
<script>
    $('td').highlight('{{$request->q}}', {
        color: 'white',
        background: 'purple',
        ignoreCase: true,
        wholeWord: false,
        bold: false
    });

    $('td').highlight(
        'INCOMPLETO',
        {
        color: 'white',
        background: 'grey',
        ignoreCase: true,
        wholeWord: false,
        bold: false
    });

</script>
@endsection