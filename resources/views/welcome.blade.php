<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Prueba test finaktiva</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light py-4">
    <div class="container">
        <h2 class="mb-4 text-center">Prueba test finaktiva</h2>

        <div class="text-right mb-3">
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalCrearEvento">
                Crear nuevo evento
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tablaEventos">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Fecha del Evento</th>
                        <th>Descripción</th>
                        <th>Tipo de Evento</th>
                        <th>Origen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Datos vía JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalCrearEvento" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="formEvento" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Evento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="fecha_evento">Fecha del evento</label>
                        <input type="datetime-local" class="form-control" name="fecha_evento" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control" name="descripcion" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="tipo_evento">Tipo de evento</label>
                        <select class="form-control" name="tipo_evento" required>
                            <option value="">Seleccione</option>
                            <option value="api">API</option>
                            <option value="formulario">Formulario</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="origen">Origen (opcional)</label>
                        <input type="text" class="form-control" name="origen">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function cargarEventos() {
            fetch('/event-logs')
                .then(res => res.json())
                .then(data => {
                    const tbody = document.querySelector('#tablaEventos tbody');
                    tbody.innerHTML = '';
                    data.data.forEach(evento => insertarFila(evento));
                });
        }

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
            tbody.insertAdjacentHTML('afterbegin', fila);
        }

        function eliminarEvento(id, boton) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción eliminará el evento.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/event-logs/delete/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(response => {
                        if (response.mensaje) {
                            Swal.fire('Eliminado', 'Evento eliminado exitosamente.', 'success');

                            // Quitar fila de la tabla
                            const fila = boton.closest('tr');
                            if (fila) fila.remove();
                        } else {
                            Swal.fire('Error', 'No se pudo eliminar el evento.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error('Error al eliminar:', err);
                        Swal.fire('Error', 'Ocurrió un error inesperado.', 'error');
                    });
                }
            });
        }


        document.addEventListener('DOMContentLoaded', () => {
            cargarEventos();

            const form = document.getElementById('formEvento');
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                // Validaciones manuales
                if (!data.fecha_evento || !data.descripcion || !data.tipo_evento) {
                    Swal.fire('Error', 'Todos los campos obligatorios deben ser completados.', 'error');
                    return;
                }

                fetch('/event-logs/storeEvent', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(response => {
                    if (response.mensaje) {
                        insertarFila(response.data);
                        Swal.fire('Éxito', 'Evento guardado exitosamente.', 'success');

                        setTimeout(() => {
                            $('#modalCrearEvento').modal('hide');
                            form.reset();
                        }, 3000);
                    } else {
                        Swal.fire('Error', 'No se pudo guardar el evento.', 'error');
                    }
                })
                .catch(err => {
                    if (err?.status === 422 && err?.errores) {
                        Swal.fire('Validación', 'Verifica los campos ingresados.', 'warning');
                    } else {
                        Swal.fire('Error', 'Ocurrió un error inesperado.', 'error');
                    }
                    console.error(err);
                });
            });
        });
    </script>
</body>
</html>
