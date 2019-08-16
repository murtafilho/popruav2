@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">Pontos Vistoriados</h1>
        <h1 class="pull-right">
           <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('pontos.create') !!}">Novo Ponto</a>
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>
            
        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                @include('pontos._search')
                    @include('pontos.table')

            </div>

        </div>
    </div>
@endsection

