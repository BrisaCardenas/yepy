<?php
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];

   
    $stmtUser  = $conn->prepare("SELECT Correo FROM usuario WHERE Correo = ?");
    $stmtUser ->bind_param("s", $correo);
    $stmtUser ->execute();
    $resultUser  = $stmtUser ->get_result();

    if ($resultUser ->num_rows > 0) {
        
        $stmtDeleteAsignaciones = $conn->prepare("DELETE FROM asignaciones WHERE usuario_Correo = ?");
        $stmtDeleteAsignaciones->bind_param("s", $correo);
        $stmtDeleteAsignaciones->execute();
        $stmtDeleteAsignaciones->close();


        $stmtDeleteIncidentes = $conn->prepare("DELETE FROM incidentes WHERE usuario_Correo = ?");
        $stmtDeleteIncidentes->bind_param("s", $correo);
        $stmtDeleteIncidentes->execute();
        $stmtDeleteIncidentes->close();

        
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