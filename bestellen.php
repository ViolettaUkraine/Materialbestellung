<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_login();

// Suche verarbeiten
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT * FROM materials";
$params = [];

if ($searchTerm !== '') {
    $sql .= " WHERE name LIKE :term OR description LIKE :term";
    $params['term'] = "%$searchTerm%";
}
$sql .= " ORDER BY name";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Bestellung verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user']['id'];
    if (!empty($_POST['product_ids'])) {
        foreach ($_POST['product_ids'] as $materialId) {
            $quantity = (int) ($_POST['quantities'][$materialId] ?? 0);
            if ($quantity > 0) {
                $db->prepare("INSERT INTO orders (user_id, material_id, quantity, status, created_at)
                              VALUES (?, ?, ?, 'offen', NOW())")
                   ->execute([$userId, $materialId, $quantity]);

                $db->prepare("UPDATE materials SET stock = stock - ? WHERE id = ? AND stock >= ?")
                   ->execute([$quantity, $materialId, $quantity]);
            }
        }
        echo "<div class='alert alert-success'>✅ Bestellung erfolgreich gespeichert!</div>";
    } else {
        echo "<div class='alert alert-warning'>⚠️ Keine Produkte ausgewählt.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Materialshop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4">📦 Büro-Materialshop</h2>

    <!-- 🔍 Suchzeile -->
    <form method="get" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Suche nach Name oder Beschreibung" value="<?= htmlspecialchars($searchTerm) ?>">
            <button class="btn btn-outline-secondary" type="submit">Suchen</button>
        </div>
    </form>

    <form method="post">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php if (count($materials) > 0): ?>
                <?php foreach ($materials as $m): ?>
                    <div class="col">
                        <div class="card h-100">
                            <?php if (!empty($m['image_url'])): ?>
                                <img src="<?= htmlspecialchars($m['image_url']) ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-secondary text-white text-center py-5">Kein Bild</div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($m['name']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($m['description']) ?></p>
                                <p class="card-text fw-bold">💶 <?= number_format($m['price'], 2, ',', '.') ?> €</p>
                                <p class="card-text text-muted">📦 Verfügbar: <?= $m['stock'] ?></p>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="product_ids[]" value="<?= $m['id'] ?>" id="check_<?= $m['id'] ?>">
                                    <label class="form-check-label" for="check_<?= $m['id'] ?>">Bestellen</label>
                                </div>
                                <input type="number" name="quantities[<?= $m['id'] ?>]" class="form-control" min="1" max="<?= $m['stock'] ?>" value="1">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col">
                    <div class="alert alert-info">🔎 Keine Materialien gefunden für „<strong><?= htmlspecialchars($searchTerm) ?></strong>“.</div>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">🛒 Bestellung absenden</button>
        </div>
    </form>
</div>

</body>
</html>
