<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "gtex");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $nocontrol = trim($_POST['nocontrol']);
    $codigoRFID = trim($_POST['rfid']);

    // Validar campos vacíos
    if ($nombre && $apellido && $correo && $telefono && $nocontrol && $codigoRFID) {

        // Verificar si el código RFID existe en la tabla tarjeta_rfid
        $stmt = $conexion->prepare("SELECT idTarjeta FROM tarjeta_rfid WHERE codigo = ?");
        $stmt->bind_param("s", $codigoRFID);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            // Tarjeta reconocida, continuar con el registro
            $fila = $resultado->fetch_assoc();
            $idTarjeta = $fila['idTarjeta'];

            // Insertar cliente
            $nombreCompleto = $nombre . " " . $apellido;
            $stmtCliente = $conexion->prepare("INSERT INTO cliente (nombreCliente, correo, telefono, NoControl, idTarjeta) VALUES (?, ?, ?, ?, ?)");
            $stmtCliente->bind_param("ssssi", $nombreCompleto, $correo, $telefono, $nocontrol, $idTarjeta);

            if ($stmtCliente->execute()) {
                echo "<script>alert('Registro exitoso'); window.location.href = 'login.html';</script>";
            } else {
                echo "<script>alert('Error al registrar al cliente');</script>";
            }

            $stmtCliente->close();
        } else {
            // Tarjeta no encontrada
            echo "<script>alert('Tarjeta no reconocida. Por favor, utiliza una tarjeta registrada.');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Por favor completa todos los campos.');</script>";
    }
}

$conexion->close();
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JTex - Registro de Usuario</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #000000;  /* Negro */
            --secondary: #FFFFFF; /* Blanco */
            --accent: #F5F5F5;    /* Gris claro */
            --dark: #333333;      /* Gris oscuro */
            --light: #FFFFFF;     /* Blanco */
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: var(--accent);
            color: var(--dark);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .register-container {
            width: 100%;
            max-width: 600px;
            background-color: var(--secondary);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 2.5rem;
            margin: 2rem;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .logo img {
            height: 50px;
            margin-right: 10px;
        }
        
        .logo h1 {
            color: var(--primary);
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .register-header h2 {
            color: var(--primary);
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .register-header p {
            color: var(--dark);
            opacity: 0.8;
        }
        
        .register-form .form-group {
            margin-bottom: 1.5rem;
        }
        
        .register-form label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary);
            font-weight: 500;
        }
        
        .register-form input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .register-form input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(0,0,0,0.1);
        }
        
        .form-row {
            display: flex;
            gap: 1rem;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .cta-button {
            width: 100%;
            background-color: var(--primary);
            color: var(--secondary);
            border: none;
            padding: 1rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
        }
        
        .cta-button:hover {
            background-color: #333;
            transform: translateY(-2px);
        }
        
        .register-footer {
            text-align: center;
            margin-top: 2rem;
            color: var(--dark);
            font-size: 0.9rem;
        }
        
        .register-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-footer a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
        
        @media (max-width: 600px) {
            .register-container {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }
            
            .logo h1 {
                font-size: 1.5rem;
            }
            
            .register-header h2 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <div class="logo">
                <img src="logo.jpg" alt="JTex Logo">
                <h1>JTex</h1>
            </div>
            <h2>Registro de Usuario</h2>
            <p>Completa el formulario para obtener acceso a nuestros scooters eléctricos</p>
        </div>
        
      <form class="register-form" action="registro.php" method="POST">


            <div class="form-row">
                <div class="form-group">
                    <label for="nombre">Nombre(s)</label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Ej. Juan">
                </div>
                
                <div class="form-group">
                    <label for="apellido">Apellido(s)</label>
                    <input type="text" id="apellido" name="apellido" required placeholder="Ej. Pérez López">
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Correo Electrónico Institucional</label>
                <input type="email" id="email" name="email" required placeholder="tu@correo.itsu.edu.mx">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" required placeholder="Ej. 4431234567">
                </div>
                
                <div class="form-group">
                    <label for="nocontrol">Número de Control</label>
                    <input type="text" id="nocontrol" name="nocontrol" required placeholder="Ej. 202345678">
                </div>
            </div>
            
            <div class="form-group">
                <label for="rfid">Número de Tarjeta RFID</label>
                <input type="text" id="rfid" name="rfid" required placeholder="Ej. 1234567890">
                <small style="display: block; margin-top: 0.5rem; color: #666;">Si no tienes tu tarjeta RFID, acude al departamento de servicios escolares</small>
            </div>
            
            <button type="submit" class="cta-button">Registrarse</button>
        </form>
        
        
    </div>
</body>
</html>