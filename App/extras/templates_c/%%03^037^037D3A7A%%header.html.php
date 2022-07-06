<?php /* Smarty version 2.6.31, created on 2022-07-06 11:41:04
         compiled from ../views/partials/header.html */ ?>
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand-lg navbar-light navbar-white border-bottom">
    <div class="container">
      <a href="http://local.ems.com/admin" class="navbar-brand">
        <span class="brand-text font-weight-bold h5 mr-3 mt-2">EMS</span>
      </a>
      <div class="collapse navbar-collapse order-3" id="navbarCollapse">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a href="http://local.ems.com/admin/users" class="nav-link">Users</a>
          </li>
          <li class="nav-item">
            <a href="http://local.ems.com/admin/holidays" class="nav-link">Holidays</a>
          </li>
          <li class="nav-item">
            <a href="http://local.ems.com/admin/leaveBalancesheet" class="nav-link">Leaves</a>
          </li>
      </div>

      <!-- Right navbar links -->
      <ul class="order-3 order-md-3 navbar-nav navbar-no-expand ml-auto">
        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link font-weight-bold" data-toggle="dropdown" href="#"><?php echo $this->_tpl_vars['sessionAdminName']; ?>
<i class="fa-solid fa-caret-down ml-2"></i></a>
          </a>
          <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
            <a href="http://local.ems.com/admin/logout" class="dropdown-item">
              <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </a>
          </div>
        </li>
          
           </div>
           
  </nav>
  <!-- /.navbar -->