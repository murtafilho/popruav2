<form class="navbar-form" role="search">

<div class="form-group">
    {!! Form::select('tipo_busca',['0' => 'Inicia com','1'=>'Contém o termo'],null, ['class' => 'form-control']) !!}
</div>
    <div class="input-group add-on">
        <input class="form-control" placeholder="Logradouro" name="logradouro" id="srch-term" type="text" value="{{$logradouro}}">
    </div>
    <div class="input-group add-on">
        <input class="form-control" placeholder="Numero" name="numero" id="srch-term" type="text" value="{{$numero}}">
    </div>
    <div class="input-group add-on">
        <div class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
        </div>
    </div>
</form>
