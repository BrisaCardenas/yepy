<?php
include 'db.php'; 
include 'sidebar.php'; 

session_start(); // Asegúrate de que esta línea esté presente

$message = "";

$resultUsuarios = $conn->query("SELECT Correo, Nombre FROM usuario ORDER BY Nombre ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['usuario_Correo']) && isset($_POST['equipo_id_Equipo'])) {
        $usuario_Correo = $_POST['usuario_Correo'];
        $equipo_id_Equipo = $_POST['equipo_id_Equipo'];
        $fecha_entrega = date('Y-m-d H:i:s');

        $stmtCheck = $conn->prepare("SELECT * FROM asignaciones WHERE equipo_id_Equipo = ?");
        $stmtCheck->bind_param("i", $equipo_id_Equipo);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            $message = "Error: El equipo ya está asignado a otro usuario.";
        } else {
            if (!empty($usuario_Correo) && !empty($equipo_id_Equipo)) {
                $stmt = $conn->prepare("INSERT INTO asignaciones (usuario_Correo, equipo_id_Equipo, Fecha_entrega) VALUES (?, ?, ?)");
                $stmt->bind_param("sis", $usuario_Correo, $equipo_id_Equipo, $fecha_entrega);

                if ($stmt->execute()) {
                    $message = "Asignación creada correctamente.";
                } else {
                    $message = "Error al crear la asignación: " . $stmt->error;
                } 

                $stmt->close();
            } else {
                $message = "Por favor, complete todos los campos.";
            }
        }

        $stmtCheck->close();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['terminate_id'])) {
            $terminate_id = $_POST['terminate_id'];
            $fecha_termino = date('Y-m-d H:i:s');
    
         
            $usuario_Correo = $_SESSION['email'];
    
            if (empty($usuario_Correo)) {
                $message = "Error: El usuario no está definido.";
            } else {
                $stmtUpdate = $conn->prepare("UPDATE asignaciones SET Fecha_termino = ? WHERE id_Asignaciones = ?");
                $stmtUpdate->bind_param("si", $fecha_termino, $terminate_id);
    
                if ($stmtUpdate->execute()) {
                    $message = "Asignación terminada correctamente.";
                    
                } else {
                    $message = "Error al terminar la asignación: " . $stmtUpdate->error;
                }
    
                $stmtUpdate->close();
            }
        }
    }
    
}

$resultEquipos = $conn->query("SELECT id_Equipo, Nombre FROM equipo WHERE Estado IN ('nuevo', 'Buen estado') AND id_Equipo NOT IN (SELECT equipo_id_Equipo FROM asignaciones) ORDER BY Nombre ASC"); 
$resultUsuarios = $conn->query("SELECT Correo, Nombre FROM usuario ORDER BY Nombre ASC"); 
$resultAsignaciones = $conn->query("SELECT a.id_Asignaciones, u.Nombre AS Usuario, e.Nombre AS Equipo, a.Fecha_entrega 
                                      FROM asignaciones a 
                                      JOIN usuario u ON a.usuario_Correo = u.Correo 
                                      JOIN equipo e ON a.equipo_id_Equipo = e.id_Equipo 
                                      ORDER BY a.Fecha_entrega DESC"); 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignaciones</title>
    <link rel="stylesheet" href="style2.css"> 
</head>
<body>
<div class="main-container">
    <div class="edit-form">
        <?php if (!empty($message)): ?>
            <div class="alert"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <form action="" method="POST"> 
            <label for="usuario_Correo">Correo del Usuario:</label>
            <select id="usuario_Correo" name="usuario_Correo" required>
                <option value="">Seleccione un usuario</option>
                <?php while ($row = $resultUsuarios->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['Correo']); ?>"><?php echo htmlspecialchars($row['Nombre']) . " (" . htmlspecialchars($row['Correo']) . ")"; ?></option>
                <?php endwhile; ?>
            </select>
            
            <label for="equipo_id_Equipo">Equipo:</label>
            <select id="equipo_id_Equipo" name="equipo_id_Equipo" required>
                <option value="">Seleccione un equipo</option>
                <?php while ($row = $resultEquipos->fetch_assoc()): ?>
                    <option value="<?php echo $row['id_Equipo']; ?>"><?php echo htmlspecialchars($row['Nombre']); ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn-update">Agregar Asignación</button>
        </form>
        
    </div>
    
    <div class="user-table">
        <h1>Asignaciones</h1>
        <ul>
            <?php
            if ($resultAsignaciones && $resultAsignaciones->num_rows > 0) {
                while ($row = $resultAsignaciones->fetch_assoc()) {
                    echo "<li>
                            <strong>" . htmlspecialchars($row['Usuario']) . "</strong> - " . htmlspecialchars($row['Equipo']) . " - " . htmlspecialchars($row['Fecha_entrega']) . "
                            <form action='' method='POST' style='display:inline;'>
                                <input type='hidden' name='terminate_id' value='" . $row['id_Asignaciones'] . "'>
                                <button type='submit' onclick='return confirm(\"¿Estás seguro de que deseas terminar esta asignación?\");' class='btn-delete'>Terminar Asignación</button>
                            </form>
                          </li>";
                }
            } else {
                echo "<li>No hay asignaciones registradas.</li>";
            }
            ?>
        </ul>
    </div>
</div>
</body>
</html>