<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header('Location: login.php'); exit; }
require_once 'config/conexion.php';

$idUsuario = (int)$_SESSION['usuario_id'];
$idGranja = (int)($_SESSION['granja_id'] ?? 0);
$mensaje = '';
$tipoMensaje = '';

if ($idGranja <= 0) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare("SELECT g.id, g.nombre_granja, g.ubicacion, g.creado_en, g.id_plan,
                             p.nombre AS plan_nombre, p.descripcion AS plan_descripcion,
                             p.limite_cerdos, p.nivel_acceso
                      FROM granjas g
                      LEFT JOIN planes p ON p.id = g.id_plan
                      WHERE g.id = ? AND g.id_usuario = ? LIMIT 1");
$stmt->execute([$idGranja, $idUsuario]);
$granja = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$granja) { http_response_code(403); exit('No tienes acceso a la granja seleccionada.'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_granja'] ?? '');
    $ubicacion = trim($_POST['ubicacion'] ?? '');
    if ($nombre === '') {
        $mensaje = 'El nombre de la granja es obligatorio.'; $tipoMensaje = 'error';
    } elseif (mb_strlen($nombre) > 100) {
        $mensaje = 'El nombre no puede superar los 100 caracteres.'; $tipoMensaje = 'error';
    } elseif (mb_strlen($ubicacion) > 255) {
        $mensaje = 'La ubicación no puede superar los 255 caracteres.'; $tipoMensaje = 'error';
    } else {
        $up = $pdo->prepare("UPDATE granjas SET nombre_granja = ?, ubicacion = ? WHERE id = ? AND id_usuario = ?");
        $up->execute([$nombre, $ubicacion !== '' ? $ubicacion : null, $idGranja, $idUsuario]);
        $granja['nombre_granja'] = $nombre;
        $granja['ubicacion'] = $ubicacion;
        $_SESSION['granja_nombre'] = $nombre;
        $mensaje = 'Información de la granja actualizada correctamente.'; $tipoMensaje = 'ok';
    }
}

function totalGranja(PDO $pdo, string $tabla, int $idGranja): int {
    $permitidas = ['cerdos','lotes','inventario'];
    if (!in_array($tabla, $permitidas, true)) return 0;
    $q = $pdo->prepare("SELECT COUNT(*) FROM {$tabla} WHERE id_granja = ?");
    $q->execute([$idGranja]);
    return (int)$q->fetchColumn();
}
$totalCerdos = totalGranja($pdo,'cerdos',$idGranja);
$totalLotes = totalGranja($pdo,'lotes',$idGranja);
$totalInventario = totalGranja($pdo,'inventario',$idGranja);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Administrar Granja | DigiHog Ranch</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="css/style.css">
<style>
:root{--g900:#123f2d;--g800:#19543a;--g700:#236846;--g600:#2d7a52;--g100:#e8f3ed;--bg:#f4f8f5;--text:#183126;--muted:#6b7c74;--border:#dfe9e3;--danger:#dc4c5a}
body{margin:0;background:var(--bg);color:var(--text);font-family:'Inter',sans-serif;display:flex;min-height:100vh;flex-direction:column}.main-header{background:#fff!important;border-bottom:1px solid #e7eee9!important}.header-wrap{min-height:122px;display:flex;align-items:center;justify-content:space-between}.brand{display:flex;align-items:center;gap:14px}.brand img{width:70px;height:70px;object-fit:contain}.brand h1{font:800 1.65rem 'Plus Jakarta Sans';color:var(--g900);margin:0}.brand p{margin:5px 0 0;color:#5f6f68;font-size:.84rem}.dot{display:inline-block;width:8px;height:8px;border-radius:50%;background:#49b97b;margin-right:7px;box-shadow:0 0 0 5px rgba(73,185,123,.14)}
.page{width:min(1180px,calc(100% - 40px));margin:30px auto 48px}.hero{position:relative;overflow:hidden;padding:29px 32px;border-radius:20px;color:#fff;background:linear-gradient(135deg,var(--g900),var(--g600));box-shadow:0 16px 36px rgba(18,63,45,.15);margin-bottom:22px}.hero:after{content:"";position:absolute;width:210px;height:210px;border-radius:50%;right:-55px;top:-85px;border:32px solid rgba(255,255,255,.06)}.tag{display:inline-flex;gap:7px;align-items:center;padding:6px 11px;border:1px solid rgba(255,255,255,.28);border-radius:999px;background:rgba(255,255,255,.09);font-size:.72rem;font-weight:800;text-transform:uppercase}.hero h2{font:800 1.85rem 'Plus Jakarta Sans';margin:12px 0 6px}.hero p{margin:0;opacity:.9}.active-pill{margin-top:15px;display:inline-flex;gap:8px;align-items:center;background:#fff;color:var(--g900);padding:8px 12px;border-radius:10px;font-weight:800;font-size:.82rem}
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px}.stat{background:#fff;border:1px solid var(--border);border-radius:15px;padding:18px;display:flex;gap:13px;align-items:center;box-shadow:0 7px 20px rgba(18,63,45,.04)}.stat i{width:43px;height:43px;display:grid;place-items:center;border-radius:11px;background:var(--g100);color:var(--g700);font-size:1.05rem}.stat small{display:block;color:var(--muted);font-weight:700}.stat strong{display:block;color:var(--g900);font:800 1.25rem 'Plus Jakarta Sans';margin-top:2px}.grid{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(280px,.7fr);gap:20px}.card{background:#fff;border:1px solid var(--border);border-radius:18px;padding:25px;box-shadow:0 8px 25px rgba(18,63,45,.05)}.card h3{font:800 1.1rem 'Plus Jakarta Sans';color:var(--g900);margin:0 0 6px}.sub{color:var(--muted);font-size:.86rem;margin:0 0 22px}.field{margin-bottom:18px}.field label{display:block;font-size:.8rem;font-weight:800;color:var(--g900);margin-bottom:7px}.field input,.field textarea{width:100%;box-sizing:border-box;border:1px solid #ceddd4;border-radius:10px;padding:12px 13px;font:inherit;color:var(--text);background:#fff;outline:none;transition:.2s}.field textarea{min-height:105px;resize:vertical}.field input:focus,.field textarea:focus{border-color:var(--g600);box-shadow:0 0 0 3px rgba(45,122,82,.12)}.hint{font-size:.74rem;color:var(--muted);margin-top:6px}.actions{display:flex;gap:10px;justify-content:flex-end}.btn{border:0;border-radius:10px;padding:11px 17px;font-weight:800;text-decoration:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:.2s}.btn-primary{background:var(--g700);color:#fff}.btn-primary:hover{background:var(--g800);transform:translateY(-2px);box-shadow:0 7px 16px rgba(18,63,45,.18)}.btn-secondary{background:#edf4f0;color:var(--g800)}.btn-secondary:hover{background:#e2eee7;transform:translateY(-2px)}.alert{padding:12px 14px;border-radius:10px;margin-bottom:18px;font-weight:700;font-size:.84rem}.alert.ok{background:#e7f6ed;color:#1f6b43;border:1px solid #c9e9d5}.alert.error{background:#fff0f1;color:#a93440;border:1px solid #f0cbd0}.info-row{padding:14px 0;border-bottom:1px solid #edf2ef}.info-row:last-child{border-bottom:0}.info-row span{display:block;color:var(--muted);font-size:.73rem;font-weight:800;text-transform:uppercase;margin-bottom:5px}.info-row strong{color:var(--g900);font-size:.9rem}.lock{display:inline-flex;gap:6px;align-items:center;color:#788981;font-size:.74rem;margin-top:5px}.notice{margin-top:17px;padding:13px;border-radius:11px;background:#f5faf7;border:1px solid #dfece4;color:#587066;font-size:.78rem;line-height:1.5}
.dhr-site-footer{width:100%;margin-top:auto;background:#123f2d;color:#dce9e1;border-top:1px solid rgba(255,255,255,.08);padding:28px 0 17px;font-family:'Inter',sans-serif}.dhr-site-footer *{box-sizing:border-box}.dhr-footer-inner{max-width:1240px;margin:auto;padding:0 24px}.dhr-footer-top{display:grid;grid-template-columns:1.6fr .7fr;gap:38px;padding-bottom:22px}.dhr-footer-brand{display:flex;gap:14px}.dhr-footer-logo{width:54px!important;height:54px!important;flex:0 0 54px!important;padding:6px!important;border-radius:14px;background:#fff;display:flex;align-items:center;justify-content:center}.dhr-footer-logo img{width:42px!important;height:42px!important;object-fit:contain!important}.dhr-footer-brand h3{margin:2px 0 6px;color:#fff;font:800 18px 'Plus Jakarta Sans'}.dhr-footer-brand p{margin:0;max-width:590px;color:#bfd1c7;font-size:13px;line-height:1.6}.dhr-footer-links h4{margin:2px 0 11px;color:#fff;font-size:13px}.dhr-footer-links nav{display:flex;flex-wrap:wrap;gap:9px 16px}.dhr-footer-links a{color:#dce9e1;text-decoration:none;font-size:13px;font-weight:600}.dhr-footer-bottom{border-top:1px solid rgba(255,255,255,.10);padding-top:15px;display:flex;justify-content:space-between;gap:16px;color:#afc5b9;font-size:12px}.dhr-footer-bottom strong{color:#e8f3ed}.dhr-footer-system{display:inline-flex;gap:7px;align-items:center}.dhr-footer-system i{color:#79c99a}
@media(max-width:850px){.stats{grid-template-columns:repeat(2,1fr)}.grid{grid-template-columns:1fr}}@media(max-width:600px){.page{width:min(100% - 24px,1180px)}.hero{padding:23px 20px}.stats{grid-template-columns:1fr}.header-wrap{padding:0 16px}.dhr-footer-top{grid-template-columns:1fr}.dhr-footer-bottom{flex-direction:column}}
</style>
</head>
<body>
<header class="main-header"><div class="container header-wrap"><div class="brand"><img src="logo/logo.svg" alt="DigiHog Ranch"><div><h1>DigiHog Ranch</h1><p><span class="dot"></span>Granja activa: <?= htmlspecialchars($granja['nombre_granja']) ?></p></div></div></div></header>
<?php include 'menu.php'; ?>
<main class="page">
<section class="hero"><span class="tag"><i class="fa-solid fa-gear"></i> Administración</span><h2>Administración de la granja</h2><p>Consulta y actualiza la información general de la granja actualmente seleccionada.</p><div class="active-pill"><i class="fa-solid fa-circle-check"></i> Granja activa: <?= htmlspecialchars($granja['nombre_granja']) ?></div></section>
<section class="stats">
<div class="stat"><i class="fa-solid fa-piggy-bank"></i><div><small>Cerdos registrados</small><strong><?= $totalCerdos ?></strong></div></div>
<div class="stat"><i class="fa-solid fa-layer-group"></i><div><small>Lotes</small><strong><?= $totalLotes ?></strong></div></div>
<div class="stat"><i class="fa-solid fa-boxes-stacked"></i><div><small>Insumos en bodega</small><strong><?= $totalInventario ?></strong></div></div>
<div class="stat"><i class="fa-solid fa-gem"></i><div><small>Plan actual</small><strong style="font-size:1rem"><?= htmlspecialchars($granja['plan_nombre'] ?? 'Sin plan') ?></strong></div></div>
</section>
<div class="grid">
<section class="card"><h3><i class="fa-solid fa-pen-to-square"></i> Información general</h3><p class="sub">Los cambios se aplicarán únicamente a la granja activa.</p>
<?php if ($mensaje): ?><div class="alert <?= $tipoMensaje ?>"><?= htmlspecialchars($mensaje) ?></div><?php endif; ?>
<form method="POST" action="">
<div class="field"><label for="nombre_granja">Nombre de la granja *</label><input id="nombre_granja" name="nombre_granja" maxlength="100" required value="<?= htmlspecialchars($granja['nombre_granja']) ?>"><div class="hint">Nombre con el que identificarás esta granja dentro de DigiHog Ranch.</div></div>
<div class="field"><label for="ubicacion">Ubicación</label><textarea id="ubicacion" name="ubicacion" maxlength="255" placeholder="Ej. Diriamba, Carazo, Nicaragua"><?= htmlspecialchars($granja['ubicacion'] ?? '') ?></textarea><div class="hint">Puedes indicar municipio, departamento y una referencia de ubicación.</div></div>
<div class="actions"><a class="btn btn-secondary" href="index.php"><i class="fa-solid fa-arrow-left"></i> Volver</a><button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Guardar cambios</button></div>
</form></section>
<aside class="card"><h3><i class="fa-solid fa-circle-info"></i> Datos de la granja</h3><p class="sub">Información administrativa vinculada al registro.</p>
<div class="info-row"><span>ID de granja</span><strong>#<?= (int)$granja['id'] ?></strong></div>
<div class="info-row"><span>Fecha de registro</span><strong><?= date('d/m/Y', strtotime($granja['creado_en'])) ?></strong></div>
<div class="info-row"><span>Plan contratado</span><strong><?= htmlspecialchars($granja['plan_nombre'] ?? 'Sin plan') ?></strong><div class="lock"><i class="fa-solid fa-lock"></i> Administrado por el sistema de suscripción</div></div>
<div class="info-row"><span>Nivel de acceso</span><strong>Nivel <?= (int)($granja['nivel_acceso'] ?? 0) ?></strong></div>
<div class="info-row"><span>Límite de cerdos</span><strong><?= isset($granja['limite_cerdos']) ? number_format((int)$granja['limite_cerdos']) : 'No definido' ?></strong></div>
<div class="notice"><i class="fa-solid fa-shield-halved"></i> Por seguridad, esta página solo permite actualizar una granja que pertenezca al usuario autenticado y coincida con la granja activa de la sesión.</div>
</aside></div>
</main>
<footer class="dhr-site-footer"><div class="dhr-footer-inner"><div class="dhr-footer-top"><div class="dhr-footer-brand"><div class="dhr-footer-logo"><img src="logo/logo.svg" alt="DigiHog Ranch"></div><div><h3>DigiHog Ranch</h3><p>Sistema de gestión para el control y seguimiento de la producción porcina, integrando producción, nutrición, sanidad, inventario y gestión de la granja.</p></div></div><div class="dhr-footer-links"><h4>Accesos rápidos</h4><nav><a href="index.php">Inicio</a><a href="bodega.php">Bodega</a><a href="reportes.php">Reportes</a><a href="acerca.php">Acerca de</a></nav></div></div><div class="dhr-footer-bottom"><div>&copy; <?= date('Y') ?> <strong>DigiHog Ranch</strong> · Todos los derechos reservados.</div><div class="dhr-footer-system"><i class="fa-solid fa-piggy-bank"></i><span>Gestión inteligente para la producción porcina</span></div></div></div></footer>
</body></html>
