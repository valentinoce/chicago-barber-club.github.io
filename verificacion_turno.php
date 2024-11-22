<?php
// Conexión a la base de datos
$servidor = "localhost";
$usuario = "root";
$clave = "";
$baseDeDatos = "resgistro_usuraio";
$enlace = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);

// Definir variable para el mensaje
$mensaje = "tu turno";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Comprobar si el campo 'email' está presente en $_POST
    if (isset($_POST['email'])) {
        // Recibir el correo del formulario
        $email = $_POST['email'];

        // Verificar si el correo existe en la base de datos (asegúrate de que 'email' es el nombre correcto de la columna)
        $consulta = "SELECT * FROM datos WHERE email = '$email'";
        $resultado = mysqli_query($enlace, $consulta);

        if (!$resultado) {
            die("Error en la consulta: " . mysqli_error($enlace));
        }

        if (mysqli_num_rows($resultado) > 0) {
            // Obtener los datos del usuario
            $usuario = mysqli_fetch_assoc($resultado);

            // Verificar si 'id_cliente' existe en el arreglo
            if (isset($usuario['id_cliente'])) {
                $id_cliente = $usuario['id_cliente'];

                // Consulta para obtener el turno reservado
                $consultaTurno = "SELECT * FROM turnos WHERE id_cliente = '$id_cliente' AND estado = 'reservado'";
                $resultadoTurno = mysqli_query($enlace, $consultaTurno);

                if (!$resultadoTurno) {
                    die("Error en la consulta de turnos: " . mysqli_error($enlace));
                }

                if (mysqli_num_rows($resultadoTurno) > 0) {
                    $turnoReservado = mysqli_fetch_assoc($resultadoTurno);
                    $fecha = $turnoReservado['fecha'];
                    $hora = $turnoReservado['hora'];

                    $mensaje = "Tu turno está confirmado para el $fecha a las $hora.";

                    // Enviar mensaje a Telegram
                    $chatId = "835995877";  // Reemplaza con tu nuevo ID de chat
                    $message = "El usuario con correo $email ha consultado su turno: $fecha a las $hora.";
                    sendMessage($chatId, $message);
                } else {
                    $mensaje = "No se ha encontrado un turno reservado para este correo.";
                }
            } else {
                $mensaje = "No se ha encontrado un 'id_cliente' asociado con este correo.";
            }
        } else {
            $mensaje = "No se ha encontrado un usuario con este correo.";
        }
    } else {
        $mensaje = "Por favor, ingresa tu correo.";
    }
}

// Función para enviar mensaje a Telegram
function sendMessage($chatId, $message) {
    $token = "8183381053:AAEfeZnJWS_I6RWcpgHjiG8MmB_LfwZ2X_g"; // Tu token de Telegram
    
    // Codificar parámetros
    $encodedMessage = urlencode($message); // Codificar el mensaje
    $encodedChatId = urlencode($chatId);   // Codificar el chat_id

    // Crear URL de la API
    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$encodedChatId&text=$encodedMessage&parse_mode=HTML";

    // Inicializar cURL
    $ch = curl_init();

    // Establecer las opciones de cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Deshabilitar verificación SSL si es necesario

    // Ejecutar la solicitud
    $response = curl_exec($ch);

    // Comprobar si hay un error en la solicitud
    if (curl_errno($ch)) {
        echo 'Error de cURL: ' . curl_error($ch);
    } else {
        // Mostrar la respuesta para depuración
        var_dump($response);
    }

    // Cerrar la sesión de cURL
    curl_close($ch);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Turno</title>
    <link rel="stylesheet" href="alertas.js">
    <link rel="stylesheet" href="botton.css">
    <link rel="stylesheet" href="calendario.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="fonts.css">
    <link rel="stylesheet" href="formulario2.css">
    <link href="https://cdn.jsdelivr.net/npm/alertifyjs@1.11.0/build/css/alertify.min.css" rel="stylesheet"/>
</head>
<body>
    <nav class="navbar navbar-light bg-light" style="display: flex; text-align: center; justify-content: center;">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Contacto</a></li>
        </ul>
    </nav>
    
    <form action="verificacion_turno.php" method="post" class="formulario">
        <p class="form_text">Ingresa el Email para Saber tu día y horario</p>
        <input type="email" name="email" id="email" required placeholder="Ingresa tu correo">
        <input type="submit" name="enviar" id="enviar">
    </form>

    <?php if ($mensaje): ?>
        <p class="form_text"><?php echo $mensaje; ?></p>
    <?php endif; ?>
</body>
</html>
