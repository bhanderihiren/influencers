<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a class="brand-link">
    <img src="{{ assets('images/top-logo.png'); }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <!--<span class="brand-text font-weight-light">India Skill Challenge</span>-->
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <?php /*
      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>
    <?php */ ?>
    <!-- Sidebar Menu -->
   <nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

    <li class="nav-item">
      <a href="#" class="nav-link">
        <i class="nav-icon fas fa-users"></i>
        <p>
          User Management
          <i class="fas fa-angle-left right"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="{{ route('admin-user') }}" class="nav-link {{ isActiveRoute('admin-user') }}">
            <i class="far fa-circle nav-icon"></i>
            <p>All User</p>
          </a>
        </li>
      </ul>
    </li>
    <li class="nav-item">
      <a href="{{ route('category') }}" class="nav-link {{ isActiveRoute('category') }}">
        <i class="nav-icon fas fa-user-slash"></i>
        <p>Category</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('review.report') }}" class="nav-link {{ isActiveRoute('review.report') }}">
        <i class="nav-icon fas fa-user-slash"></i>
        <p>Report Issue</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('admin.reviews') }}" class="nav-link {{ isActiveRoute('admin.reviews') }}">
        <i class="nav-icon fas fa-user-slash"></i>
        <p>Reviews</p>
      </a>
    </li>
  </ul>
   </nav>


    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
