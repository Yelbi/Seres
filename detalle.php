<?php
// detalle.php
require 'config.php';

// Obtener el slug del ser
$slug = $_GET['ser'] ?? '';

if (empty($slug)) {
    header('Location: /galeria.php');
    exit;
}

try {
    // Obtener información básica del ser
    $stmt = $pdo->prepare("SELECT * FROM seres WHERE slug = ?");
    $stmt->execute([$slug]);
    $ser = $stmt->fetch();
    
    if (!$ser) {
        header('Location: /galeria.php');
        exit;
    }
    
    // Obtener información detallada
    $stmt = $pdo->prepare("SELECT * FROM seres_detalle WHERE ser_id = ?");
    $stmt->execute([$ser['id']]);
    $detalle = $stmt->fetch();
    
    // Obtener imágenes adicionales
    $stmt = $pdo->prepare("SELECT * FROM seres_imagenes WHERE ser_id = ? ");
    $stmt->execute([$ser['id']]);
    $imagenes = $stmt->fetchAll();
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($ser['nombre']) ?> - Seres Místicos</title>
    <link rel="stylesheet" href="/styles/detalle.css">
    <link rel="stylesheet" href="/styles/header.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="shortcut icon" href="/Img/logo.png" />
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.3.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Meta tags para SEO -->
    <meta name="description" content="Información completa sobre <?= htmlspecialchars($ser['nombre']) ?>, <?= htmlspecialchars($ser['tipo']) ?> de <?= htmlspecialchars($ser['region']) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($ser['nombre']) ?> - Seres Místicos">
    <meta property="og:description" content="Descubre todo sobre <?= htmlspecialchars($ser['nombre']) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($ser['imagen']) ?>">
</head>
<body>

    <!-- Header -->
    <header class="header">
        <a href="/index.php" class="logo">
            <img src="/Img/logo.png" alt="">
        </a>
        <nav class="nav-menu">
            <a href="/index.php" class="nav-link">Inicio</a>
            <a href="/galeria.php" class="nav-link">Galería</a>
            <a href="#" class="nav-link">Contacto</a>
        </nav>
        <a href="#" class="user-btn"><i class="fi fi-rr-user"></i></a>
    </header>

    <!-- Breadcrumb -->
    <nav class="breadcrumb">
    <a href="/index.php">Inicio</a>
    <span class="separator">></span>
    <a href="/galeria.php">Galería</a>
    <span class="separator">></span>
    <span class="current"><?= htmlspecialchars($ser['nombre']) ?></span>
</nav>

<main class="detail-container">
    <section class="hero-section">
        <div class="hero-content">
            <div class="hero-image">
                <img src="<?= htmlspecialchars($ser['imagen']) ?>" alt="<?= htmlspecialchars($ser['nombre']) ?>" class="main-image">
                <div class="image-overlay"></div>
            </div>
            <div class="hero-info">
                <h1 class="ser-title"><?= htmlspecialchars($ser['nombre']) ?></h1>
                <div class="basic-info">
                    <div class="info-item">
                        <span class="label">Tipo:</span>
                        <span class="value tipo"><?= htmlspecialchars($ser['tipo']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Región:</span>
                        <span class="value region"><?= htmlspecialchars($ser['region']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if ($detalle): ?>
    <section class="content-section">
        <div class="content-grid">
            <?php if (!empty($detalle['descripcion'])): ?>
            <div class="content-card description-card">
                <h2 class="section-title">Descripción</h2>
                <div class="content-text"><?= nl2br(htmlspecialchars($detalle['descripcion'])) ?></div>
            </div>
            <?php endif; ?>

            <?php if (!empty($detalle['etimologia'])): ?>
            <div class="content-card etymology-card">
                <h2 class="section-title">Etimología</h2>
                <div class="content-text"><?= nl2br(htmlspecialchars($detalle['etimologia'])) ?></div>
            </div>
            <?php endif; ?>

            <?php if (!empty($detalle['historia'])): ?>
            <div class="content-card history-card">
                <h2 class="section-title">Historia</h2>
                <div class="content-text"><?= nl2br(htmlspecialchars($detalle['historia'])) ?></div>
            </div>
            <?php endif; ?>

            <?php if (!empty($detalle['caracteristicas'])): ?>
            <div class="content-card characteristics-card">
                <h2 class="section-title">Características</h2>
                <div class="content-text"><?= nl2br(htmlspecialchars($detalle['caracteristicas'])) ?></div>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($imagenes)): ?>
    <section class="gallery-section">
        <h2 class="section-title">Galería de Imágenes</h2>
        <div class="image-gallery">
            <?php foreach ($imagenes as $img): ?>
            <div class="gallery-item">
                <img src="<?= htmlspecialchars($img['imagen_url']) ?>" alt="<?= htmlspecialchars($ser['nombre']) ?>" loading="lazy" onclick="openModal(this)">
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <section class="navigation-section">
        <div class="nav-buttons">
            <a href="/galeria.php" class="btn-back">Volver a la Galería</a>
            <?php
            $prev = $pdo->prepare("SELECT slug, nombre FROM seres WHERE id < ? ORDER BY id DESC LIMIT 1");
            $prev->execute([$ser['id']]);
            $prevItem = $prev->fetch();
            $next = $pdo->prepare("SELECT slug, nombre FROM seres WHERE id > ? ORDER BY id ASC LIMIT 1");
            $next->execute([$ser['id']]);
            $nextItem = $next->fetch();
            ?>
            <div class="nav-arrows">
                <?php if ($prevItem): ?>
                <a href="/detalle.php?ser=<?= urlencode($prevItem['slug']) ?>" class="btn-nav prev">&larr; <?= htmlspecialchars($prevItem['nombre']) ?></a>
                <?php endif; ?>
                <?php if ($nextItem): ?>
                <a href="/detalle.php?ser=<?= urlencode($nextItem['slug']) ?>" class="btn-nav next"><?= htmlspecialchars($nextItem['nombre']) ?> &rarr;</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

</main>

    <!-- Modal para imágenes -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <img id="modalImage" src="" alt="">
        </div>
    </div>

    <script src="/JS/detalle.js"></script>

</body>
</html>