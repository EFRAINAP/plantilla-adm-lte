<?php 
$title = "Reportes del Sistema";
ob_start();
?>
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filtros de Reportes</h3>
                    <div class="card-tools">
                        <button class="btn btn-success btn-sm" onclick="exportReport('excel')">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="exportReport('pdf')">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="reportFilters" class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tipo de Reporte</label>
                                <select class="form-control" name="report_type" onchange="updateReport()">
                                    <option value="users">Usuarios</option>
                                    <option value="sales">Ventas</option>
                                    <option value="activity">Actividad</option>
                                    <option value="errors">Errores</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha Desde</label>
                                <input type="date" class="form-control" name="date_from" value="2024-01-01" onchange="updateReport()">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha Hasta</label>
                                <input type="date" class="form-control" name="date_to" value="<?= date('Y-m-d') ?>" onchange="updateReport()">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Estado</label>
                                <select class="form-control" name="status" onchange="updateReport()">
                                    <option value="">Todos</option>
                                    <option value="active">Activos</option>
                                    <option value="inactive">Inactivos</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gráfico de Reportes</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="reportChart" style="height: 400px;">
                        <!-- Aquí iría el gráfico -->
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="text-center">
                                <i class="fas fa-chart-bar fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Gráfico de Reportes</h5>
                                <p class="text-muted">Los datos se mostrarían aquí</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Estadísticas Rápidas</h3>
                </div>
                <div class="card-body">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-info"><i class="far fa-user"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Usuarios</span>
                            <span class="info-box-number">1,410</span>
                        </div>
                    </div>
                    
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success"><i class="far fa-chart-bar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Ventas</span>
                            <span class="info-box-number">$41,410</span>
                        </div>
                    </div>
                    
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning"><i class="far fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Tiempo Promedio</span>
                            <span class="info-box-number">2.4 min</span>
                        </div>
                    </div>
                    
                    <div class="info-box">
                        <span class="info-box-icon bg-danger"><i class="far fa-exclamation-triangle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Errores</span>
                            <span class="info-box-number">3</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Datos del Reporte</h3>
                </div>
                <div class="card-body">
                    <table id="reportTable" class="table table-bordered table-striped">
                        <thead>
                            <tr id="reportTableHeader">
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Fecha Registro</th>
                                <th>Estado</th>
                                <th>Último Acceso</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
                            <tr>
                                <td>1</td>
                                <td>Juan Pérez</td>
                                <td>juan@example.com</td>
                                <td>2024-01-15</td>
                                <td><span class="badge badge-success">Activo</span></td>
                                <td>2024-07-20 14:30</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>María García</td>
                                <td>maria@example.com</td>
                                <td>2024-02-20</td>
                                <td><span class="badge badge-success">Activo</span></td>
                                <td>2024-07-19 09:15</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Carlos López</td>
                                <td>carlos@example.com</td>
                                <td>2024-03-10</td>
                                <td><span class="badge badge-warning">Inactivo</span></td>
                                <td>2024-07-10 16:45</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    initializeReportTable();
});

function initializeReportTable() {
    if ($.fn.DataTable.isDataTable('#reportTable')) {
        $('#reportTable').DataTable().destroy();
    }
    
    $('#reportTable').DataTable({
        "responsive": true,
        "autoWidth": false,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "buttons": [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
}

function updateReport() {
    const formData = new FormData(document.getElementById('reportFilters'));
    const reportType = formData.get('report_type');
    
    toastr.info('Actualizando reporte...');
    
    // Simular carga de datos
    setTimeout(() => {
        updateTableHeaders(reportType);
        updateTableData(reportType);
        toastr.success('Reporte actualizado');
    }, 1500);
}

function updateTableHeaders(reportType) {
    const headers = {
        'users': ['ID', 'Nombre', 'Email', 'Fecha Registro', 'Estado', 'Último Acceso'],
        'sales': ['ID', 'Producto', 'Cliente', 'Monto', 'Fecha', 'Estado'],
        'activity': ['ID', 'Usuario', 'Acción', 'Recurso', 'Fecha', 'IP'],
        'errors': ['ID', 'Tipo', 'Mensaje', 'Archivo', 'Fecha', 'Usuario']
    };
    
    const headerRow = document.getElementById('reportTableHeader');
    headerRow.innerHTML = headers[reportType].map(h => `<th>${h}</th>`).join('');
}

function updateTableData(reportType) {
    const sampleData = {
        'users': [
            ['1', 'Juan Pérez', 'juan@example.com', '2024-01-15', '<span class="badge badge-success">Activo</span>', '2024-07-20 14:30'],
            ['2', 'María García', 'maria@example.com', '2024-02-20', '<span class="badge badge-success">Activo</span>', '2024-07-19 09:15']
        ],
        'sales': [
            ['1', 'Producto A', 'Cliente X', '$150.00', '2024-07-20', '<span class="badge badge-success">Completada</span>'],
            ['2', 'Producto B', 'Cliente Y', '$89.99', '2024-07-19', '<span class="badge badge-warning">Pendiente</span>']
        ],
        'activity': [
            ['1', 'Juan Pérez', 'Login', 'Sistema', '2024-07-20 14:30', '192.168.1.100'],
            ['2', 'María García', 'Editar Usuario', 'users/edit/5', '2024-07-20 14:25', '192.168.1.101']
        ],
        'errors': [
            ['1', 'PHP Error', 'Undefined variable', '/app/controllers/UserController.php:45', '2024-07-20 14:20', 'Sistema'],
            ['2', 'Database', 'Connection timeout', '/app/database/Connection.php:12', '2024-07-20 13:15', 'Sistema']
        ]
    };
    
    const tbody = document.getElementById('reportTableBody');
    tbody.innerHTML = sampleData[reportType].map(row => 
        `<tr>${row.map(cell => `<td>${cell}</td>`).join('')}</tr>`
    ).join('');
    
    initializeReportTable();
}

function exportReport(format) {
    toastr.info(`Exportando reporte en formato ${format.toUpperCase()}...`);
    
    setTimeout(() => {
        toastr.success(`Reporte exportado exitosamente en ${format.toUpperCase()}`);
    }, 2000);
}
</script>

<?php
$content = ob_get_clean();
include RESOURCES_PATH . '/layouts/main.php';
