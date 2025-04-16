<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Prueba test finaktiva</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light py-4">
    <div class="container">
        <h2 class="mb-4 text-center">Prueba test finaktiva</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tablaEventos">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Fecha del Evento</th>
                        <th>Descripción</th>
                        <th>Tipo de Evento</th>
                        <th>Origen</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Aquí se cargan los eventos desde JS -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('/event-logs/')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector('#tablaEventos tbody');
                    tbody.innerHTML = '';

                    data.data.forEach(evento => {
                        const fila = `
                            <tr>
                                <td>${evento.id}</td>
                                <td>${new Date(evento.fecha_evento).toLocaleString()}</td>
                                <td>${evento.descripcion}</td>
                                <td>${evento.tipo_evento}</td>
                                <td>${evento.origen}</td>
                            </tr>
                        `;
                        tbody.innerHTML += fila;
                    });
                })
                .catch(error => {
                    console.error('Error al obtener eventos:', error);
                    const tbody = document.querySelector('#tablaEventos tbody');
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">No se pudieron cargar los eventos</td></tr>`;
                });
        });
    </script>
</body>
</html>
