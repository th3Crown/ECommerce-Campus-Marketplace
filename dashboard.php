<?php  
session_start();  
  
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {  
    header('Location: index.php');  
    exit;  
}  
  
$username = $_SESSION['username'];  
$userId = $_SESSION['user_id'];  
  
require_once 'db.php';

$stmt = $pdo->prepare("SELECT * FROM products WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$userProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>  <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Marketplace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4ecdc4',
                        accent: '#44a08d'
                    }
                }
            }
        }
    </script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="assets/css/dash.css">
    <style>
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    </style>
</head>
<body class="dashboard-page">
<div class="theme-toggle">
    <button type="button" class="theme-btn" id="themeToggleBtn" onclick="toggleTheme()">
        <i class="fas fa-moon"></i>
    </button>
</div>

<div class="main-wrapper" id="mainContainer">
    <div class="dashboard-layout">
        <?php include __DIR__ . '/layout.php'; ?>

        <main class="main-content">
            <div class="topbar animate__animated animate__fadeInDown" data-aos="fade-down">
                <h2>Welcome back, <?php echo htmlspecialchars($username); ?>!</h2>
            </div>

            <section class="summary-cards">
                <div class="dashboard-content grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="dashboard-card bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 animate__animated animate__fadeInUp" data-aos="fade-up" data-aos-delay="100">
                        <h3><i class="fas fa-user text-primary"></i> Your Profile</h3>
                        <p>Manage your account settings, update your profile, and view your activity.</p>
                        <a href="profile.php" class="action-button bg-primary text-white px-4 py-2 rounded hover:bg-accent transition-colors">Edit Profile</a>
                    </div>

                    <div class="dashboard-card bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 animate__animated animate__fadeInUp" data-aos="fade-up" data-aos-delay="200">
                        <h3><i class="fas fa-shopping-cart text-primary"></i> Browse Products</h3>
                        <p>Explore and purchase items from our marketplace.</p>
                        <a href="products.php" class="action-button bg-primary text-white px-4 py-2 rounded hover:bg-accent transition-colors">Shop Now</a>
                    </div>

                    <div class="dashboard-card bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 animate__animated animate__fadeInUp" data-aos="fade-up" data-aos-delay="300">
                        <h3><i class="fas fa-bell text-primary"></i> Notifications</h3>
                        <p>Check your latest updates and messages.</p>
                        <a href="notifications.php" class="action-button bg-primary text-white px-4 py-2 rounded hover:bg-accent transition-colors">View Notifications</a>
                    </div>

                    <div class="dashboard-card bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 animate__animated animate__fadeInUp" data-aos="fade-up" data-aos-delay="400">
                        <h3><i class="fas fa-plus text-primary"></i> Sell Products</h3>
                        <p>List your items for sale and reach buyers.</p>
                        <a href="add_listing.php" class="action-button bg-primary text-white px-4 py-2 rounded hover:bg-accent transition-colors">Add Listing</a>
                    </div>
                </div>
            </section>

            <?php if (!empty($userProducts)): ?>
            <section class="product-section mt-8">
                <h3 class="section-title text-2xl font-semibold mb-4 animate__animated animate__fadeInLeft" data-aos="fade-left">Your Products</h3>
                <div class="product-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($userProducts as $product): ?>
                    <div class="product-card bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 animate__animated animate__zoomIn" data-aos="zoom-in" data-aos-delay="100">
                        <?php $imgUrl = !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'https://via.placeholder.com/400x300?text=No+Image'; ?>
                        <img src="<?php echo $imgUrl; ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="w-full h-48 object-cover rounded">
                        <div class="product-info mt-4">
                            <h4 class="text-lg font-medium"><?php echo htmlspecialchars($product['title']); ?></h4>
                            <p class="price text-primary font-bold">₱<?php echo htmlspecialchars($product['price']); ?></p>
                            <a href="javascript:void(0);" class="action-button bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors" onclick="if(confirm('Remove this product?')) { deleteProduct(<?php echo (int)$product['id']; ?>); }">Remove</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
        </main>
    </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
AOS.init();

function logout() {
    fetch('logout.php', {
        method: 'POST'
    })
    .then(() => {
        window.location.href = 'index.php';
    })
    .catch(error => {
        console.error('Logout error:', error);
        window.location.href = 'index.php';
    });
}

function toggleTheme() {
    const root = document.documentElement;
    const themeBtn = document.getElementById('themeToggleBtn');
    const icon = themeBtn ? themeBtn.querySelector('i') : null;

    if (root.hasAttribute('data-theme')) {
        root.removeAttribute('data-theme');
        localStorage.setItem('selectedTheme', 'light');
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
    } else {
        root.setAttribute('data-theme', 'dark');
        localStorage.setItem('selectedTheme', 'dark');
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
    }
}

window.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('selectedTheme');
    const themeBtn = document.getElementById('themeToggleBtn');
    const icon = themeBtn ? themeBtn.querySelector('i') : null;

    if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
    }
});

function viewProductDetails(id, title, price, imageUrl, description) {
    let modal = document.getElementById('productDetailModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'productDetailModal';
        modal.style.cssText = 'display: none; position: fixed; z-index: 1100; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7);';
        modal.onclick = function(e) { if (e.target === modal) closeProductModal(); };
        document.body.appendChild(modal);
    }

    const unitPrice = parseFloat(price) || 0;
    modal.innerHTML = `
        <div style="background: rgba(45,55,72,0.98); margin: 4% auto; padding: 20px; border-radius: 10px; width: 92%; max-width: 700px; color: #fff;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                <h2 style="margin:0;font-size:1.25rem;">${title}</h2>
                <button onclick="closeProductModal()" style="background:none;border:none;color:#fff;font-size:1.6rem;cursor:pointer;">&times;</button>
            </div>
            <div style="display:flex;gap:16px;flex-wrap:wrap;">
                <img src="${imageUrl}" alt="${title}" style="width:260px;height:180px;object-fit:cover;border-radius:8px;flex-shrink:0;">
                <div style="flex:1;min-width:200px;">
                    <p style="margin:0 0 8px 0;color:#cbd5e0;">${description}</p>
                    <p style="color:#4ecdc4;font-weight:700;font-size:1.1rem;margin-top:8px;">$${unitPrice.toFixed(2)}</p>
                    <div style="margin-top:14px;display:flex;gap:8px;">
                        <button class="action-button" onclick="buyProduct(${id}, ${JSON.stringify(title)})">Buy Now</button>
                        <button class="action-button" onclick="closeProductModal()" style="background:rgba(255,255,255,0.06);">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    modal.style.display = 'block';
}

function closeProductModal() {
    const modal = document.getElementById('productDetailModal');
    if (modal) modal.style.display = 'none';
}

function buyProduct(productId, productTitle) {
    const quantity = 1;
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch('place_order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✓ Order placed successfully!\n\nProduct: ' + data.product_title + '\nQuantity: ' + data.quantity + '\nTotal: $' + parseFloat(data.total_price).toFixed(2));
            const pdm = document.getElementById('productDetailModal');
            if (pdm) pdm.style.display = 'none';
            setTimeout(() => location.reload(), 800);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error placing order. Please try again.');
    });
}

function deleteProduct(productId) {
    console.log('Deleting product:', productId);
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'delete_product.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        console.log('Response:', xhr.responseText);
        try {
            const data = JSON.parse(xhr.responseText);
            if (data.success) {
                showNotification('✓ Product removed successfully', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Error: ' + (data.message || 'Could not remove product'), 'error');
            }
        } catch (e) {
            console.error('Parse error:', e);
            showNotification('Server error. Please try again.', 'error');
        }
    };
    
    xhr.onerror = function() {
        console.error('Request error');
        showNotification('Network error. Please try again.', 'error');
    };
    
    xhr.send('product_id=' + productId);
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        border-radius: 8px;
        font-weight: 600;
        z-index: 2000;
        animation: slideIn 0.4s ease-out;
        backdrop-filter: blur(8px);
    `;
    
    if (type === 'success') {
        notification.style.background = 'rgba(76, 205, 196, 0.95)';
        notification.style.color = '#052';
        notification.style.border = '1px solid #4ecdc4';
    } else {
        notification.style.background = 'rgba(244, 67, 54, 0.95)';
        notification.style.color = '#fff';
        notification.style.border = '1px solid #f44336';
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.4s ease-out';
        setTimeout(() => notification.remove(), 400);
    }, 3000);
}

</script>

</body>
</html>
