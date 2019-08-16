@extends('layouts.app')

@section('search-form')
<form action="#" method="get" class="sidebar-form">
    <div class="input-group">
    <input type="text" name="q" class="form-control" placeholder="Buscar..."/>
  <span class="input-group-btn">
    <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
    </button>
  </span>
    </div>
</form>
@endsection

@section('content')
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        @isset($roteiros)
        <div class="box">
            <div class="box-body">
                    @include('roteiros.table')
            </div>
        </div>
        @endisset
    </div>
@endsection

