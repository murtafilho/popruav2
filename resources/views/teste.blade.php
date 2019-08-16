@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            {!! Form::model($data, ['route' => ['teste'], 'method' => 'patch']) !!}

            <!-- Endereco Reclamante Id Field -->
                <div class="form-group col-sm-6">
                    {!! Form::label('endereco_reclamante_id', 'Endereçamento Reclamante:') !!}
                    <select class="form-control" name="endereco_reclamante_id" id="endereco_reclamante_id">
                        @if(isset($data))
                            <option value="{{$data->id}}">{{$data->text}}</option>
                        @endif
                    </select>
                </div>
            {!! Form::close() !!}

        </div>
    </div>
@endsection