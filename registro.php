<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $nocontrol = trim($_POST['nocontrol']);
    $codigoRFID = trim($_POST['rfid']);

    if ($nombre && $apellido && $correo && $telefono && $nocontrol && $codigoRFID) {

        // Verificar si el código RFID existe
        $stmt = $conexion->prepare("SELECT idTarjeta FROM tarjeta_rfid WHERE codigo = ?");
        $stmt->bind_param("s", $codigoRFID);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            $idTarjeta = $fila['idTarjeta'];

            $nombreCompleto = $nombre . " " . $apellido;
            $stmtCliente = $conexion->prepare("INSERT INTO cliente (nombreCliente, correo, telefono, NoControl, idTarjeta) VALUES (?, ?, ?, ?, ?)");
            $stmtCliente->bind_param("ssssi", $nombreCompleto, $correo, $telefono, $nocontrol, $idTarjeta);

            if ($stmtCliente->execute()) {
                echo "<script>alert('Registro exitoso'); window.location.href = 'index.html';</script>";
            } else {
                echo "<script>alert('Error al registrar al cliente');</script>";
            }

            $stmtCliente->close();
        } else {
            echo "<script>alert('Tarjeta no reconocida. Por favor, utiliza una tarjeta registrada.');window.location.href = 'formulario.html';</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Por favor completa todos los campos.');</script>";
    }
}

$conexion->close();
?>

