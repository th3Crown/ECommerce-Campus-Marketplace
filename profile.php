<?php
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user = null;
try {
    $stmt = $pdo->prepare('SELECT id, username, email, created_at FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
     $userListings = [];
     $pstmt = $pdo->prepare('SELECT id, title, price, image_url FROM products WHERE user_id = ? ORDER BY created_at DESC');
     $pstmt->execute([$_SESSION['user_id']]);
     $userListings = $pstmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Profile fetch error: ' . $e->getMessage());
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Profile - Marketplace</title>
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
    <link rel="stylesheet" href="assets/css/dash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
    <style>

        .profile-wrapper .topbar h1 { font-family: 'Poppins', sans-serif; }
        .profile-grid .avatar { font-size: 1.15rem; }
        .mini-list img { border-radius: 6px; }
        .btn { display:inline-block;padding:8px 12px;border-radius:8px;background:linear-gradient(45deg,#4ecdc4,#44a08d);color:#052;text-decoration:none;font-weight:600 }
        .tiny-btn { font-size:0.9rem }
        @media (max-width: 800px) {
            .profile-grid { grid-template-columns:1fr !important; }
        }
    </style>
</head>
<body class="dashboard-page">

<div class="main-wrapper" id="mainContainer">
    <div class="animated-circle"></div>
    <div class="dashboard-layout">
        <?php include __DIR__ . '/layout.php'; ?>

        <main class="main-content">
            <div class="profile-wrapper" style="max-width:1100px;margin:0 auto;padding:18px;color:#fff;">
                <header class="topbar" style="margin-bottom:18px;">
                    <h1 style="margin:0;font-size:1.6rem;">Your profile</h1>
                    <div class="muted" style="color:rgba(255,255,255,0.6);">Manage your listings and contact details</div>
                </header>

                <section class="profile-grid" style="display:grid;grid-template-columns:280px 1fr;gap:20px;align-items:start;">
                    <div class="profile-card" style="background: rgba(255,255,255,0.03); padding:20px;border-radius:10px;">
                        <?php if ($user): ?>
                            <?php
                                $initials = '';
                                if (!empty($user['username'])) {
                                    $parts = preg_split('/\s+/', $user['username']);
                                    foreach ($parts as $p) { if ($p !== '') $initials .= strtoupper($p[0]); }
                                    $initials = substr($initials, 0, 2);
                                }
                            ?>
                            <div class="avatar" style="width:72px;height:72px;border-radius:10px;background:linear-gradient(135deg,#4ecdc4,#44a08d);display:flex;align-items:center;justify-content:center;font-weight:700;color:#052;box-shadow:0 6px 18px rgba(0,0,0,0.3);"><?php echo $initials ?: 'U'; ?></div>
                            <h3 style="margin-top:12px;margin-bottom:6px;"><?php echo htmlspecialchars($user['username']); ?></h3>
                            <p class="muted" style="color:rgba(255,255,255,0.6);margin:0 0 8px 0;">Member</p>
                            <p style="margin:6px 0 0 0;color:rgba(255,255,255,0.9);">Email: <?php echo htmlspecialchars($user['email']); ?></p>
                            <p style="margin:6px 0 0 0;color:rgba(255,255,255,0.7);font-size:0.9rem;">Member since: <?php echo date('M j, Y', strtotime($user['created_at'])); ?></p>
                            <div style="margin-top:12px;"><a href="profile_edit.php" class="btn action-button">Edit profile</a></div>
                        <?php else: ?>
                            <p>No user data found.</p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <div class="card" style="background: rgba(255,255,255,0.03); padding:18px;border-radius:10px;margin-bottom:16px;">
                            <h3 style="margin:0 0 12px 0;">My Listings</h3>
                            <?php if (!empty($userListings)): ?>
                                <ul class="mini-list" style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px;">
                                    <?php foreach ($userListings as $item): ?>
                                        <?php $thumb = !empty($item['image_url']) ? htmlspecialchars($item['image_url']) : 'https://via.placeholder.com/96x72?text=No+Image'; ?>
                                        <li style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                                            <div style="display:flex;align-items:center;gap:12px;">
                                                <img src="<?php echo $thumb; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="width:96px;height:72px;object-fit:cover;border-radius:6px;">
                                                <div>
                                                    <div style="font-weight:600"><?php echo htmlspecialchars($item['title']); ?></div>
                                                    <div style="color:rgba(255,255,255,0.7);font-size:0.95rem;">₱<?php echo number_format((float)$item['price'],2); ?></div>
                                                </div>
                                            </div>
                                            <div>
                                                <a class="tiny-btn" href="edit_listing.php?id=<?php echo (int)$item['id']; ?>" style="background:transparent;border:1px solid rgba(255,255,255,0.08);padding:6px 8px;border-radius:6px;color:#fff;text-decoration:none;">Edit</a>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="muted">You have no listings yet. <a href="add_listing.php">Add one</a>.</p>
                            <?php endif; ?>
                        </div>

                        <div class="card" style="background: rgba(255,255,255,0.03); padding:18px;border-radius:10px;">
                            <h3 style="margin:0 0 12px 0;">Ratings</h3>
                            <p class="muted" style="color:rgba(255,255,255,0.7);">⭐️⭐️⭐️⭐️☆ (4.2) — 15 reviews</p>
                        </div>
                    </div>
                </section>

                <footer class="footer" style="margin-top:18px;color:rgba(255,255,255,0.6);">
                    <small>Update your contact details so buyers can reach you.</small>
                </footer>
            </div>
        </main>
    </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="script.js"></script>
<script>
    if (typeof AOS !== 'undefined') AOS.init();
</script>

</body>
</html>
