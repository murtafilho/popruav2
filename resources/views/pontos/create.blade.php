@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Novo Ponto
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'pontos.store']) !!}

                        @include('pontos.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
