<div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="{{ assets('images/AdminLTELogo.png'); }}" alt="AdminLTELogo" height="60" width="60">
  </div>

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto header-profile-nave">
    <div class="topbar_btn">
      <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle profile-picture" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <img class="animation__shake" src="{{ assets('images/AdminLTELogo.png'); }}" alt="AdminLTELogo" height="60" width="60">
        </button>
        <div class="dropdown-menu profile-drope-main" aria-labelledby="dropdownMenuButton">
          @if( !empty(Auth::user()) && Auth::user()->role == 2)
            <a href="{{ route('edit-profile') }}" class="header_icon_1 profile-drope-dowen">Edit Profile</a>
          @endif
          <a href="{{ route('password.change') }}" class="header_icon_1 profile-drope-dowen">Reset Password</a>
          <form method="POST" action="{{ route('logout') }}" class="d-inline" style="margin: 0; padding-top: 0;">
            @csrf
            <button type="submit" class="header_icon_3 profile-drope-dowen btn btn-link p-0 m-0">Log Out</button>
          </form>
        </div>
      </div>
    </div>
  </ul>
</nav>
<!-- /.navbar -->