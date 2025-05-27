<?php
session_start(); // Wichtig für Warenkorb
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_login();

// Suche
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

// Bestellung speichern & Warenkorb aktualisieren
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user']['id'];
    if (!empty($_POST['product_ids'])) {
        foreach ($_POST['product_ids'] as $materialId) {
            $quantity = (int) ($_POST['quantities'][$materialId] ?? 0);
            if ($quantity > 0) {
                // Datenbank-Bestellung
                $db->prepare("INSERT INTO orders (user_id, material_id, quantity, status, created_at)
                              VALUES (?, ?, ?, 'offen', NOW())")
                   ->execute([$userId, $materialId, $quantity]);

                $db->prepare("UPDATE materials SET stock = stock - ? WHERE id = ? AND stock >= ?")
                   ->execute([$quantity, $materialId, $quantity]);

                // In den Session-Warenkorb legen
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }
                if (isset($_SESSION['cart'][$materialId])) {
                    $_SESSION['cart'][$materialId] += $quantity;
                } else {
                    $_SESSION['cart'][$materialId] = $quantity;
                }
            }
        }
        $successMessage = "✅ Bestellung erfolgreich gespeichert & zum Warenkorb hinzugefügt!";
    } else {
        $errorMessage = "⚠️ Keine Produkte ausgewählt.";
    }
}
?>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Materialshop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-img-top { height: 180px; object-fit: cover; }
        .warenkorb { position: sticky; top: 20px; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="mb-4">📦 Büro-Materialshop</h1>

    <!-- Suche -->
    <form method="get" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="🔍 Suche nach Name oder Beschreibung" value="<?= htmlspecialchars($searchTerm) ?>">
            <button class="btn btn-outline-secondary" type="submit">Suchen</button>
        </div>
    </form>

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success"><?= $successMessage ?></div>
    <?php elseif (!empty($errorMessage)): ?>
        <div class="alert alert-warning"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="post" id="bestellForm">
        <div class="row">
            <div class="col-md-9">
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php if ($materials): ?>
                        <?php foreach ($materials as $m): ?>
                            <div class="col">
                                <div class="card h-100">
                                    <?php if (!empty($m['image_url'])): ?>
                                        <img src="<?= htmlspecialchars($m['image_url']) ?>" class="card-img-top">
                                    <?php else: ?>
                                        <div class="bg-secondary text-white text-center py-5">Kein Bild</div>
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($m['name']) ?></h5>
                                        <p class="card-text"><?= htmlspecialchars($m['description']) ?></p>
                                        <p class="fw-bold">💶 <?= number_format($m['price'], 2, ',', '.') ?> €</p>
                                        <p class="text-muted">📦 Verfügbar: <?= $m['stock'] ?></p>
                                        <div class="form-check">
                                            <input class="form-check-input product-check" type="checkbox" name="product_ids[]" value="<?= $m['id'] ?>" data-price="<?= $m['price'] ?>" data-name="<?= htmlspecialchars($m['name']) ?>" id="check_<?= $m['id'] ?>">
                                            <label class="form-check-label" for="check_<?= $m['id'] ?>">Bestellen</label>
                                        </div>
                                        <input type="number" name="quantities[<?= $m['id'] ?>]" class="form-control mt-2 quantity-input" min="1" max="<?= $m['stock'] ?>" value="1" data-id="<?= $m['id'] ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info">Keine Materialien gefunden für „<?= htmlspecialchars($searchTerm) ?>“.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 🛒 Warenkorb -->
            <div class="col-md-3 warenkorb">
                <div class="card">
                    <a href="warenkorb.php" class="card-header bg-primary text-white text-decoration-none d-block">🛒 Warenkorb anzeigen</a>

                    <ul class="list-group list-group-flush" id="cartItems"></ul>
                    <div class="card-body">
                        <p class="fw-bold">Gesamt: <span id="cartTotal">0,00 €</span></p>
                        <button type="submit" class="btn btn-success w-100">✅ Bestellung absenden</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');

    function updateCart() {
        cartItems.innerHTML = '';
        let total = 0;

        document.querySelectorAll('.product-check').forEach(check => {
            const id = check.value;
            const name = check.dataset.name;
            const price = parseFloat(check.dataset.price);
            const quantityInput = document.querySelector(`.quantity-input[data-id="${id}"]`);
            const quantity = parseInt(quantityInput.value);

            if (check.checked && quantity > 0) {
                const subtotal = price * quantity;
                total += subtotal;
                const li = document.createElement('li');
                li.className = 'list-group-item';
                li.textContent = `${name} × ${quantity} = ${subtotal.toFixed(2).replace('.', ',')} €`;
                cartItems.appendChild(li);
            }
        });

        cartTotal.textContent = total.toFixed(2).replace('.', ',') + ' €';
    }

    document.querySelectorAll('.product-check, .quantity-input').forEach(el => {
        el.addEventListener('change', updateCart);
    });

    updateCart(); // Initial beim Laden
</script>

</body>
</html>
