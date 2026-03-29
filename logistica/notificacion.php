<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    exit(); // Si no hay usuario logueado, no mostramos nada
}

$idUsuario = $_SESSION['id_usuario'];

// Consultar cantidad de notificaciones no leídas
$sql = "SELECT COUNT(*) AS cantidad FROM notificaciones WHERE id_usuario = $idUsuario AND leida = 0";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);
$cantNuevas = $row['cantidad'];

// Consultar las últimas 5 notificaciones
$sqlLista = "SELECT mensaje, fecha, leida FROM notificaciones 
             WHERE id_usuario = $idUsuario 
             ORDER BY fecha DESC 
             LIMIT 5";
$resLista = mysqli_query($conn, $sqlLista);
$notificaciones = mysqli_fetch_all($resLista, MYSQLI_ASSOC);
?>

<!-- 🔔 Ícono de notificación -->
<div class="notificacion-wrapper">
    <div class="icono-campana <?= $cantNuevas > 0 ? 'activa' : '' ?>" onclick="toggleMenu()">
        🔔
        <?php if ($cantNuevas > 0): ?>
            <span class="badge"><?= $cantNuevas ?></span>
        <?php endif; ?>
    </div>

    <div class="menu-notificaciones" id="menuNotificaciones">
        <h4>Notificaciones</h4>
        <ul>
            <?php if (count($notificaciones) === 0): ?>
                <li class="sin-notificaciones">No hay notificaciones</li>
            <?php else: ?>
                <?php foreach ($notificaciones as $notif): ?>
                    <li class="<?= $notif['leida'] == 0 ? 'no-leida' : '' ?>">
                        <?= htmlspecialchars($notif['mensaje']) ?><br>
                        <small><?= date('d/m/Y H:i', strtotime($notif['fecha'])) ?></small>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <form method="POST" action="marcar_leidas.php">
            <button type="submit" class="marcar-leidas">Marcar como leídas</button>
        </form>
    </div>
</div>



<!-- 🔄 Script -->
<script>
function toggleMenu() {
    const menu = document.getElementById('menuNotificaciones');
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}
</script>
