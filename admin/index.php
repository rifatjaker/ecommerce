<?php
require_once 'auth.php';
requireAdminAuth();

$admin = getCurrentAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Eyesome Sports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f7f7f9; }
        .navbar-brand img { height: 30px; margin-right: 10px; }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-card.secondary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .stats-card.success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .stats-card.warning {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        .welcome-section {
            background: linear-gradient(45deg, #1e3c72, #2a5298);
            color: white;
            border-radius: 15px;
        }
    </style>
    <link rel="icon" href="../favicon.ico">
    <meta name="robots" content="noindex,nofollow">
    <script src="../js/config.js"></script>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main class="container py-5">
        <!-- Welcome Section -->
        <div class="welcome-section p-4 mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2">
                        <i class="fas fa-tachometer-alt me-2"></i>Welcome back, <?php echo htmlspecialchars($admin['full_name']); ?>!
                    </h2>
                    <p class="mb-0 opacity-75">
                        Last login: <?php echo date('F j, Y \a\t g:i A', $admin['login_time']); ?>
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-flex justify-content-md-end gap-2">
                        <a href="profile.php" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-user me-1"></i>Profile
                        </a>
                        <a href="logout.php" class="btn btn-outline-light btn-sm" onclick="return confirm('Are you sure you want to logout?')">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-box fa-3x mb-3 opacity-75"></i>
                        <h3 class="mb-1" id="totalProducts">-</h3>
                        <p class="mb-0">Total Products</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card secondary h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-tags fa-3x mb-3 opacity-75"></i>
                        <h3 class="mb-1" id="totalCategories">-</h3>
                        <p class="mb-0">Categories</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card success h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-shield-alt fa-3x mb-3 opacity-75"></i>
                        <h3 class="mb-1" id="totalClubs">-</h3>
                        <p class="mb-0">Clubs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card warning h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-shopping-cart fa-3x mb-3 opacity-75"></i>
                        <h3 class="mb-1" id="totalOrders">-</h3>
                        <p class="mb-0">Orders</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="add_product.php" class="btn btn-primary w-100">
                                    <i class="fas fa-plus me-2"></i>Add Product
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="add_category.php" class="btn btn-success w-100">
                                    <i class="fas fa-tag me-2"></i>Add Category
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="add_club.php" class="btn btn-info w-100">
                                    <i class="fas fa-shield-alt me-2"></i>Add Club
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="orders.php" class="btn btn-warning w-100">
                                    <i class="fas fa-shopping-cart me-2"></i>View Orders
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Products</h5>
                    </div>
                    <div class="card-body">
                        <div id="recentProducts">
                            <div class="text-center text-muted">
                                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                                <p>Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div id="recentActivity">
                            <div class="text-center text-muted">
                                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                                <p>Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load dashboard statistics
        async function loadDashboardStats() {
            try {
                // Load products count
                const productsRes = await fetch(CONFIG.getApiUrl(CONFIG.API_ENDPOINTS.PRODUCTS));
                const products = await productsRes.json();
                document.getElementById('totalProducts').textContent = products.length || 0;

                // Load categories count
                const categoriesRes = await fetch(CONFIG.getApiUrl(CONFIG.API_ENDPOINTS.CATEGORIES));
                const categories = await categoriesRes.json();
                document.getElementById('totalCategories').textContent = categories.length || 0;

                // Load clubs count
                const clubsRes = await fetch(CONFIG.getApiUrl(CONFIG.API_ENDPOINTS.CLUBS));
                const clubs = await clubsRes.json();
                document.getElementById('totalClubs').textContent = clubs.length || 0;

                // For now, set orders to 0 (will be implemented later)
                document.getElementById('totalOrders').textContent = '0';

                // Load recent products
                loadRecentProducts(products.slice(0, 5));
                
            } catch (error) {
                console.error('Failed to load dashboard stats:', error);
            }
        }

        function loadRecentProducts(products) {
            const container = document.getElementById('recentProducts');
            if (products.length === 0) {
                container.innerHTML = '<p class="text-muted text-center">No products found</p>';
                return;
            }

            container.innerHTML = products.map(product => `
                <div class="d-flex align-items-center mb-3">
                    <img src="../${product.primary_image || 'products/default.jpg'}" 
                         alt="${product.name}" 
                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; margin-right: 15px;">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${product.name}</h6>
                        <small class="text-muted">৳${product.price} • ${product.category_name}</small>
                    </div>
                    <span class="badge bg-${product.status === 'active' ? 'success' : 'secondary'}">${product.status}</span>
                </div>
            `).join('');
        }

        function loadRecentActivity() {
            const container = document.getElementById('recentActivity');
            // For now, show placeholder activity
            container.innerHTML = `
                <div class="timeline">
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-circle fa-2x text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Admin logged in</h6>
                            <small class="text-muted">Just now</small>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-box fa-2x text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">New product added</h6>
                            <small class="text-muted">2 hours ago</small>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-tag fa-2x text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Category updated</h6>
                            <small class="text-muted">1 day ago</small>
                        </div>
                    </div>
                </div>
            `;
        }

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardStats();
            loadRecentActivity();
        });
    </script>
</body>
</html>
