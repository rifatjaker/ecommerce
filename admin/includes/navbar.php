<?php
// Get current page name for active navigation
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(45deg, #1e3c72, #2a5298);">
  <div class="container">
    <a class="navbar-brand" href="./">
      <img src="../favicon.ico" alt="Logo" style="height: 30px; margin-right: 10px;">
      Eyesome Sports <font color="yellow">(Admin)</font>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="adminNavbar">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page === 'index' || $current_page === 'dashboard') ? 'active' : ''; ?>" href="./">
            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page === 'add_category') ? 'active' : ''; ?>" href="add_category.php">
            <i class="fas fa-tags me-1"></i>Categories
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page === 'add_club') ? 'active' : ''; ?>" href="add_club.php">
            <i class="fas fa-shield-alt me-1"></i>Clubs
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page === 'add_product') ? 'active' : ''; ?>" href="add_product.php">
            <i class="fas fa-box me-1"></i>Products
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page === 'orders') ? 'active' : ''; ?>" href="orders.php">
            <i class="fas fa-shopping-cart me-1"></i>Orders
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page === 'users') ? 'active' : ''; ?>" href="users.php">
            <i class="fas fa-users me-1"></i>Users
          </a>
        </li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle me-1"></i>Admin
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
            <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="../index.html" target="_blank"><i class="fas fa-external-link-alt me-2"></i>View Site</a></li>
            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
