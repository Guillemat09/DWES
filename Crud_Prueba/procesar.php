<?php
include "conexion.php";

function sanitizar($conexion, $dato) {
    return $conexion->real_escape_string(htmlspecialchars(trim($dato)));
}

function listarEventos($conexion, $filtro = '', $orden = 'fecha', $direccion = 'ASC', $limite = 10, $offset = 0) {
    $sql = "SELECT e.*, o.nombre as organizador_nombre 
            FROM eventos e 
            LEFT JOIN organizadores o ON e.id_organizador = o.id
            WHERE e.nombre_evento LIKE ? OR e.tipo_deporte LIKE ? OR e.ubicacion LIKE ? OR o.nombre LIKE ?
            ORDER BY $orden $direccion
            LIMIT ? OFFSET ?";
    
    $stmt = $conexion->prepare($sql);
    $busqueda = "%$filtro%";
    $stmt->bind_param("ssssii", $busqueda, $busqueda, $busqueda, $busqueda, $limite, $offset);
    $stmt->execute();
    return $stmt->get_result();
}

function contarEventos($conexion, $filtro = '') {
    $sql = "SELECT COUNT(*) as total 
            FROM eventos e 
            LEFT JOIN organizadores o ON e.id_organizador = o.id
            WHERE e.nombre_evento LIKE ? OR e.tipo_deporte LIKE ? OR e.ubicacion LIKE ? OR o.nombre LIKE ?";
    
    $stmt = $conexion->prepare($sql);
    $busqueda = "%$filtro%";
    $stmt->bind_param("ssss", $busqueda, $busqueda, $busqueda, $busqueda);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    return $resultado['total'];
}

function listarOrganizadores($conexion) {
    return $conexion->query("SELECT * FROM organizadores");
}

//Añadir organizadores
function AñadirOrganizador()
{
    global $conexion;

    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $telefono = $_POST["telefono"];

    $nombre = sanitizar($conexion, $nombre);
    $email = sanitizar($conexion, $email);
    $telefono = sanitizar($conexion, $telefono);

    $sql = $conexion->query(
        "INSERT INTO organizadores(nombre, email, telefono) VALUES('$nombre', '$email', '$telefono')"
    );

    if ($sql) {
        Header("Location: index.php");
    } else {
        echo "Error al añadir organizador: " . $conexion->error;
    }
}

// Verifica si se ha enviado el formulario
if (isset($_POST["registrarOrganizador"])) {
    AñadirOrganizador();
}

//Eliminar organizadores
// Función para eliminar organizador
function eliminarOrganizador($id)
{
    global $conexion;

    // Verificar si el organizador tiene eventos asignados
    $consultaEventos = $conexion->query(
        "SELECT COUNT(*) AS count FROM eventos WHERE id_organizador = $id"
    );
    $resultado = $consultaEventos->fetch_object();

    if ($resultado->count > 0) {
        // No permitir eliminar si hay eventos asignados
        header(
            "Location: index.php?mensaje=No se puede eliminar el organizador porque tiene eventos asignados."
        );
        exit();
    } else {
        // Proceder con la eliminación
        $sql = $conexion->query("DELETE FROM organizadores WHERE id = $id");

        if ($sql) {
            header(
                "Location: index.php?mensaje=Organizador eliminado correctamente."
            );
            exit();
        } else {
            header(
                "Location: index.php?mensaje=Error al eliminar el organizador: " .
                    $conexion->error
            );
            exit();
        }
    }
}

// Llamar a la función si se recibe el parámetro para eliminar
if (isset($_GET["eliminar_organizador"])) {
    $id = intval($_GET["eliminar_organizador"]);
    eliminarOrganizador($id);
}

// Llamar a la función si se recibe el parámetro para eliminar
if (isset($_GET["eliminar_organizador"])) {
    $id = intval($_GET["eliminar_organizador"]);
    eliminarOrganizador($id);
}
//Añadir eventos
function AñadirEvento()
{
    global $conexion;
    $nombre_evento = $_POST["nombre_evento"];
    $tipo_deporte = $_POST["tipo_deporte"];
    $fecha = $_POST["fecha"];
    $hora = $_POST["hora"];
    $ubicacion = $_POST["ubicacion"];
    $id_organizador = $_POST["id_organizador"];

    $nombre_evento = sanitizar($conexion, $nombre_evento);
    $tipo_deporte = sanitizar($conexion, $tipo_deporte);
    $fecha = sanitizar($conexion, $fecha);
    $hora = sanitizar($conexion, $hora);
    $ubicacion = sanitizar($conexion, $ubicacion);
    $id_organizador = sanitizar($conexion, $id_organizador);


    $sql = $conexion->query(
        " INSERT INTO eventos(nombre_evento,tipo_deporte,fecha,hora,ubicacion,id_organizador)VALUES('$nombre_evento','$tipo_deporte','$fecha','$hora','$ubicacion','$id_organizador') "
    );

    if ($sql) {
        Header("Location: index.php");
    } else {
        echo "Error al añadir evento: " . $conexion->error;
    }
}
if (isset($_POST["btnAñadirEvento"])) {
    AñadirEvento();
}

//Eliminar evento
function eliminarEvento($id)
{
    global $conexion;
    $sql = $conexion->query("DELETE FROM eventos WHERE id = '$id'");
    if ($sql) {
        echo "<div class='alert alert-success'>Evento eliminado</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al eliminar el evento: " .
            $conexion->error .
            "</div>";
    }
}

if (isset($_GET["eliminarEvento"]) && isset($_GET["id"])) {
    $id = intval($_GET["id"]);
    $eliminar = $conexion->query("DELETE FROM eventos WHERE id = $id");

    if ($eliminar) {
        header("Location: index.php?status=success");
    } else {
        header("Location: index.php?status=error");
    }
    exit();
}

//Editar evento

function editarEvento()
{
    global $conexion;
    // Validar el ID antes de usarlo en la consulta
    $id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
    if ($id > 0) {
        $sql = $conexion->query("SELECT * FROM eventos WHERE id = $id");
        return $sql->fetch_object(); // Obtener los datos del evento seleccionado
    }
    return null;
}

$evento = editarEvento();

function buscarEventos($conexion, $busqueda) {
    $busqueda = sanitizar($conexion, $busqueda);
    $query = "SELECT e.*, o.nombre as organizador_nombre 
              FROM eventos e 
              LEFT JOIN organizadores o ON e.id_organizador = o.id 
              WHERE e.nombre_evento LIKE '%$busqueda%' 
              OR e.tipo_deporte LIKE '%$busqueda%' 
              OR e.ubicacion LIKE '%$busqueda%' 
              OR o.nombre LIKE '%$busqueda%'";
    return $conexion->query($query);
}

//Nuevas funciones para procesar.php

// Process POST requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'agregar_evento':
                AñadirEvento();
                break;
            case 'agregar_organizador':
                AñadirOrganizador();
                break;
            case 'eliminar_evento':
                eliminarEvento($_POST['id']);
                break;
            case 'eliminar_organizador':
                eliminarOrganizador($_POST['id']);
                break;
            // Add other cases as needed
        }
    }
    header('Location: index.php');
    exit;
}

// Process GET requests
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['accion'])) {
        switch ($_GET['accion']) {
            case 'editar_evento':
                if (isset($_GET['id'])) {
                    $evento = editarEvento();
                    echo json_encode($evento);
                    exit;
                }
                break;
            case 'buscar':
                if (isset($_GET['q'])) {
                    $resultados = listarEventos($conexion, $_GET['q']);
                    // Output results as HTML
                    while ($evento = $resultados->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($evento['nombre_evento']) . "</td>
                                <td>" . htmlspecialchars($evento['tipo_deporte']) . "</td>
                                <td>" . htmlspecialchars($evento['fecha']) . "</td>
                                <td>" . htmlspecialchars($evento['hora']) . "</td>
                                <td>" . htmlspecialchars($evento['ubicacion']) . "</td>
                                <td>" . htmlspecialchars($evento['organizador_nombre']) . "</td>
                                <td>
                                    <button onclick='editarEvento(" . $evento['id'] . ")' class='btn btn-editar btn-sm'>Editar</button>
                                    <button onclick='eliminarEvento(" . $evento['id'] . ")' class='btn btn-eliminar btn-sm'>Eliminar</button>
                                </td>
                              </tr>";
                    }
                    exit;
                }
                break;
        }
    }
}

if (isset($_GET['accion']) && $_GET['accion'] == 'editar_evento' && isset($_GET['id'])) {

    $id = (int)$_GET['id'];
    if (isset($_POST['nombre_evento'])) {
        // Actualizar el evento
        $nombre = sanitizar($conexion, $_POST['nombre_evento']);
        $tipo = sanitizar($conexion, $_POST['tipo_deporte']);
        $fecha = sanitizar($conexion, $_POST['fecha']);
        $hora = sanitizar($conexion, $_POST['hora']);
        $ubicacion = sanitizar($conexion, $_POST['ubicacion']);
        $organizador = (int)$_POST['id_organizador'];

        $query = "UPDATE eventos SET 
                  nombre_evento = '$nombre', 
                  tipo_deporte = '$tipo', 
                  fecha = '$fecha', 
                  hora = '$hora', 
                  ubicacion = '$ubicacion', 
                  id_organizador = $organizador 
                  WHERE id = $id";
        $conexion->query($query);
        header('Location: index.php');
        exit;
    } else {
        // Obtener datos del evento para edición
        $query = "SELECT * FROM eventos WHERE id = $id";
        $resultado = $conexion->query($query);
        $evento = $resultado->fetch_assoc();
        echo json_encode($evento);
        exit;
    }
}

if (isset($_GET['accion']) && $_GET['accion'] == 'buscar' && isset($_GET['q'])) {

    $busqueda = $_GET['q'];
    $resultados = buscarEventos($conexion, $busqueda);
    while ($evento = $resultados->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($evento['nombre_evento']) . "</td>
                <td>" . htmlspecialchars($evento['tipo_deporte']) . "</td>
                <td>" . htmlspecialchars($evento['fecha']) . "</td>
                <td>" . htmlspecialchars($evento['hora']) . "</td>
                <td>" . htmlspecialchars($evento['ubicacion']) . "</td>
                <td>" . htmlspecialchars($evento['organizador_nombre']) . "</td>
                <td>
                    <button onclick='editarEvento(" . $evento['id'] . ")' class='btn btn-editar btn-sm'>Editar</button>
                    <button onclick='eliminarEvento(" . $evento['id'] . ")' class='btn btn-eliminar btn-sm'>Eliminar</button>
                </td>
              </tr>";
    }
    exit;
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Evento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <form class="my-4" method="POST" action="procesar.php" id="formEventos">
        <h4 class="mb-4">Modificar Evento</h4>

        <?php if ($evento): ?>
            <div class="mb-3">
                <label for="nombre_evento" class="form-label">Nombre del Evento</label>
                <input type="text" class="form-control" id="nombre_evento" name="nombre_evento" value="<?= htmlspecialchars(
                    $evento->nombre_evento
                ) ?>" required>
            </div>
            <div class="mb-3">
                <label for="tipo_deporte" class="form-label">Tipo de Deporte</label>
                <input type="text" class="form-control" id="tipo_deporte" name="tipo_deporte" value="<?= htmlspecialchars(
                    $evento->tipo_deporte
                ) ?>" required>
            </div>
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" class="form-control" id="fecha" name="fecha" value="<?= htmlspecialchars(
                    $evento->fecha
                ) ?>" required>
            </div>
            <div class="mb-3">
                <label for="hora" class="form-label">Hora</label>
                <input type="time" class="form-control" id="hora" name="hora" value="<?= htmlspecialchars(
                    $evento->hora
                ) ?>" required>
            </div>
            <div class="mb-3">
                <label for="ubicacion" class="form-label">Ubicación</label>
                <input type="text" class="form-control" id="ubicacion" name="ubicacion" value="<?= htmlspecialchars(
                    $evento->ubicacion
                ) ?>" required>
            </div>
            <div class="mb-3">
                <label for="id_organizador" class="form-label">Organizador</label>
                <select name="id_organizador" id="id_organizador" class="form-control" required>
                    <option value="">Seleccione un Organizador</option>
                    <?php
                    $sqlOrganizadores = listarOrganizadores($conexion);
                    while ($organizador = $sqlOrganizadores->fetch_object()) {
                        $selected =
                            $organizador->id == $evento->id_organizador
                                ? "selected"
                                : "";
                        echo "<option value='{$organizador->id}' $selected>{$organizador->nombre}</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success" name="btnModificarEvento" value="ok">Guardar Cambios</button>
        <?php else: ?>
            <p class="text-danger">No se encontró el evento.</p>
        <?php endif; ?>
    </form>
</div>
</body>
</html>