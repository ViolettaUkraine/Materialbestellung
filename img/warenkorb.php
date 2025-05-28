<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_login();

$cart = $_SESSION['cart'] ?? [];
$materials = [];

if (!empty($cart)) {
    $placeholders = implode(',', array_fill(0, count($cart), '?'));
    $stmt = $db->prepare("SELECT * FROM materials WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($cart));
    $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$total = 0;
foreach ($materials as $m) {
    $total += $m['price'] * $cart[$m['id']];
}

// Bestellung absenden
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($cart)) {
    $userId = $_SESSION['user']['id'];

    foreach ($cart as $materialId => $quantity) {
        $db->prepare("INSERT INTO orders (user_id, material_id, quantity, status, created_at)
                      VALUES (?, ?, ?, 'offen', NOW())")
           ->execute([$userId, $materialId, $quantity]);

        $db->prepare("UPDATE materials SET stock = stock - ? WHERE id = ? AND stock >= ?")
           ->execute([$quantity, $materialId, $quantity]);
    }

    $_SESSION['cart'] = [];
    $successMessage = "✅ Bestellung wurde erfolgreich aufgegeben!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Warenkorb</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4">🛒 Dein Warenkorb</h1>

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success"><?= $successMessage ?></div>
        <a href="bestellen.php" class="btn btn-primary">↩️ Zurück zum Shop</a>
    <?php elseif (empty($cart)): ?>
        <div class="alert alert-info">⚠️ Dein Warenkorb ist leer.</div>
        <a href="bestellen.php" class="btn btn-secondary">➕ Produkte ansehen</a>
    <?php else: ?>
        <form method="post">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Material</th>
                        <th>Menge</th>
                        <th>Einzelpreis</th>
                        <th>Gesamt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($materials as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['name']) ?></td>
                            <td><?= $cart[$m['id']] ?></td>
                            <td><?= number_format($m['price'], 2, ',', '.') ?> €</td>
                            <td><?= number_format($m['price'] * $cart[$m['id']], 2, ',', '.') ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Gesamtsumme:</td>
                        <td class="fw-bold"><?= number_format($total, 2, ',', '.') ?> €</td>
                    </tr>
                </tfoot>
            </table>

            <button type="submit" class="btn btn-success">✅ Bestellung abschicken</button>
            <a href="bestellen.php" class="btn btn-secondary">↩️ Weiter einkaufen</a>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
