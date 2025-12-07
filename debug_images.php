<?php
require 'db.php';

$stmt = $pdo->query('SELECT id, title, image_url FROM products ORDER BY created_at DESC LIMIT 10');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
foreach ($rows as $r) {
    echo "ID: " . $r['id'] . ", Title: " . $r['title'] . ", Image: " . ($r['image_url'] ?: '(empty)') . "\n";
    if (!empty($r['image_url'])) {
        $fullPath = __DIR__ . '/' . $r['image_url'];
        $exists = file_exists($fullPath) ? 'EXISTS' : 'NOT FOUND';
        echo "  Full Path: " . $fullPath . " [$exists]\n";
    }
}
echo "</pre>";
?>
