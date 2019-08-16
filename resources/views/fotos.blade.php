@extends('layouts.app')

@section('content')



<div class="content">

    {!! Form::open(['route' => 'fotos.store', 'files' => true,'class'=>'form-inline']) !!}
      <div class="form-group">
         <input type="file" class="form-control" id="url" name="url">
      </div>
    <input type="hidden" value="{{$vistoria_id}}" name="vistoria_id" id="vistoria_id">
      <div class="form-group">
          {!! Form::submit('Enviar',['class'=>'form-control btn btn-primary']) !!}
      </div>

    {!! Form::close() !!}
<br>
  @foreach ($fotos as $foto)
  <div class="row">
      <div class="col-sm-12">
        <div class="thumbnail">
          <img src="/storage/{{ $foto->url }}" alt="...">
          <div class="caption">
            <h3>{{'Vistoria: '.\Carbon\Carbon::parse($vistoria->data_abordagem)->format('d-m-Y').' '.
    $vistoria->tipo.' '.$vistoria->logradouro.' '.$vistoria->numero}}</h3>

            
            {!! Form::open(['route' => ['fotos.destroy', $foto->id], 'method' => 'delete','class'=>'form-inline']) !!}
            <div class="form-group">
            {!! Form::submit('Apagar',['class'=>'form-control btn btn-danger']) !!}
            </div>
            {!! Form::close() !!}
          </div>
        </div>
      </div>
    </div>
  @endforeach

</div>

@endsection
