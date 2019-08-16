@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Editar Endereçamento
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($endereco, ['route' => ['enderecos.update', $endereco->id], 'method' => 'patch']) !!}

                        @include('enderecos.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection