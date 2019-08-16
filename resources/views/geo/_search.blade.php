<form class="navbar-form col-sm-6" role="search" action="{{route('geo',['ponto_id'=>$ponto->id])}}">
<div class="form-group">
    {!! Form::select('tipo_busca',['0' => 'Contém o termo','1'=>'Inicia com'],null, ['class' => 'form-control']) !!}
</div>
    <div class="input-group add-on">
        <input class="form-control" placeholder="Logradouro" name="logradouro" id="" type="text" value="{{$logradouro}}">
    </div>
    <div class="input-group add-on">
        <input class="form-control" placeholder="Numero" name="numero" id="" type="text" value="{{$numero}}">
    </div>
    <div class="input-group add-on">
        <div class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
        </div>
    </div>

    <div class="input-group add-on">
        <div class="input-group-btn">
            <a href="{{session('url_lista_pontos')}}" class="btn btn-default"><i class="glyphicon glyphicon-chevron-left"></i>Retornar</a>
        </div>
    </div>

</form>