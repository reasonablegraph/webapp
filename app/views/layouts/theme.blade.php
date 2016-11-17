@extends('layouts.standalone')

@section('head')
<link rel="stylesheet" type="text/css" href="/_assets/css/archive.css">
<link rel="stylesheet" type="text/css" href="/_assets/bootstrap/bootstrap.css">

<script type="text/javascript" src="/_assets/vendor/masonry/masonry.pkgd.min.js"></script>
<script type="text/javascript" src="/_assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>

@stop


@section('header')
<div style="background-color:olive;">
HEADER
<br/>
<br/>
</div>
@stop

@section('menu')
<div style="background-color:greenyellow;">

@foreach( $menus as $menu )
  @if( is_array($menu['link']) )
    <li class="dropdown">
      <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"> {{ $menu['title'] }} <b class="caret"></b></a>
        <ul class="dropdown-menu">
          @foreach( $menu['link'] as $subMenu )
            <li><a href=" {{ $subMenu['link'] }} "> {{ $subMenu['title'] }} </a></li>
          @endforeach
        </ul>
      </li>
  @else
      <li><a href="{{ $menu['link'] }}"> {{ $menu['title'] }} </a></li>
  @endif
@endforeach

</div>
@stop



@section('footer')

<hr/>
<div style="background-color:olive;">
<p>FOOTER</p>
</div>
@stop
