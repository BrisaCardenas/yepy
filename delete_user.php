<?php
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];

    // Obtener el ID del usuario a eliminar
    $stmtUser  = $conn->prepare("SELECT Correo FROM usuario WHERE Correo = ?");
    $stmtUser ->bind_param("s", $correo);
    $stmtUser ->execute();
    $resultUser  = $stmtUser ->get_result();

    if ($resultUser ->num_rows > 0) {
        // Eliminar las asignaciones del usuario
        $stmtDeleteAsignaciones = $conn->prepare("DELETE FROM asignaciones WHERE usuario_Correo = ?");
        $stmtDeleteAsignaciones->bind_param("s", $correo);
        $stmtDeleteAsignaciones->execute();
        $stmtDeleteAsignaciones->close();

        // Eliminar los incidentes del usuario
        $stmtDeleteIncidentes = $conn->prepare("DELETE FROM incidentes WHERE usuario_Correo = ?");
        $stmtDeleteIncidentes->bind_param("s", $correo);
        $stmtDeleteIncidentes->execute();
        $stmtDeleteIncidentes->close();

        // Eliminar el usuario
        $sql = "DELETE FROM usuario WHERE Correo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $correo);

        if ($stmt->execute()) {
            echo "Usuario eliminado correctamente.";
        } else {
            echo "Error al eliminar el usuario: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Usuario no encontrado.";
    }

    $stmtUser ->close();
    $conn->close();

    header("Location: usuarios.php");
    exit();
}
?>