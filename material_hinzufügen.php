<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_role(['bearbeiter', 'admin']);

$error = '';
$success = isset($_GET['success']) ? "✅ Material erfolgreich hinzugefügt." : '';

// Material hinzufügen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);

    $image_url = '';

    // Bild hochladen
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'img/';
        $fileName = basename($_FILES['image']['name']);
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = uniqid('img_', true) . '.' . $ext;
        $targetPath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image_url = $targetPath;
        } else {
            $error = "❌ Fehler beim Hochladen des Bildes.";
        }
    }

    if ($name && $description && $price >= 0 && $stock >= 0 && !$error) {
        $stmt = $db->prepare("INSERT INTO materials (name, description, price, stock, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $stock, $image_url]);
        $success = "✅ Material erfolgreich hinzugefügt.";
    } else if (!$error) {
        $error = "⚠️ Bitte alle Felder korrekt ausfüllen.";
    }
}

// Materialien anzeigen
$stmt = $db->query("SELECT * FROM materials ORDER BY name");
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Material hinzufügen – BüroDirekt24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-img-top { height: 180px; object-fit: cover; }
        .sticky-header {
            position: fixed;
            top: 0; left: 0; width: 100%;
            z-index: 1030;
            background-color: #343a40;
            color: white;
            padding: 0.75rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-light" style="padding-top: 100px;">

<header class="sticky-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h4 mb-0 fw-bold">📦 <span style="color: #ffc107;">BFW</span>-Materialmanager</h1>
            <small class="d-block text-white fst-italic">Material hinzufügen & verwalten</small>
        </div>
        <form method="post" action="logout.php" class="m-0">
            <button type="submit" class="btn btn-outline-light btn-sm">🚪 Abmelden</button>
        </form>
    </div>
</header>

<div class="container py-4">
    <h2 class="mb-3">➕ Neues Material hinzufügen</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="row g-3 mb-5">
        <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Preis (€)</label>
            <input type="number" name="price" class="form-control" step="0.01" min="0" required>
        </div>
        <div class="col-md-12">
            <label class="form-label">Beschreibung</label>
            <textarea name="description" class="form-control" rows="2" required></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Lagerbestand</label>
            <input type="number" name="stock" class="form-control" min="0" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Bild (optional)</label>
            <input type="file" name="image" accept="image/*" class="form-control">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">➕ Hinzufügen</button>
        </div>
    </form>

    <h3 class="mb-3">📦 Bereits vorhandene Materialien</h3>
    <?php if (count($materials) === 0): ?>
        <div class="alert alert-info">Noch keine Materialien vorhanden.</div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($materials as $m): ?>
                <div class="col">
                    <div class="card h-100">
                        <?php if (!empty($m['image_url']) && file_exists($m['image_url'])): ?>
                            <img src="<?= htmlspecialchars($m['image_url']) ?>" class="card-img-top">
                        <?php else: ?>
                            <div class="bg-secondary text-white text-center py-5">Kein Bild</div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($m['name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($m['description']) ?></p>
                            <p class="fw-bold">💶 <?= number_format($m['price'], 2, ',', '.') ?> €</p>
                            <p class="text-muted">📦 Lager: <?= $m['stock'] ?></p>
                            
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>