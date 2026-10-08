<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$pdo = new PDO("mysql:host=localhost;dbname=digihog_ranch;charset=utf8mb4", 'root', '');

// Lógica para guardar nuevo producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_producto'])) {
    $nombre_img = 'default.jpg';
    if (!empty($_FILES['imagen']['name'])) {
        $nombre_img = time() . '_' . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], 'uploads/' . $nombre_img);
    }

    // CORRECCIÓN: Ajustamos el INSERT para guardar id_proveedor en lugar de texto redundante.
    // Nota: Necesitarás tener un campo hidden en el formulario o un select si quieres elegir el proveedor.
    // He ajustado la consulta. Asegúrate de que el ID sea correcto.
    $stmt = $pdo->prepare("INSERT INTO marketplace (titulo, descripcion, precio, categoria, stock, id_proveedor, imagen) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['titulo'], $_POST['descripcion'], $_POST['precio'], $_POST['categoria'], $_POST['stock'], 1, $nombre_img]); // Asumimos ID 1 por defecto
    $mensaje = "Producto añadido correctamente.";
}

// Lógica para eliminar producto
if (isset($_GET['eliminar'])) {
    $stmt = $pdo->prepare("DELETE FROM marketplace WHERE id = ?");
    $stmt->execute([$_GET['eliminar']]);
    header('Location: admin_marketplace.php');
    exit();
}

// CORRECCIÓN: Usamos JOIN para traer el nombre del proveedor desde la tabla normalizada
$productos = $pdo->query("SELECT m.*, p.empresa AS nombre_proveedor 
                          FROM marketplace m 
                          LEFT JOIN proveedores p ON m.id_proveedor = p.id 
                          ORDER BY m.creado_en DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace Admin | DigiHog Ranch</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
:root{--g900:#123f2d;--g800:#19543a;--g700:#236846;--g600:#2d7a52;--g100:#e8f3ed;--bg:#f4f8f5;--text:#183126;--muted:#6b7c74;--border:#dfe9e3;--danger:#d9535f}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--text);font-family:'Inter',sans-serif}
.container{width:min(1200px,calc(100% - 40px));margin:0 auto}
.main-header{background:#fff!important;border-bottom:1px solid #e7eee9!important;box-shadow:none!important}
.header-wrap{min-height:112px;display:flex;align-items:center;justify-content:space-between;gap:20px}
.logo-area{display:flex;align-items:center;gap:15px}
.logo-icon{width:68px;height:68px;display:flex;align-items:center;justify-content:center;border-radius:17px;background:var(--g100)!important;color:var(--g700)!important;font-size:28px}
.logo-text h1{margin:0;color:var(--g900)!important;background:none!important;-webkit-text-fill-color:var(--g900)!important;font-family:'Plus Jakarta Sans';font-size:1.55rem}
.logo-status{margin:5px 0 0;color:#62746b!important;font-size:.78rem;font-weight:600}
.pulse-dot{display:inline-block;width:8px;height:8px;border-radius:50%;background:#49b97b;box-shadow:0 0 0 5px rgba(73,185,123,.14);margin-right:7px}
.btn-logout{display:inline-flex;align-items:center;gap:7px;background:#e45f63;color:#fff;text-decoration:none;padding:10px 15px;border-radius:9px;font-weight:700;font-size:.84rem}
.main-nav{background:var(--g800)!important;border:0!important}
.nav-links{min-height:58px;display:flex;align-items:center;gap:5px;flex-wrap:wrap}
.nav-item{display:inline-flex!important;align-items:center;gap:7px;color:#fff!important;text-decoration:none!important;padding:11px 14px!important;border-radius:8px!important;font-size:.84rem;font-weight:600}
.nav-item:hover,.nav-item.active{background:rgba(255,255,255,.13)!important}
.main-content{padding-top:30px;padding-bottom:45px}
.hero{position:relative;overflow:hidden;padding:29px 32px;margin-bottom:24px;border-radius:20px;background:linear-gradient(135deg,var(--g900),var(--g600));color:#fff;box-shadow:0 16px 36px rgba(18,63,45,.14)}
.hero:after{content:"";position:absolute;width:210px;height:210px;border-radius:50%;right:-55px;top:-85px;border:34px solid rgba(255,255,255,.06)}
.hero-tag{display:inline-flex;align-items:center;gap:7px;padding:6px 11px;border-radius:999px;border:1px solid rgba(255,255,255,.28);background:rgba(255,255,255,.09);font-size:.72rem;font-weight:800;text-transform:uppercase}
.hero h2{margin:12px 0 7px;color:#fff;font-family:'Plus Jakarta Sans';font-size:1.8rem}.hero p{margin:0;color:rgba(255,255,255,.86)}
.alert-success{display:flex;align-items:center;gap:9px;background:#edf8f1;color:var(--g700);border:1px solid #cfe8d8;padding:14px 16px;border-radius:11px;margin-bottom:20px;font-weight:700}
.panel{background:#fff;border:1px solid var(--border);border-radius:17px;box-shadow:0 8px 25px rgba(18,63,45,.05);overflow:hidden;margin-bottom:22px}
.panel-head{padding:21px 24px;border-bottom:1px solid var(--border)}
.panel-head h3{margin:0;color:var(--g900);font-family:'Plus Jakarta Sans';font-size:1.08rem}.panel-head p{margin:5px 0 0;color:var(--muted);font-size:.81rem}
.form-body{padding:23px 24px}
.form-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:16px;margin:0}
.form-group{margin-bottom:17px}.form-group.full{grid-column:1/-1}
.form-group label{display:block;margin-bottom:7px;color:#40564b;font-size:.78rem;font-weight:700}
input,select,textarea{width:100%;padding:11px 12px;border:1px solid #cbdcd2;border-radius:9px;background:#fbfdfc;font-family:'Inter';color:var(--text);outline:none}
input,select{height:45px}textarea{resize:vertical;min-height:90px}
input:focus,select:focus,textarea:focus{border-color:var(--g600);background:#fff;box-shadow:0 0 0 3px rgba(45,122,82,.1)}
.file-input{height:auto!important;padding:9px!important}
.form-actions{display:flex;justify-content:flex-end}
.btn-add{display:inline-flex;align-items:center;gap:7px;background:var(--g700);color:#fff;border:0;padding:12px 18px;border-radius:9px;cursor:pointer;font-weight:700}.btn-add:hover{background:var(--g800)}
.inventory-summary{display:flex;justify-content:space-between;align-items:center;gap:15px}
.count-badge{display:inline-flex;align-items:center;gap:7px;background:var(--g100);color:var(--g700);padding:7px 11px;border-radius:999px;font-size:.78rem;font-weight:700}
.table-wrap{overflow-x:auto}
.product-table{width:100%;border-collapse:collapse}
.product-table th{padding:14px 15px;text-align:left;background:#edf5f0;color:var(--g800);border-bottom:1px solid #dce9e1;font-size:.73rem;text-transform:uppercase;letter-spacing:.03em}
.product-table td{padding:14px 15px;border-bottom:1px solid #edf2ef;color:#455a50;vertical-align:middle}.product-table tbody tr:last-child td{border-bottom:0}.product-table tbody tr:hover{background:#fbfdfc}
.product-info{display:flex;align-items:center;gap:12px;min-width:245px}.product-image{width:58px;height:58px;object-fit:contain;border:1px solid var(--border);background:#fff;border-radius:10px;padding:3px}.product-name{display:block;color:var(--g900);font-weight:700;margin-bottom:4px}.stock{font-size:.75rem;color:var(--muted)}
.category-badge{display:inline-flex;padding:6px 9px;border-radius:999px;background:#f0f5f2;color:#587064;font-size:.76rem;font-weight:700}
.provider-cell{font-weight:600;color:#3e554a}.price{font-weight:800;color:var(--g700)!important;white-space:nowrap}
.delete-btn{display:inline-flex;width:34px;height:34px;align-items:center;justify-content:center;border-radius:8px;background:#fff0f0;color:var(--danger);text-decoration:none}.delete-btn:hover{background:#fbe1e2}
.empty-state{text-align:center;padding:42px 20px;color:var(--muted)}.empty-state i{font-size:34px;color:#a8beb1;margin-bottom:10px}
@media(max-width:900px){.form-grid{grid-template-columns:1fr 1fr}}
@media(max-width:760px){.header-wrap{min-height:auto;padding-top:20px;padding-bottom:20px;align-items:flex-start;flex-direction:column}.hero{padding:24px 20px}.inventory-summary{align-items:flex-start;flex-direction:column}}
@media(max-width:520px){.container{width:min(100% - 28px,1200px)}.nav-item{flex:1 1 100%;justify-content:center}.form-grid{grid-template-columns:1fr}.form-group.full{grid-column:auto}.form-actions .btn-add{width:100%;justify-content:center}}

/* ==========================
   FOOTER DIGIHOG RANCH
========================== */
.dhr-site-footer{width:100%;margin-top:auto;background:#123f2d;color:#dce9e1;border-top:1px solid rgba(255,255,255,.08);padding:28px 0 17px;font-family:'Inter',sans-serif}
.dhr-site-footer *{box-sizing:border-box}
.dhr-site-footer .dhr-footer-inner{width:100%;max-width:1240px;margin:0 auto;padding:0 24px}
.dhr-site-footer .dhr-footer-top{display:grid;grid-template-columns:minmax(0,1.6fr) minmax(220px,.7fr);gap:38px;align-items:start;padding-bottom:22px}
.dhr-site-footer .dhr-footer-brand{display:flex;align-items:flex-start;gap:14px}
.dhr-site-footer .dhr-footer-logo{width:54px!important;height:54px!important;min-width:54px!important;max-width:54px!important;flex:0 0 54px!important;padding:6px!important;margin:0!important;border-radius:14px!important;background:#fff!important;overflow:hidden!important;display:flex!important;align-items:center!important;justify-content:center!important}
.dhr-site-footer .dhr-footer-logo img{width:42px!important;height:42px!important;min-width:0!important;max-width:42px!important;max-height:42px!important;object-fit:contain!important;display:block!important;margin:0!important;padding:0!important;position:static!important;transform:none!important}
.dhr-site-footer .dhr-footer-brand h3{margin:2px 0 6px!important;color:#fff!important;font-family:'Plus Jakarta Sans',sans-serif;font-size:18px!important}
.dhr-site-footer .dhr-footer-brand p{margin:0!important;max-width:590px;color:#bfd1c7!important;font-size:13px!important;line-height:1.6!important}
.dhr-site-footer .dhr-footer-links h4{margin:2px 0 11px!important;color:#fff!important;font-size:13px!important}
.dhr-site-footer .dhr-footer-links nav{display:flex;flex-wrap:wrap;gap:9px 16px}
.dhr-site-footer .dhr-footer-links a{color:#dce9e1!important;text-decoration:none!important;font-size:13px!important;font-weight:600!important;transition:color .2s ease,transform .2s ease}
.dhr-site-footer .dhr-footer-links a:hover{color:#fff!important;transform:translateY(-1px)}
.dhr-site-footer .dhr-footer-bottom{border-top:1px solid rgba(255,255,255,.10);padding-top:15px;display:flex;justify-content:space-between;align-items:center;gap:16px;color:#afc5b9;font-size:12px}
.dhr-site-footer .dhr-footer-bottom strong{color:#e8f3ed}
.dhr-site-footer .dhr-footer-system{display:inline-flex;align-items:center;gap:7px}
.dhr-site-footer .dhr-footer-system i{color:#79c99a}
@media(max-width:760px){.dhr-site-footer .dhr-footer-top{grid-template-columns:1fr;gap:22px}.dhr-site-footer .dhr-footer-bottom{flex-direction:column;align-items:flex-start}}

</style>
</head>
<body>
    <header class="main-header">
        <div class="container header-wrap">
            <div class="logo-area">
                <div class="logo-icon"><i class="fa-solid fa-store"></i></div>
                <div class="logo-text">
                    <h1>Marketplace Admin</h1>
                    <p class="logo-status"><span class="pulse-dot"></span> Panel Administrativo DigiHog Ranch</p>
                </div>
            </div>
            <a href="logout.php" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
        </div>
    </header>

    <nav class="main-nav">
        <div class="container">
            <div class="nav-links">
                <a href="admin_planes.php" class="nav-item"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                <a href="gestionar_usuarios.php" class="nav-item"><i class="fa-solid fa-users"></i> Gestión Usuarios</a>
                <a href="gestionar_proveedores.php" class="nav-item"><i class="fa-solid fa-truck-field"></i> Gest. Proveedores</a>
                <a href="admin_marketplace.php" class="nav-item active"><i class="fa-solid fa-store"></i> Marketplace</a>
            </div>
        </div>
    </nav>

    <main class="container main-content">
        <section class="hero">
            <span class="hero-tag"><i class="fa-solid fa-store"></i> Administración</span>
            <h2>Gestión del Marketplace</h2>
            <p>Administra los productos disponibles y consulta de forma ordenada su proveedor, categoría, existencia y precio.</p>
        </section>

        <?php if(isset($mensaje)): ?>
            <div class="alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <section class="panel">
            <div class="panel-head">
                <h3><i class="fa-solid fa-circle-plus"></i> Nuevo Producto</h3>
                <p>Registra un nuevo producto dentro del catálogo general del marketplace.</p>
            </div>

            <div class="form-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="titulo">Nombre del producto</label>
                            <input type="text" id="titulo" name="titulo" placeholder="Ej. Alimento concentrado" required>
                        </div>

                        <div class="form-group">
                            <label for="categoria">Categoría</label>
                            <select id="categoria" name="categoria">
                                <option value="Alimento">Alimento</option>
                                <option value="Medicamento">Medicamento</option>
                                <option value="Equipo">Equipo</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="precio">Precio (C$)</label>
                            <input type="number" id="precio" step="0.01" name="precio" placeholder="0.00" required>
                        </div>

                        <div class="form-group">
                            <label for="stock">Stock</label>
                            <input type="number" id="stock" name="stock" placeholder="Cantidad" required>
                        </div>

                        <div class="form-group full">
                            <label for="imagen">Imagen del producto</label>
                            <input type="file" id="imagen" name="imagen" accept="image/*" class="file-input">
                        </div>

                        <div class="form-group full">
                            <label for="descripcion">Descripción</label>
                            <textarea id="descripcion" name="descripcion" placeholder="Descripción breve del producto..." rows="3"></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="guardar_producto" class="btn-add">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Publicar en Marketplace
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <section class="panel">
            <div class="panel-head inventory-summary">
                <div>
                    <h3><i class="fa-solid fa-box-open"></i> Inventario Actual</h3>
                    <p>Los productos se muestran del más reciente al más antiguo.</p>
                </div>
                <span class="count-badge">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <?php echo count($productos); ?> productos
                </span>
            </div>

            <?php if (!empty($productos)): ?>
                <div class="table-wrap">
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Proveedor</th>
                                <th>Categoría</th>
                                <th>Stock</th>
                                <th>Precio</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($productos as $p): ?>
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            <img src="uploads/<?php echo htmlspecialchars($p['imagen']); ?>" class="product-image" alt="<?php echo htmlspecialchars($p['titulo']); ?>">
                                            <div>
                                                <span class="product-name"><?php echo htmlspecialchars($p['titulo']); ?></span>
                                                <span class="stock">ID #<?php echo (int)$p['id']; ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="provider-cell">
                                        <i class="fa-solid fa-building"></i>
                                        <?php echo htmlspecialchars($p['nombre_proveedor'] ?? 'N/A'); ?>
                                    </td>
                                    <td>
                                        <span class="category-badge"><?php echo htmlspecialchars($p['categoria']); ?></span>
                                    </td>
                                    <td>
                                        <strong><?php echo (int)$p['stock']; ?></strong> uds.
                                    </td>
                                    <td class="price">C$ <?php echo number_format($p['precio'], 2); ?></td>
                                    <td>
                                        <a href="?eliminar=<?php echo $p['id']; ?>" class="delete-btn" title="Eliminar producto" onclick="return confirm('¿Eliminar producto?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa-solid fa-box-open"></i>
                    <p>No hay productos registrados en el marketplace.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>

<footer class="dhr-site-footer">
    <div class="dhr-footer-inner">
        <div class="dhr-footer-top">
            <div class="dhr-footer-brand">
                <div class="dhr-footer-logo">
                    <img src="logo/logo.svg" alt="DigiHog Ranch">
                </div>
                <div>
                    <h3>DigiHog Ranch</h3>
                    <p>Sistema de gestión para el control y seguimiento de la producción porcina, integrando producción, nutrición, sanidad, inventario y gestión de la granja.</p>
                </div>
            </div>
            <div class="dhr-footer-links">
                <h4>Accesos rápidos</h4>
                <nav aria-label="Enlaces del pie de página">
                    <a href="index.php">Inicio</a>
                    <a href="bodega.php">Bodega</a>
                    <a href="reportes.php">Reportes</a>
                    <a href="acerca.php">Acerca de</a>
                </nav>
            </div>
        </div>
        <div class="dhr-footer-bottom">
            <div>&copy; <?php echo date('Y'); ?> <strong>DigiHog Ranch</strong> · Todos los derechos reservados.</div>
            <div class="dhr-footer-system"><i class="fa-solid fa-piggy-bank"></i><span>Gestión inteligente para la producción porcina</span></div>
        </div>
    </div>
</footer>

</body>
</html>