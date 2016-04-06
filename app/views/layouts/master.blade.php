<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width" />
<title>BlackMarketU {{ isset($title) ? $title : '' }}</title>
<link rel="stylesheet" href="{{ URL::asset('assets/styles/style.css') }}">
<link rel="stylesheet" href="{{ URL::asset('assets/styles/dropzone.css') }}">
<script src="{{ URL::asset('assets/js/jquery-2.1.3.js') }}"></script>
<script src="{{ URL::asset('assets/js/dropzone.js') }}"></script>
<script src="{{ URL::asset('assets/js/masonry.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/imagesloaded.pkgd.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/isotope.pkgd.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/functionality.js') }}"></script>
<link href='http://fonts.googleapis.com/css?family=Raleway:700,400' rel='stylesheet' type='text/css'>
</head>

<body>
  @if(Auth::check())
  <header id="logged-in">
  @else
  <header>
  @endif
    <div class="wrapper">
      <nav class="the-main-menu">
        <ul class="logo-list">
          <li class="top-left-logo"><a href="{{ URL::Route('home') }}"><img src="{{ URL::asset('assets/images/logo-2.png') }}" class="logo"></a></li>
        </ul>
        <ul class="top-menu-list">
          @if(Auth::check())
          <li class="menu-item"><a href="{{ URL::Route('profile', [Auth::user()->username]) }}" class="menu-link">Profile</a></li>
          <li class="menu-item"><a href="{{ URL::Route('account-logout') }}" class="menu-link">Logout</a></li>
          @else
          <li class="menu-item"><a href="#" class="menu-link">About Us</a></li>
          <li class="menu-item"><a href="# " class="menu-link">Discover</a></li>
          <li class="menu-item"><a href="{{ URL::Route('account-create') }}" class="menu-link">Sign Up</a></li>
          <li class="menu-item divider">|</li>
          <li class="menu-item"><a href="{{ URL::Route('account-login') }}" class="menu-link" class="menu-link">Login</a></li>
          @endif
        </ul>
      </nav>
    </div>
    @if(Auth::check())
    @include('authenticated.mainsubmenu')
    @endif
  </header>
    <div id="content">
      @yield('content')
    </div>
    </div>
    </body>
    </html>
