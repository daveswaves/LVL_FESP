<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title')</title>

<link  rel="stylesheet" href="http://fonts.googleapis.com/css?family=Nunito">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<script src="/js/jquery-1.12.4.min.js"></script>

<link  rel="stylesheet" href="/css/style.css">
<link rel="stylesheet" href="/css/form_style.css">
<link rel="stylesheet" href="/css/modal.css">

</head>
<body>
    
<header class="header">
    <div class="dropdown fr">
        <!-- https://www.w3schools.com/icons/fontawesome_icons_webapp.asp -->
        <!-- https://www.w3schools.com/icons/tryit.asp?filename=tryicons_fa-navicon *** Requires font-awesome.min.css -->
        <button class="dropbtn fa">&#xf0c9;</button>
        <div class="dropdown-content view-menu-width">
            <a href="{{ route('undispatched') }}">Undispatched</a>
            <a href="{{ route('products') }}">Products</a>
            <a href="{{ route('createOrder') }}">Create Order</a>
            <a href="{{ route('createOrder') }}/?pf=am&id=026-5959042-4144314">Reorder (single)</a>
            <a href="{{ route('createOrder') }}/?pf=am&id=203-3459969-9978761">Reorder (multi)</a>
            <a href="{{ route('phpinfo') }}" target="_blank">phpinfo</a>
        </div>
    </div>
    @yield('header')
</header>

<div class="h40"></div>

@yield('content')

<div class="h40"></div>


<footer>
    @yield('footer')
</footer>

</body>
</html>