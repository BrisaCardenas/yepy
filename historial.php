<?php
include 'db.php';
include 'sidebar.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
<div class="main-container">
    <div class="user-table">
        <h1>Historial</h1>
        <ul>
            <?php
            $sqlHistorial = "SELECT h.id_historial, h.tabla, h.accion, h.datos, h.fecha, u.Nombre AS Usuario
                            FROM historial h
                            JOIN usuario u ON h.usuario_Correo = u.Correo
                            ORDER BY h.fecha DESC";

            $resultHistorial = $conn->query($sqlHistorial);
            if ($resultHistorial && $resultHistorial->num_rows > 0) {
                while ($row = $resultHistorial->fetch_assoc()) {
                    echo "<li>
                            <strong>Tabla:</strong> " . htmlspecialchars($row['tabla']) . " - 
                            <strong>Acción:</strong> " . htmlspecialchars($row['accion']) . " - 
                            <strong>Datos:</strong> " . htmlspecialchars($row['datos']) . " - 
                            <strong>Usuario:</strong> " . htmlspecialchars($row['Usuario']) . " - 
                            <strong>Fecha:</strong> " . htmlspecialchars($row['fecha']) . "
                          </li>";
                }
            } else {
                echo "<li>No hay registros en el historial.</li>";
            }
            ?>
        </ul>
    </div>
</div>
</body>
</html>
