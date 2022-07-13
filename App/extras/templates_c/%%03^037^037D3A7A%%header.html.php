<?php /* Smarty version 2.6.31, created on 2022-07-13 10:04:26
         compiled from ../views/partials/header.html */ ?>
<nav class="main-header navbar navbar-expand-md navbar-light navbar-white" style="margin-left: 0px;">
  <div class="container">
    <a href="http://local.ems.com/admin" class="navbar-brand">
      <span class="brand-text font-weight-dark mr-2">EMS</span>
    </a>

    <div class="collapse navbar-collapse order-3" id="navbarCollapse">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a href="http://local.ems.com/app/holidays" class="nav-link">Holidays</a>
        </li>
        <li class="nav-item dropdown">
          <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Leaves</a>
          <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
            <li><a href="http://local.ems.com/app/leaveBalancesheet" class="dropdown-item">Leave Balancesheet </a></li>
            <li><a href="http://local.ems.com/app/leaves" class="dropdown-item">Leave Applications</a></li>
          </ul>
        </li>
      </ul>
    </div>
    <!-- Right navbar links -->
    <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link font-weight-bold" data-toggle="dropdown" href="#"><?php echo $this->_tpl_vars['sessionUserName']; ?>
<i class="fa-solid fa-caret-down ml-2"></i></a>
        </a>
        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
          <a href="http://local.ems.com/app/logout" class="dropdown-item">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
          </a>
        </div>
      </li>
      <!-- Notifications Dropdown Menu -->
  </div>
</nav>