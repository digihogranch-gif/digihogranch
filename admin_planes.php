<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - DigiHog Ranch</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
:root{--g900:#123f2d;--g800:#19543a;--g700:#236846;--g600:#2d7a52;--g100:#e8f3ed;--bg:#f4f8f5;--text:#183126;--muted:#6b7c74;--border:#dfe9e3}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--text);font-family:'Inter',sans-serif}
.container{width:min(1200px,calc(100% - 40px));margin:0 auto}
.main-header{background:#fff!important;border-bottom:1px solid #e7eee9!important;box-shadow:none!important}
.header-wrap{min-height:112px;display:flex;align-items:center;justify-content:space-between;gap:20px}
.logo-area{display:flex;align-items:center;gap:15px}
.logo-icon{width:68px;height:68px;display:flex;align-items:center;justify-content:center;border-radius:17px;background:var(--g100);color:var(--g700);font-size:29px}
.logo-text h1{margin:0;color:var(--g900)!important;background:none!important;-webkit-text-fill-color:var(--g900)!important;font-family:'Plus Jakarta Sans';font-size:1.55rem}
.logo-status{margin:5px 0 0;color:#62746b!important;font-size:.78rem;font-weight:600}
.pulse-dot{display:inline-block;width:8px;height:8px;border-radius:50%;background:#49b97b!important;box-shadow:0 0 0 5px rgba(73,185,123,.14)!important;margin-right:7px}
.btn-logout{display:inline-flex;align-items:center;gap:7px;background:#e45f63;color:#fff;text-decoration:none;padding:10px 15px;border-radius:9px;font-weight:700;font-size:.84rem}
.main-nav{background:var(--g800)!important;border:0!important}
.nav-links{min-height:58px;display:flex;align-items:center;gap:5px;flex-wrap:wrap}
.nav-item{display:inline-flex!important;align-items:center;gap:7px;color:#fff!important;text-decoration:none!important;padding:11px 14px!important;border-radius:8px!important;font-size:.84rem;font-weight:600}
.nav-item:hover{background:rgba(255,255,255,.11)!important}
.main-content{padding-top:30px;padding-bottom:45px}
.welcome-banner{position:relative;overflow:hidden;margin:0 0 25px;padding:30px 32px;border-radius:20px;background:linear-gradient(135deg,var(--g900),var(--g600))!important;color:#fff;box-shadow:0 16px 36px rgba(18,63,45,.14)}
.welcome-banner:after{content:"";position:absolute;width:210px;height:210px;border-radius:50%;right:-55px;top:-85px;border:34px solid rgba(255,255,255,.06)}
.banner-tag{display:inline-flex!important;align-items:center;gap:7px;padding:6px 11px!important;border-radius:999px!important;border:1px solid rgba(255,255,255,.28)!important;background:rgba(255,255,255,.09)!important;color:#fff!important;font-size:.72rem!important;font-weight:800!important;text-transform:uppercase}
.welcome-banner h2{margin:12px 0 7px;color:#fff!important;font-family:'Plus Jakarta Sans';font-size:1.8rem}
.welcome-banner p{margin:0;max-width:720px;color:rgba(255,255,255,.86)!important;line-height:1.6}
.section-heading{margin:0 0 17px}.section-heading h2{margin:0;color:var(--g900);font-family:'Plus Jakarta Sans';font-size:1.2rem}.section-heading p{margin:5px 0 0;color:var(--muted);font-size:.86rem}
.admin-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;margin:0}
.admin-card{position:relative;padding:25px;background:#fff;border-radius:17px;border:1px solid var(--border);text-align:left;box-shadow:0 8px 25px rgba(18,63,45,.05);transition:.2s}
.admin-card:hover{transform:translateY(-3px);border-color:#b9d4c4;box-shadow:0 14px 32px rgba(18,63,45,.09)}
.admin-card .card-icon{width:50px;height:50px;display:flex;align-items:center;justify-content:center;border-radius:13px;background:var(--g100);color:var(--g700);font-size:22px;margin-bottom:17px}
.admin-card h3{margin:0 0 8px;color:var(--g900);font-family:'Plus Jakarta Sans';font-size:1.05rem}
.admin-card p{margin:0;color:var(--muted);line-height:1.55;font-size:.86rem;min-height:43px}
.btn-admin{display:inline-flex;align-items:center;gap:7px;padding:10px 15px;background:var(--g700);color:#fff;text-decoration:none;border-radius:9px;font-weight:700;margin-top:18px;font-size:.82rem}
.btn-admin:hover{background:var(--g800)}
.btn-disabled{background:#87968f}.btn-disabled:hover{background:#788780}
.main-footer{background:#fff!important;border-top:1px solid var(--border);color:#718078!important;padding:22px 0!important;text-align:center;font-size:.8rem}
@media(max-width:760px){.header-wrap{min-height:auto;padding-top:20px;padding-bottom:20px;align-items:flex-start;flex-direction:column}.nav-links{padding:8px 0}.nav-item{flex:1 1 45%;justify-content:center}.admin-grid{grid-template-columns:1fr}.welcome-banner{padding:24px 20px}}
@media(max-width:480px){.container{width:min(100% - 28px,1200px)}.nav-item{flex-basis:100%}.logo-icon{width:58px;height:58px}}

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
                <div class="logo-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <div class="logo-text">
                    <h1>DigiHog Admin</h1>
                    <p class="logo-status"><span class="pulse-dot"></span> Administrador: <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>
                </div>
            </div>

            <a href="logout.php" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
            </a>
        </div>
    </header>

    <nav class="main-nav">
        <div class="container">
            <div class="nav-links">
                <a href="gestionar_usuarios.php" class="nav-item"><i class="fa-solid fa-users"></i> Gestión Usuarios</a>
                <a href="gestionar_proveedores.php" class="nav-item"><i class="fa-solid fa-truck-field"></i> Gest. Proveedores</a>
                <a href="admin_marketplace.php" class="nav-item"><i class="fa-solid fa-store"></i> Marketplace</a>
                <a href="gestionar_planes.php" class="nav-item"><i class="fa-solid fa-layer-group"></i> Gestión de Planes</a>
            </div>
        </div>
    </nav>

    <main class="container main-content">
        <section class="welcome-banner">
            <span class="banner-tag"><i class="fa-solid fa-shield-halved"></i> Consola de Administración</span>
            <h2>Control Centralizado</h2>
            <p>Administra usuarios, proveedores, marketplace y configuraciones generales desde el panel maestro de DigiHog Ranch.</p>
        </section>

        <div class="section-heading">
            <h2>Herramientas Administrativas</h2>
            <p>Selecciona el módulo que deseas gestionar.</p>
        </div>

        <section class="admin-grid">
            <article class="admin-card">
                <div class="card-icon"><i class="fa-solid fa-users-gear"></i></div>
                <h3>Gestión de Usuarios</h3>
                <p>Administra las cuentas de granjas activas y los permisos del sistema.</p>
                <a href="gestionar_usuarios.php" class="btn-admin"><i class="fa-solid fa-arrow-right"></i> Gestionar</a>
            </article>

            <article class="admin-card">
                <div class="card-icon"><i class="fa-solid fa-handshake"></i></div>
                <h3>Gestión de Proveedores</h3>
                <p>Administra proveedores y la asignación de planes Básico o Premium.</p>
                <a href="gestionar_proveedores.php" class="btn-admin"><i class="fa-solid fa-arrow-right"></i> Gestionar Planes</a>
            </article>

            <article class="admin-card">
                <div class="card-icon"><i class="fa-solid fa-store"></i></div>
                <h3>Marketplace</h3>
                <p>Gestiona el catálogo de insumos y servicios disponibles para los granjeros.</p>
                <a href="admin_marketplace.php" class="btn-admin"><i class="fa-solid fa-arrow-right"></i> Administrar</a>
            </article>

            <article class="admin-card">
                <div class="card-icon"><i class="fa-solid fa-database"></i></div>
                <h3>Mantenimiento BD</h3>
                <p>Acceso al módulo destinado a optimizaciones y respaldos de seguridad.</p>
                <a href="#" class="btn-admin btn-disabled"><i class="fa-solid fa-screwdriver-wrench"></i> Ejecutar</a>
            </article>
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