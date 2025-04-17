<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Prueba test finaktiva</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 4 CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Prueba test finaktiva</h2>

    <!-- Botón y Modal para crear evento -->
    <div class="mb-3">
        <button class="btn btn-success" data-toggle="modal" data-target="#modalCrear">Crear Evento</button>
    </div>

    <!-- Modal Crear Evento -->
    <div class="modal fade" id="modalCrear" tabindex="-1" role="dialog" aria-labelledby="modalCrearLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formCrearEvento">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCrearLabel">Crear Evento</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="fecha_evento">Fecha del Evento</label>
                            <input type="datetime-local" class="form-control" id="fecha_evento" required>
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea class="form-control" id="descripcion" rows="2" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="tipo_evento_modal">Tipo de Evento</label>
                            <select class="form-control" id="tipo_evento_modal" required>
                                <option value="api">API</option>
                                <option value="formulario">Formulario</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="origen">Origen (opcional)</label>
                            <input type="text" class="form-control" id="origen">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Filtros -->
    <div class="row mb-3">
        <div class="col-md-3">
            <label>Tipo de Evento</label>
            <select class="form-control" id="tipo_evento">
                <option value="all">Todos</option>
                <option value="api">API</option>
                <option value="formulario">Formulario</option>
            </select>
        </div>
        <div class="col-md-3">
            <label>Fecha Inicio</label>
            <input type="date" id="filtroFechaInicio" class="form-control" value="{{ now()->toDateString() }}">
        </div>
        <div class="col-md-3">
            <label>Fecha Fin</label>
            <input type="date" id="filtroFechaFin" class="form-control" value="{{ now()->toDateString() }}">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary w-100" onclick="cargarEventos(1)">Filtrar</button>
        </div>
    </div>

    <!-- Tabla -->
    <table class="table table-bordered" id="tablaEventos">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Fecha Evento</th>
                <th>Descripción</th>
                <th>Tipo</th>
                <th>Origen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <!-- Paginación -->
    <nav>
        <ul class="pagination" id="paginacion"></ul>
    </nav>
</div>

<!-- Scripts -->
<script>
    window.onload = () => {
        cargarEventosIniciales();
        defalutDate();
    };

    function cargarEventosIniciales() {
        fetch('/event-logs/api/default')
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector('#tablaEventos tbody');
                tbody.innerHTML = '';
                data.forEach(evento => insertarFila(evento));
                document.getElementById('paginacion').innerHTML = '';
            })
            .catch(error => {
                console.error('Error al cargar eventos iniciales:', error);
                Swal.fire('Error', 'No se pudieron cargar los eventos', 'error');
            });
    }

    function cargarEventos(pagina) {
        const tipo = document.getElementById('tipo_evento').value;
        const fechaInicio = document.getElementById('filtroFechaInicio').value;
        const fechaFin = document.getElementById('filtroFechaFin').value;

        let url = `/event-logs/api?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&page=${pagina}`;
        if (tipo !== 'todos') {
            url += `&tipo_evento=${tipo}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                const tbody = document.querySelector('#tablaEventos tbody');
                tbody.innerHTML = '';
                data.data.forEach(evento => insertarFila(evento));
                construirPaginacion(data.current_page, data.last_page);
            })
            .catch(err => {
                console.error('Error al cargar eventos:', err);
                Swal.fire('Error', 'No se pudieron cargar los eventos', 'error');
            });
    }

    document.getElementById('formCrearEvento').addEventListener('submit', function(e) {
        e.preventDefault();

        const fecha = document.getElementById('fecha_evento').value;
        const descripcion = document.getElementById('descripcion').value.trim();
        const tipo = document.getElementById('tipo_evento_modal').value;
        const origen = document.getElementById('origen').value.trim();

        if (!fecha || !descripcion || !tipo) {
            Swal.fire('Campos requeridos', 'Por favor, completa todos los campos obligatorios', 'warning');
            return;
        }

        fetch('/event-logs/storeEvent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                fecha_evento: fecha,
                descripcion: descripcion,
                tipo_evento: tipo,
                origen: origen
            })
        })
        .then(res => {
            if (!res.ok) throw res;
            return res.json();
        })
        .then(data => {
            $('#modalCrear').modal('hide');
            Swal.fire('Éxito', 'Evento creado correctamente', 'success');
            cargarEventosIniciales();

            document.getElementById('formCrearEvento').reset();
            defalutDate();
            })
        .catch(async err => {
            let mensaje = 'Error al registrar el evento';
            if (err.status === 422) {
                const errores = await err.json();

                if (errores.error === true && errores.errors) {
                    mensaje = Object.values(errores.errors)
                        .flat()
                        .join('<br>');
                }
                else if (errores.errores) {
                    mensaje = Object.values(errores.errores).join('<br>');
                }
            }
            Swal.fire('Error', mensaje, 'error');
        });
    });

    function insertarFila(evento) {
        const tbody = document.querySelector('#tablaEventos tbody');
        const fila = `
            <tr data-id="${evento.id}">
                <td>${evento.id}</td>
                <td>${new Date(evento.fecha_evento).toLocaleString()}</td>
                <td>${evento.descripcion}</td>
                <td>${evento.tipo_evento}</td>
                <td>${evento.origen}</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="eliminarEvento(${evento.id}, this)">Eliminar</button>
                </td>
            </tr>`;
        tbody.insertAdjacentHTML('beforeend', fila);
    }

    function construirPaginacion(actual, total) {
        const paginacion = document.getElementById('paginacion');
        paginacion.innerHTML = '';

        for (let i = 1; i <= total; i++) {
            paginacion.innerHTML += `
                <li class="page-item ${i === actual ? 'active' : ''}">
                    <button class="page-link" onclick="cargarEventos(${i})">${i}</button>
                </li>`;
        }
    }

    function eliminarEvento(id, boton) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará el evento.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/event-logs/delete/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => {
                    if (!res.ok) throw new Error('Error al eliminar');
                    return res.json();
                })
                .then(resp => {
                    if (resp.mensaje) {
                        boton.closest('tr').remove();
                        Swal.fire('Eliminado', resp.mensaje, 'success');
                    } else {
                        throw new Error('Error inesperado');
                    }
                })
                .catch(err => {
                    console.error('Error al eliminar:', err);
                    Swal.fire('Error', 'No se pudo eliminar el evento', 'error');
                });
            }
        });
    }

    function defalutDate(){

        const now = new Date();

        // Formatear la fecha y hora en el formato requerido para datetime-local (YYYY-MM-DDThh:mm)
        const year = now.getFullYear();
        const month = (now.getMonth() + 1).toString().padStart(2, '0');
        const day = now.getDate().toString().padStart(2, '0');
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');

        const formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;

        document.getElementById('fecha_evento').value = formattedDateTime;
    }

</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
