<li class="{{ Request::is('home*') ? 'active' : '' }}">
    <a href="{!! route('home') !!}"><i class="fa fa-edit"></i><span>Home</span></a>
</li>
<li class="{{ Request::is('pontos*') ? 'active' : '' }}">
    <a href="{!! route('pontos.index') !!}"><i class="fa fa-edit"></i><span>Pontos</span></a>
</li>

<li class="{{ Request::is('vistorias*') ? 'active' : '' }}">
    <a href="{!! route('vistorias.index') !!}"><i class="fa fa-edit"></i><span>Vistorias</span></a>
</li>


