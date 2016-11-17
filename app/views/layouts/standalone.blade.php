<html>
<head>
@yield('head')
<title><?=$title?></title>
</head>
<body>

<div id="header">
	@yield('header')
</div>

<div id="menu">
	@yield('menu')
</div>

<div class="arch-wrap">
	@yield('content')
</div>

<div id="footer">
	@yield('footer')
</div>
</body>
</html>