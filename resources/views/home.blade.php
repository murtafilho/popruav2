@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div style="margin-top: 20px"></div>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="info-box">
              <span class="info-box-icon bg-aqua"><i class="ion ion-ios-gear-outline"></i></span>
  
              <div class="info-box-content">
                <span class="info-box-text">Número de pontos vistoriados</span>
                <span class="info-box-number">{{$num_pontos}}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>

          <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="info-box">
              <span class="info-box-icon bg-aqua"><i class="ion ion-ios-gear-outline"></i></span>
  
              <div class="info-box-content">
                <span class="info-box-text">Número de vistorias</span>
                <span class="info-box-number">{{$num_vistorias}}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
    </div>
</div>
@endsection
