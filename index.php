<?php
$servidor = "localhost";
$usuario = "root";
$clave = "";
$baseDeDatos = "resgistro_usuraio";
$enlace = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);

$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];

    // Convertir fecha
    $fecha_convertida = DateTime::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');

    // Insertar datos del cliente
    $insertarDatos = "INSERT INTO datos (nombre, apellido, email, phone) VALUES ('$nombre', '$apellido', '$email', '$phone')";
    $ejecutarInsertar = mysqli_query($enlace, $insertarDatos);

    if ($ejecutarInsertar) {
        $id_cliente = mysqli_insert_id($enlace);  // Obtener ID del cliente

        // Verificar disponibilidad del turno
        $consultaTurno = "SELECT * FROM turnos WHERE fecha = '$fecha_convertida' AND hora = '$hora' AND estado = 'disponible'";
        $resultadoTurno = mysqli_query($enlace, $consultaTurno);

        if (mysqli_num_rows($resultadoTurno) > 0) {
            // Reservar el turno
            $reservarTurno = "UPDATE turnos SET estado = 'reservado', id_cliente = '$id_cliente' WHERE fecha = '$fecha_convertida' AND hora = '$hora'";
            $ejecutarReserva = mysqli_query($enlace, $reservarTurno);
            
            $mensaje = $ejecutarReserva ? "Turno reservado exitosamente." : "Error al reservar el turno.";
        } else {
            $mensaje = "No hay turnos disponibles.";
        }
    } else {
        $mensaje = "Error al registrar los datos: " . mysqli_error($enlace);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHICAGO BARBER CLUB</title>        
    <link rel="stylesheet" href="calendario.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="formulario.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/alertifyjs@1.11.0/build/css/alertify.min.css" rel="stylesheet"/>
</head>
<body>
    <nav class="navbar navbar-light bg-light" style="display: flex; text-align: center; justify-content: center;">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="verificacion_turno.php">Consulta tu Turno</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Contacto</a></li>
        </ul>
    </nav>

    <section> 
        <h1>CHICAGO BARBER CLUB</h1>
        <p class="form_text">Lorem ipsum dolor, <strong>sit amet consectetur adipisicing elit.</strong> Nostrum ex alias nemo...</p>
    </section>

    <form action="index.php" name="resgistro_usuraio" method="post" class="formulario">
        <div class="formulario_input">
            <p class="form_text">Rellena el formulario y luego pide tu turno</p>
            <input type="text" name="nombre" id="nombre" required placeholder="nombre">
            <input type="text" name="apellido" id="apellido" required placeholder="apellido">
            <input type="email" name="email" id="email" required placeholder="email">
            <input type="tel" name="phone" id="phone" required placeholder="telefono">
            <div class="form-group text-center">
                <input type="text" id="datepicker" name="fecha" class="formulario_input" placeholder="fecha" required>
            </div>
            <div class="form-group text-center">
                <select class="form-select" name="hora" required>
                    <option selected>Selecciona la hora</option>
                    <option value="09:00:00">09:00 A.M</option>
                    <option value="10:00:00">10:00 A.M</option>
                    <option value="16:00:00">16:00 P.M</option>
                </select>
                <input type="submit" name="enviar" id="enviar">
            </div>
        </div>
    </form>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(function() {
            $("#datepicker").datepicker({
                dateFormat: "dd/mm/yy",
                changeMonth: true,
                changeYear: true,
                showAnim: "slideDown"
            });
        });
    </script>   

    <footer><p class="form_text">VS-1.9</p></footer>

    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.11.0/build/alertify.min.js"></script>

    <script>
        document.forms['resgistro_usuraio'].onsubmit = function(event) {
            event.preventDefault();
            alertify.confirm("¿Estás seguro de enviar los datos?", 
                function() {
                    document.forms['resgistro_usuraio'].submit();
                },
                function() {
                    alertify.error('Envío cancelado');
                }
            );
        };

        <?php if ($mensaje): ?>
            alertify.success('<?php echo $mensaje; ?>');
        <?php endif; ?>
    </script>
</body>
</html>
