<?php
include 'conexion.php';
include 'procesar.php';

$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 10;
$offset = ($pagina_actual - 1) * $por_pagina;
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : '';
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'fecha';
$direccion = isset($_GET['direccion']) ? $_GET['direccion'] : 'ASC';

$eventos = listarEventos($conexion, $filtro, $orden, $direccion, $por_pagina, $offset);
$total_eventos = contarEventos($conexion, $filtro);
$total_paginas = ceil($total_eventos / $por_pagina);
$organizadores = listarOrganizadores($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Eventos Deportivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-editar { background-color: #ff9800; border-color: #ff9800; color: white; }
        .btn-editar:hover { background-color: #f57c00; border-color: #f57c00; color: white; }
        .btn-eliminar { background-color: #dc3545; border-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Gestión de Eventos Deportivos</h1>

        <!-- Buscador -->
        <div class="row mb-4">
            <div class="col-md-6">
                <input type="text" id="buscar" class="form-control" placeholder="Buscar...">
            </div>
            <div class="col-md-2">
                <button onclick="buscar()" class="btn btn-primary">Buscar</button>
            </div>
        </div>

        <!-- Listado de Eventos -->
        <h2>Listado de Eventos</h2>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo de Deporte</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Ubicación</th>
                        <th>Organizador</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-eventos">
                    <?php while($evento = $eventos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($evento['nombre_evento']); ?></td>
                        <td><?php echo htmlspecialchars($evento['tipo_deporte']); ?></td>
                        <td><?php echo htmlspecialchars($evento['fecha']); ?></td>
                        <td><?php echo htmlspecialchars($evento['hora']); ?></td>
                        <td><?php echo htmlspecialchars($evento['ubicacion']); ?></td>
                        <td><?php echo htmlspecialchars($evento['organizador_nombre']); ?></td>
                        <td>
                            <button onclick="editarEvento(<?php echo $evento['id']; ?>)" class="btn btn-editar btn-sm">Editar</button>
                            <button onclick="eliminarEvento(<?php echo $evento['id']; ?>)" class="btn btn-eliminar btn-sm">Eliminar</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <nav aria-label="Navegación de eventos">
            <ul class="pagination">
                <?php for($i = 1; $i <= $total_paginas; $i++): ?>
                <li class="page-item <?php echo $pagina_actual == $i ? 'active' : ''; ?>">
                    <a class="page-link" href="?pagina=<?php echo $i; ?>&filtro=<?php echo urlencode($filtro); ?>&orden=<?php echo $orden; ?>&direccion=<?php echo $direccion; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>

        <button onclick="mostrarFormularioEvento()" class="btn btn-primary mb-4">Añadir Evento</button>

        <!-- Listado de Organizadores -->
        <h2 class="mt-4">Listado de Organizadores</h2>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-organizadores">
                    <?php while($organizador = $organizadores->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($organizador['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($organizador['email']); ?></td>
                        <td><?php echo htmlspecialchars($organizador['telefono']); ?></td>
                        <td>
                            <button onclick="eliminarOrganizador(<?php echo $organizador['id']; ?>)" 
                                    class="btn btn-eliminar btn-sm">Eliminar</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <button onclick="mostrarFormularioOrganizador()" class="btn btn-primary">Añadir Organizador</button>
    </div>

    <!-- Modal para Eventos -->
    <div class="modal fade" id="eventoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventoModalLabel">Añadir/Editar Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="eventoForm">
                        <input type="hidden" id="evento_id" name="id">
                        <input type="hidden" name="accion" value="agregar_evento">
                        <div class="mb-3">
                            <label for="nombre_evento" class="form-label">Nombre del Evento</label>
                            <input type="text" class="form-control" id="nombre_evento" name="nombre_evento" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipo_deporte" class="form-label">Tipo de Deporte</label>
                            <input type="text" class="form-control" id="tipo_deporte" name="tipo_deporte" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" required>
                        </div>
                        <div class="mb-3">
                            <label for="hora" class="form-label">Hora</label>
                            <input type="time" class="form-control" id="hora" name="hora" required>
                        </div>
                        <div class="mb-3">
                            <label for="ubicacion" class="form-label">Ubicación</label>
                            <input type="text" class="form-control" id="ubicacion" name="ubicacion" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_organizador" class="form-label">Organizador</label>
                            <select class="form-control" id="id_organizador" name="id_organizador" required>
                                <?php 
                                $organizadores->data_seek(0);
                                while($org = $organizadores->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $org['id']; ?>">
                                        <?php echo htmlspecialchars($org['nombre']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarEvento()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Organizadores -->
    <div class="modal fade" id="organizadorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="organizadorModalLabel">Añadir Organizador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="organizadorForm">
                        <input type="hidden" name="accion" value="agregar_organizador">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarOrganizador()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const eventoModal = new bootstrap.Modal(document.getElementById('eventoModal'));
        const organizadorModal = new bootstrap.Modal(document.getElementById('organizadorModal'));

        function mostrarFormularioEvento() {
            document.getElementById('eventoForm').reset();
            document.getElementById('eventoModalLabel').textContent = 'Añadir Evento';
            eventoModal.show();
        }

        function mostrarFormularioOrganizador() {
            document.getElementById('organizadorForm').reset();
            organizadorModal.show();
        }

        function editarEvento(id) {
            fetch(`procesar.php?accion=editar_evento&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('evento_id').value = data.id;
                    document.getElementById('nombre_evento').value = data.nombre_evento;
                    document.getElementById('tipo_deporte').value = data.tipo_deporte;
                    document.getElementById('fecha').value = data.fecha;
                    document.getElementById('hora').value = data.hora;
                    document.getElementById('ubicacion').value = data.ubicacion;
                    document.getElementById('id_organizador').value = data.id_organizador;
                    document.getElementById('eventoModalLabel').textContent = 'Editar Evento';
                    eventoModal.show();
                });
        }

        function guardarEvento() {
            const form = document.getElementById('eventoForm');
            fetch('procesar.php', {
                method: 'POST',
                body: new FormData(form)
            }).then(() => {
                eventoModal.hide();
                location.reload();
            });
        }

        function guardarOrganizador() {
            const form = document.getElementById('organizadorForm');
            fetch('procesar.php', {
                method: 'POST',
                body: new FormData(form)
            }).then(() => {
                organizadorModal.hide();
                location.reload();
            });
        }

        function eliminarEvento(id) {
            if(confirm('¿Está seguro de que desea eliminar este evento?')) {
                fetch('procesar.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `accion=eliminar_evento&id=${id}`
                }).then(() => location.reload());
            }
        }

        function eliminarOrganizador(id) {
            if(confirm('¿Está seguro de que desea eliminar este organizador?')) {
                fetch('procesar.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `accion=eliminar_organizador&id=${id}`
                }).then(() => location.reload());
            }
        }

        function buscar() {
            const busqueda = document.getElementById('buscar').value;
            fetch(`procesar.php?accion=buscar&q=${encodeURIComponent(busqueda)}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('tabla-eventos').innerHTML = html;
                });
        }
    </script>
</body>
</html>