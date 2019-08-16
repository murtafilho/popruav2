@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">Pontos</h1>
        <h1 class="pull-right">

            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('vistorias.create.detail',$ponto->id )!!}">Nova vistoria para este ponto</a>

        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('pontos.show_fields')
                    @if(isset($vistorias))
                    @include('vistorias.table')
                    @endif
                    <br>
                    <a href="{!! route('pontos.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
