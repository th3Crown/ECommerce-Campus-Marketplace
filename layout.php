<?php
?>
<?php $current = basename($_SERVER['PHP_SELF']); ?>
<div class="floating-item"></div>
<div class="floating-item"></div>
<div class="floating-item"></div>
<div class="floating-item"></div>
<aside class="sidebar">
    <div class="sidebar-brand">Marketplace</div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-link <?php echo $current === 'dashboard.php' ? 'active' : '';?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="products.php" class="nav-link <?php echo $current === 'products.php' ? 'active' : '';?>"><i class="fas fa-store"></i> Products</a>
        <a href="add_listing.php" class="nav-link <?php echo $current === 'add_listing.php' ? 'active' : '';?>"><i class="fas fa-plus"></i> Add Listing</a>
        <a href="notifications.php" class="nav-link <?php echo $current === 'notifications.php' ? 'active' : '';?>"><i class="fas fa-bell"></i> Notifications</a>
        <a href="orders.php" class="nav-link <?php echo $current === 'orders.php' ? 'active' : '';?>"><i class="fas fa-box-open"></i> Orders</a>
        <a href="#" class="nav-link logout-link" onclick="fetch('logout.php',{method:'POST'}).then(()=>location.href='index.php')"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
    <div class="sidebar-footer">Signed in as <strong><?php echo isset($_SESSION['username'])?htmlspecialchars($_SESSION['username']):'Guest'; ?></strong></div>
</aside>
