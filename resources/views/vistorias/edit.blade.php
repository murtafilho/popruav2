@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Editando vistoria
        </h1>
        <h3>
            Ponto vistoriado: {!! $ponto->Resumo !!}
        </h3>

   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($vistoria, ['route' => ['vistorias.update', $vistoria->id], 'method' => 'patch']) !!}

                        @include('vistorias.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection