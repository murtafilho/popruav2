@section('css')
    <style>
      #map {
        height: 600px;  
        width: 100%; 
       }

       .hover{
        background-color:#3C8DBC;
        color:white;
        cursor: pointer;

        }

    </style>
@endsection

<h3>Ponto: {{$ponto->logradouro.' '.$ponto->numero.' - '.$ponto->bairro.' '.$ponto->regional.' '.$ponto->complemento}}</h3>
@include('geo._search')
<input type="hidden" id="ponto_id" value="{{$ponto->id}}">
<table class="table table-responsive" id="enderecamentos-table">
    <thead>
        <th>Idend</th>
        <th>Id Logrado</th>
        <th>Sigla Tipo</th>
        <th>Nome Logra</th>
        <th>Numero Imo</th>
        <th>Letra Imov</th>
        <th>Nome Bairr</th>
        <th>Nome Regio</th>
        <th>Cep</th>
        <th>Leste</th>
        <th>Norte</th>

    </thead>
    <tbody>
    @foreach($enderecamentos as $enderecamento)
        <tr>
            <td>{!! $enderecamento->IDEND !!}</td>
            <td>{!! $enderecamento->ID_LOGRADO !!}</td>
            <td>{!! $enderecamento->SIGLA_TIPO !!}</td>
            <td>{!! $enderecamento->NOME_LOGRA !!}</td>
            <td>{!! $enderecamento->NUMERO_IMO !!}</td>
            <td>{!! $enderecamento->LETRA_IMOV !!}</td>
            <td>{!! $enderecamento->NOME_BAIRR !!}</td>
            <td>{!! $enderecamento->NOME_REGIO !!}</td>
            <td>{!! $enderecamento->CEP !!}</td>
            <td>{!! $enderecamento->LESTE !!}</td>
            <td>{!! $enderecamento->NORTE !!}</td>
        </tr>
    @endforeach
    </tbody>
</table>

{!! $enderecamentos->appends(Request::except('page'))->links() !!}

<div class="row">
  <div class="">
  <br>
    <form class="form-inline" action="#">
    <div class="form-group">
        <label for="lat1" class="form-controll">Lat: </label>
        <input type="text" class="form-control" id="lat1" disabled>
    </div>
    <div class="form-group">
    <label for="lng1" class="form-controll">Lng: </label>
        <input type="text" class="form-control" id="lng1" disabled>
    </div>
    <span id="label1" class=""> Ponto georreferenciado com sucesso!</span>
    <button id="btn-georreferenciar" type="submit" class="btn btn-primary">Georreferenciar Ponto</button>
    </form>
    <br>
    </div><!-- /input-group -->
  </div><!-- /.col-lg-6 -->
</div><!-- /.row -->
<div id="map"></map>
@section('scripts')
<script>
    var latini = {{$ponto->lat}} + "";
    var lngini = {{$ponto->lng}} + "";   
    $(function(){
        if(latini != ""){
            initMap(parseFloat(latini) ,parseFloat(lngini));
            $("#lat1").val(latini);
            $("#lng1").val(lngini);
        }
        
    })
    $("#label1").hide();
    $("#btn-georreferenciar").hide();
    $(function(){
        $('tbody tr').hover(function() {
        $(this).addClass('hover');
    }, function() {
        $(this).removeClass('hover');
    });
    

    $('tbody tr').click(function(){
        var leste = $(this).children().eq(9).text();
        var norte = $(this).children().eq(10).text();
        url = "{{route('converter')}}";
        $.ajax({
            url: url,
            data:{
                leste:leste,
                norte:norte
            },
            success: function(result){
                result = result.split(',');
                lat = parseFloat(result[0]);
                lng = parseFloat(result[1]);
                $("#lat1").val(lat);
                $("#lng1").val(lng);
                
                initMap(lat,lng);
            }
        })
        $("#btn-georreferenciar").toggle(1000);
    })

    $('#btn-georreferenciar').click(function(event){
        var lat = $("#lat1").val();
        var lng = $("#lng1").val();
        var id = $('#ponto_id').val();
        url = "{{route('georreferenciar')}}";
        $.ajax({
            url: url,
            data:{
                lat:lat,
                lng:lng,
                id:id
            },
            success: function(result){
                console.log(result);
                
            }
        })
        $(this).toggle(1000);
        $("#label1").toggle(1000);
        event.preventDefault();
    })

})
</script>
<script>
var map;

function initMap(lat,lng) {

    var ponto = {lat: lat, lng: lng};
    var markers = [];
    
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 16,
        center: ponto,
        mapTypeId: 'hybrid',
        tilt: 0
    });

    map.addListener('click', function(event) {
        setMapOnAll(null);
        addMarker(event.latLng);
        $("#lat1").val(event.latLng.lat());
        $("#lng1").val(event.latLng.lng());
            $("#label1").hide();
            $("#btn-georreferenciar").show();

    });

    function setMapOnAll(map) {
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(map);
        }
    }

    addMarker(ponto);

    function addMarker(ponto) {
        var marker = new google.maps.Marker({
        position: ponto,
        map: map
        });
        markers.push(marker);
        
    }
}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBSYnyUUzk1tMCsLVDbnUL6g8zmAWmml7c&"
async defer></script>
@endsection