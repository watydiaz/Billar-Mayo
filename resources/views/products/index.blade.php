@extends('layouts.app')

@section('title', 'Gestión de Productos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-boxes text-primary"></i> 
                            Sistema de Inventario
                        </h4>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="bi bi-upload"></i> Importar
                            </button>
                            <button class="btn btn-outline-primary" onclick="exportarInventario()">
                                <i class="bi bi-download"></i> Exportar
                            </button>
                            <a href="{{ route('productos.inventario-masivo') }}" class="btn btn-outline-info" target="_blank">
                                <i class="bi bi-boxes"></i> Inventario Masivo
                            </a>
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                <i class="bi bi-plus-lg"></i> Agregar Producto
                            </button>
                        </div>
                    </div>
                    
                    <!-- Estadísticas rápidas -->
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-boxes display-6 me-3"></i>
                                        <div>
                                            <h6 class="mb-0">Total Productos</h6>
                                            <h4 class="mb-0">{{ $products->total() }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-exclamation-triangle display-6 me-3"></i>
                                        <div>
                                            <h6 class="mb-0">Stock Bajo</h6>
                                            <h4 class="mb-0 stock-bajo-count">-</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle display-6 me-3"></i>
                                        <div>
                                            <h6 class="mb-0">Productos Activos</h6>
                                            <h4 class="mb-0 productos-activos-count">-</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-currency-dollar display-6 me-3"></i>
                                        <div>
                                            <h6 class="mb-0">Valor Inventario</h6>
                                            <h4 class="mb-0 valor-inventario">$-</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtros de búsqueda y acciones rápidas -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Búsqueda principal -->
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-search"></i> Buscar Productos
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="searchInput" 
                                               placeholder="Nombre, código o descripción..." 
                                               value="{{ request('search') }}">
                                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Filtro por categoría -->
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-tags"></i> Categoría
                                    </label>
                                    <select class="form-select" id="categoriaFilter">
                                        <option value="">Todas</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                                {{ $categoria->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Filtro por estado -->
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-toggle-on"></i> Estado
                                    </label>
                                    <select class="form-select" id="activoFilter">
                                        <option value="">Todos</option>
                                        <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activos</option>
                                        <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivos</option>
                                    </select>
                                </div>
                                
                                <!-- Filtro por stock -->
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-boxes"></i> Stock
                                    </label>
                                    <select class="form-select" id="stockFilter">
                                        <option value="">Todos</option>
                                        <option value="bajo">Stock Bajo</option>
                                        <option value="critico">Stock Crítico</option>
                                        <option value="agotado">Agotados</option>
                                    </select>
                                </div>
                                
                                <!-- Acciones -->
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-gear"></i> Acciones
                                    </label>
                                    <div class="d-grid">
                                        <button class="btn btn-primary" id="applyFilters">
                                            <i class="bi bi-funnel"></i> Filtrar
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Acciones rápidas -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button class="btn btn-outline-warning btn-sm" onclick="filtrarStockBajo()">
                                            <i class="bi bi-exclamation-triangle"></i> Ver Stock Bajo
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" onclick="filtrarAgotados()">
                                            <i class="bi bi-x-circle"></i> Ver Agotados
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="filtrarServicios()">
                                            <i class="bi bi-gear"></i> Solo Servicios
                                        </button>
                                        <button class="btn btn-outline-success btn-sm" onclick="limpiarFiltros()">
                                            <i class="bi bi-arrow-clockwise"></i> Limpiar Filtros
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de resultados -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">
                            Mostrando {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} 
                            de {{ $products->total() }} productos
                        </span>
                        <div class="text-end">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> 
                                Haz clic en cualquier celda para editar
                            </small>
                        </div>
                    </div>

                    <!-- Tabla de productos -->
                    <div class="table-responsive" id="productsTableContainer">
                        @include('products.table')
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar producto -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Nuevo Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addProductForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="200">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoria_id" class="form-label">Categoría</label>
                                <select class="form-select" id="categoria_id" name="categoria_id">
                                    <option value="">Sin categoría</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="precio_compra" class="form-label">
                                    <i class="bi bi-arrow-down-circle text-danger"></i> Precio Compra
                                </label>
                                <input type="number" class="form-control" id="precio_compra" name="precio_compra" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="precio_venta" class="form-label">
                                    <i class="bi bi-arrow-up-circle text-success"></i> Precio Venta *
                                </label>
                                <input type="number" class="form-control" id="precio_venta" name="precio_venta" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="codigo" class="form-label">
                                    <i class="bi bi-upc-scan"></i> Código
                                </label>
                                <input type="text" class="form-control" id="codigo" name="codigo" maxlength="50" placeholder="Automático si vacío">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Fila para imagen URL -->
                    <div class="mb-3">
                        <label for="imagen_url" class="form-label">
                            <i class="bi bi-image"></i> URL de Imagen
                        </label>
                        <input type="url" class="form-control" id="imagen_url" name="imagen_url" maxlength="500" placeholder="https://ejemplo.com/imagen.jpg">
                        <div class="form-text">Opcional: URL de la imagen del producto</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock_actual" class="form-label">Stock Actual *</label>
                                <input type="number" class="form-control" id="stock_actual" name="stock_actual" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                                <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" min="0" value="5">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="unidad_medida" class="form-label">Unidad de Medida</label>
                                <input type="text" class="form-control" id="unidad_medida" name="unidad_medida" maxlength="20" placeholder="ej: unidad, litro, kilo">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" checked>
                        <label class="form-check-label" for="activo">
                            Producto activo
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para importar inventario -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-upload text-success"></i> 
                    Importar Inventario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="importForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Instrucciones -->
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Instrucciones de Importación</h6>
                        <ul class="mb-0">
                            <li>Sube un archivo <strong>CSV</strong> o <strong>Excel (.xlsx)</strong></li>
                            <li>La primera fila debe contener los encabezados</li>
                            <li>Columnas requeridas: <code>nombre</code>, <code>precio</code>, <code>stock</code></li>
                            <li>Columnas opcionales: <code>codigo</code>, <code>descripcion</code>, <code>categoria</code>, <code>stock_minimo</code></li>
                        </ul>
                    </div>

                    <!-- Plantilla de ejemplo -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-download"></i> Descargar Plantilla
                        </label>
                        <div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="descargarPlantilla()">
                                <i class="bi bi-file-earmark-spreadsheet"></i> Plantilla CSV
                            </button>
                            <small class="text-muted ms-2">Descarga una plantilla con el formato correcto</small>
                        </div>
                    </div>

                    <!-- Selección de archivo -->
                    <div class="mb-3">
                        <label for="archivoImport" class="form-label">
                            <i class="bi bi-file-earmark-arrow-up"></i> Seleccionar Archivo *
                        </label>
                        <input type="file" 
                               class="form-control" 
                               id="archivoImport" 
                               name="archivo" 
                               accept=".csv,.xlsx,.xls"
                               required>
                        <div class="form-text">
                            Formatos soportados: CSV, Excel (.xlsx, .xls)
                        </div>
                    </div>

                    <!-- Opciones de importación -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="actualizarExistentes" name="actualizar_existentes" checked>
                                <label class="form-check-label" for="actualizarExistentes">
                                    Actualizar productos existentes
                                </label>
                                <small class="form-text text-muted d-block">Si el código ya existe, actualizar información</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="crearCategorias" name="crear_categorias" checked>
                                <label class="form-check-label" for="crearCategorias">
                                    Crear categorías automáticamente
                                </label>
                                <small class="form-text text-muted d-block">Si la categoría no existe, crearla</small>
                            </div>
                        </div>
                    </div>

                    <!-- Preview de datos (se llena dinámicamente) -->
                    <div id="previewContainer" class="mt-3" style="display: none;">
                        <h6><i class="bi bi-eye"></i> Vista Previa</h6>
                        <div class="table-responsive" style="max-height: 300px;">
                            <table class="table table-sm table-bordered">
                                <thead class="table-dark" id="previewHeaders"></thead>
                                <tbody id="previewBody"></tbody>
                            </table>
                        </div>
                        <div class="text-muted">
                            <small>Mostrando las primeras 5 filas. Total de registros: <span id="totalRegistros">0</span></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-info" id="previewBtn" onclick="previsualizarArchivo()" disabled>
                        <i class="bi bi-eye"></i> Vista Previa
                    </button>
                    <button type="submit" class="btn btn-success" id="importBtn" disabled>
                        <i class="bi bi-upload"></i> Importar Datos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/products.js') }}"></script>
@endsection

@section('styles')
<style>
.editable {
    cursor: pointer;
    position: relative;
}

.editable:hover {
    background-color: #f8f9fa !important;
}

.editing {
    background-color: #fff3cd !important;
}

.table-responsive {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

.product-row.inactive {
    opacity: 0.6;
}

.status-badge {
    cursor: pointer;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.spinner {
    width: 20px;
    height: 20px;
    border: 2px solid #ccc;
    border-top: 2px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Nuevos estilos para inventario */
.table-warning {
    background-color: #fff3cd !important;
}

.table-danger {
    background-color: #f8d7da !important;
}

.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar estadísticas
    actualizarEstadisticas();
    
    // Event listeners para filtros rápidos
    document.getElementById('searchInput').addEventListener('input', debounce(filtrarProductos, 300));
    
    // Manejar toggle de estado
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('status-toggle')) {
            toggleProductStatus(e.target);
        }
    });
});

// Funciones de filtros rápidos
function filtrarStockBajo() {
    document.getElementById('stockFilter').value = 'bajo';
    filtrarProductos();
}

function filtrarAgotados() {
    document.getElementById('stockFilter').value = 'agotado';
    filtrarProductos();
}

function filtrarServicios() {
    // Implementar filtro para servicios si es necesario
    console.log('Filtrar servicios');
}

function limpiarFiltros() {
    document.getElementById('searchInput').value = '';
    document.getElementById('categoriaFilter').value = '';
    document.getElementById('activoFilter').value = '';
    document.getElementById('stockFilter').value = '';
    filtrarProductos();
}

// Función principal de filtrado
function filtrarProductos() {
    const search = document.getElementById('searchInput').value;
    const categoria = document.getElementById('categoriaFilter').value;
    const activo = document.getElementById('activoFilter').value;
    const stock = document.getElementById('stockFilter').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (categoria) params.append('categoria_id', categoria);
    if (activo) params.append('activo', activo);
    if (stock) params.append('stock', stock);
    
    // Recargar tabla con filtros
    fetch(`{{ route('productos.index') }}?ajax=1&${params.toString()}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('productsTableContainer').innerHTML = html;
            actualizarEstadisticas();
        })
        .catch(error => {
            console.error('Error al filtrar productos:', error);
            mostrarToast('Error al filtrar productos', 'error');
        });
}

// Toggle estado de producto
function toggleProductStatus(toggle) {
    const productId = toggle.dataset.id;
    const isActive = toggle.checked;
    
    fetch(`/productos/${productId}/toggle-status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ activo: isActive })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarToast('Estado del producto actualizado', 'success');
            toggle.title = isActive ? 'Desactivar producto' : 'Activar producto';
        } else {
            toggle.checked = !isActive; // Revertir cambio
            mostrarToast('Error al cambiar estado', 'error');
        }
    })
    .catch(error => {
        toggle.checked = !isActive; // Revertir cambio
        mostrarToast('Error al cambiar estado', 'error');
    });
}

// Actualizar estadísticas en tiempo real
function actualizarEstadisticas() {
    const filas = document.querySelectorAll('.product-row');
    let stockBajo = 0;
    let productosActivos = 0;
    let valorInventario = 0;
    
    filas.forEach(fila => {
        if (fila.classList.contains('table-warning') || fila.classList.contains('table-danger')) {
            stockBajo++;
        }
        
        if (!fila.classList.contains('table-secondary')) {
            productosActivos++;
        }
        
        // Calcular valor aproximado (precio * stock)
        const precio = parseFloat(fila.querySelector('[data-field="precio"]').textContent.replace('$', '').replace(',', '').replace('.', ''));
        const stock = parseInt(fila.querySelector('[data-field="stock"]').textContent) || 0;
        valorInventario += precio * stock;
    });
    
    // Actualizar cards de estadísticas
    const stockBajoElement = document.querySelector('.stock-bajo-count');
    const productosActivosElement = document.querySelector('.productos-activos-count');
    const valorInventarioElement = document.querySelector('.valor-inventario');
    
    if (stockBajoElement) stockBajoElement.textContent = stockBajo;
    if (productosActivosElement) productosActivosElement.textContent = productosActivos;
    if (valorInventarioElement) valorInventarioElement.textContent = '$' + valorInventario.toLocaleString();
}

// Exportar inventario
function exportarInventario() {
    mostrarToast('Preparando exportación...', 'info');
    
    // Crear y descargar CSV
    const filas = document.querySelectorAll('.product-row');
    let csv = 'ID,Producto,Categoria,Precio,Stock Actual,Stock Minimo,Codigo,Tipo,Estado\n';
    
    filas.forEach(fila => {
        const id = fila.dataset.id;
        const nombre = fila.querySelector('[data-field="nombre"]').textContent;
        const categoria = fila.querySelector('td:nth-child(3) .badge').textContent;
        const precio = fila.querySelector('[data-field="precio"]').textContent.replace('$', '');
        const stock = fila.querySelector('[data-field="stock"]').textContent;
        const stockMin = fila.querySelector('[data-field="stock_minimo"]').textContent;
        const codigo = fila.querySelector('[data-field="codigo"]').textContent;
        const tipo = fila.querySelector('td:nth-child(8) .badge').textContent;
        const estado = fila.querySelector('.status-toggle').checked ? 'Activo' : 'Inactivo';
        
        csv += `${id},"${nombre}","${categoria}","${precio}","${stock}","${stockMin}","${codigo}","${tipo}","${estado}"\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `inventario_${new Date().toISOString().slice(0, 10)}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
    
    mostrarToast('Inventario exportado correctamente', 'success');
}

// ===== FUNCIONES DE IMPORTACIÓN =====

// Event listener para el archivo
document.getElementById('archivoImport').addEventListener('change', function(e) {
    const archivo = e.target.files[0];
    const previewBtn = document.getElementById('previewBtn');
    const importBtn = document.getElementById('importBtn');
    
    if (archivo) {
        previewBtn.disabled = false;
        // Resetear vista previa
        document.getElementById('previewContainer').style.display = 'none';
        importBtn.disabled = true;
    } else {
        previewBtn.disabled = true;
        importBtn.disabled = true;
    }
});

// Descargar plantilla CSV
function descargarPlantilla() {
    const csvContent = 'nombre,codigo,descripcion,categoria,precio_compra,precio_venta,stock,stock_minimo,imagen_url\n' +
                      'Coca Cola,CC001,Bebida gaseosa,Bebidas,1500,2500,50,10,https://ejemplo.com/cocacola.jpg\n' +
                      'Cerveza,BC001,Cerveza nacional,Bebidas,2000,3500,30,5,https://ejemplo.com/cerveza.jpg\n' +
                      'Papas Fritas,PF001,Snack salado,Snacks,1000,2000,25,8,\n' +
                      'Tiempo Mesa 1 Hora,TM001,Alquiler mesa por hora,Servicios,,5000,999,0,';
    
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'plantilla_inventario.csv';
    a.click();
    window.URL.revokeObjectURL(url);
    
    mostrarToast('Plantilla descargada correctamente', 'success');
}

// Previsualizar archivo antes de importar
function previsualizarArchivo() {
    const archivo = document.getElementById('archivoImport').files[0];
    if (!archivo) return;
    
    const formData = new FormData();
    formData.append('archivo', archivo);
    
    mostrarToast('Analizando archivo...', 'info');
    
    fetch('/productos/preview-import', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarVistaPrevia(data.data);
            document.getElementById('importBtn').disabled = false;
            mostrarToast('Archivo analizado correctamente', 'success');
        } else {
            mostrarToast('Error al analizar archivo: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarToast('Error al procesar archivo', 'error');
    });
}

// Mostrar vista previa de los datos
function mostrarVistaPrevia(data) {
    const previewContainer = document.getElementById('previewContainer');
    const headersContainer = document.getElementById('previewHeaders');
    const bodyContainer = document.getElementById('previewBody');
    const totalRegistros = document.getElementById('totalRegistros');
    
    // Limpiar contenido anterior
    headersContainer.innerHTML = '';
    bodyContainer.innerHTML = '';
    
    if (data.length === 0) {
        previewContainer.style.display = 'none';
        return;
    }
    
    // Crear encabezados
    const headers = Object.keys(data[0]);
    const headerRow = headers.map(header => `<th>${header}</th>`).join('');
    headersContainer.innerHTML = `<tr>${headerRow}</tr>`;
    
    // Crear filas (máximo 5 para preview)
    const previewData = data.slice(0, 5);
    const rows = previewData.map(row => {
        const cells = headers.map(header => `<td>${row[header] || ''}</td>`).join('');
        return `<tr>${cells}</tr>`;
    }).join('');
    bodyContainer.innerHTML = rows;
    
    // Mostrar total
    totalRegistros.textContent = data.length;
    previewContainer.style.display = 'block';
}

// Procesar importación
document.getElementById('importForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const importBtn = document.getElementById('importBtn');
    
    // Deshabilitar botón durante importación
    importBtn.disabled = true;
    importBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Importando...';
    
    mostrarToast('Iniciando importación de datos...', 'info');
    
    fetch('/productos/import', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarToast(`Importación completada: ${data.procesados} productos procesados`, 'success');
            
            // Cerrar modal y recargar tabla
            const modal = bootstrap.Modal.getInstance(document.getElementById('importModal'));
            modal.hide();
            
            // Recargar página para mostrar nuevos productos
            setTimeout(() => {
                window.location.reload();
            }, 1500);
            
        } else {
            mostrarToast('Error en importación: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarToast('Error al procesar importación', 'error');
    })
    .finally(() => {
        // Rehabilitar botón
        importBtn.disabled = false;
        importBtn.innerHTML = '<i class="bi bi-upload"></i> Importar Datos';
    });
});

// ===== FUNCIONES PARA IMÁGENES =====

// Mostrar imagen completa en modal
function mostrarImagenCompleta(url, nombre) {
    const modalHtml = `
        <div class="modal fade" id="imagenModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-image"></i> ${nombre}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${url}" alt="${nombre}" class="img-fluid rounded shadow" style="max-height: 500px;">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <a href="${url}" target="_blank" class="btn btn-primary">
                            <i class="bi bi-box-arrow-up-right"></i> Abrir en Nueva Pestaña
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal anterior si existe
    const existingModal = document.getElementById('imagenModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Agregar y mostrar nuevo modal
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('imagenModal'));
    modal.show();
}

// Editar imagen de producto
function editarImagen(productId) {
    const url = prompt('Ingresa la URL de la imagen:');
    if (url && url.trim()) {
        fetch(`/productos/${productId}/field`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                field: 'imagen_url',
                value: url.trim()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarToast('Imagen actualizada correctamente', 'success');
                // Recargar tabla para mostrar la imagen
                filtrarProductos();
            } else {
                mostrarToast('Error al actualizar imagen', 'error');
            }
        })
        .catch(error => {
            mostrarToast('Error al procesar solicitud', 'error');
        });
    }
}

// Función debounce para optimizar búsquedas
function debounce(func, delay) {
    let debounceTimer;
    return function() {
        const context = this;
        const args = arguments;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => func.apply(context, args), delay);
    }
}

// Función para mostrar notificaciones toast
function mostrarToast(mensaje, tipo = 'info') {
    // Crear toast dinámico
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${tipo === 'error' ? 'danger' : tipo === 'success' ? 'success' : 'primary'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${mensaje}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    // Agregar al DOM y mostrar
    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remover después de que se oculte
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}
</script>
@endsection