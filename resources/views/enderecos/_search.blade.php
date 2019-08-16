<form class="navbar-form" role="search">
    <div class="input-group add-on">
        <input class="form-control" placeholder="Logradouro" name="logradouro" id="logradouro" type="text" value="{{$request->logradouro}}">
    </div>
    <div class="input-group add-on">
        <input class="form-control" placeholder="Bairro" name="bairro" id="regional" type="text" value="{{$request->bairro}}">
    </div>
    <div class="input-group add-on">
        <input class="form-control" placeholder="Regional" name="regional" id="regional" type="text" value="{{$request->regional}}">
        <div class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
        </div>
    </div>
</form>
