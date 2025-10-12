@extends('layouts.app')

@section('title', 'Inventario Masivo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-boxes text-primary"></i> 
                            Inventario Masivo
                        </h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Volver al Inventario
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Barra de herramientas -->
                <div class="card-body border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-success btn-sm" id="agregarFila">
                                    <i class="bi bi-plus-lg"></i> Agregar Producto
                                </button>
                                <button class="btn btn-danger btn-sm" id="eliminarSeleccionados" disabled>
                                    <i class="bi bi-trash"></i> Eliminar Seleccionados
                                </button>
                                <button class="btn btn-warning btn-sm" id="duplicarFila" disabled>
                                    <i class="bi bi-copy"></i> Duplicar
                                </button>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-outline-primary btn-sm" id="seleccionarTodo">
                                        <i class="bi bi-check-all"></i> Seleccionar Todo
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" id="limpiarSeleccion">
                                        <i class="bi bi-x-lg"></i> Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="d-flex gap-2 justify-content-end flex-wrap">
                                <button class="btn btn-info btn-sm" id="guardarTodo">
                                    <i class="bi bi-save"></i> Guardar Cambios
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" id="deshacerCambios">
                                    <i class="bi bi-arrow-counterclockwise"></i> Deshacer
                                </button>
                                <span class="badge bg-secondary align-self-center" id="contadorFilas">0 productos</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla tipo Excel -->
                <div class="card-body p-0">
                    <div class="table-container" style="height: 70vh; overflow: auto;">
                        <table class="table table-bordered mb-0" id="tablaInventario">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th width="40px" class="text-center">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th width="60px" class="text-center">#</th>
                                    <th width="200px">
                                        <i class="bi bi-box"></i> Nombre *
                                    </th>
                                    <th width="80px">
                                        <i class="bi bi-upc-scan"></i> Código
                                    </th>
                                    <th width="150px">
                                        <i class="bi bi-tags"></i> Categoría
                                    </th>
                                    <th width="100px">
                                        <i class="bi bi-arrow-down-circle text-danger"></i> P. Compra
                                    </th>
                                    <th width="100px">
                                        <i class="bi bi-arrow-up-circle text-success"></i> P. Venta *
                                    </th>
                                    <th width="80px">
                                        <i class="bi bi-percent"></i> Margen
                                    </th>
                                    <th width="80px">
                                        <i class="bi bi-boxes"></i> Stock
                                    </th>
                                    <th width="80px">
                                        <i class="bi bi-exclamation-triangle"></i> S.Mín
                                    </th>
                                    <th width="250px">
                                        <i class="bi bi-text-paragraph"></i> Descripción
                                    </th>
                                    <th width="200px">
                                        <i class="bi bi-image"></i> Imagen URL
                                    </th>
                                    <th width="80px">
                                        <i class="bi bi-toggle-on"></i> Activo
                                    </th>
                                    <th width="60px">
                                        <i class="bi bi-gear"></i> Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tablaInventarioBody">
                                <!-- Las filas se cargarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer con estadísticas -->
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> 
                                <strong>Total:</strong> <span id="totalProductos">0</span> productos
                            </small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">
                                <i class="bi bi-pencil"></i> 
                                <strong>Modificados:</strong> <span id="productosModificados">0</span>
                            </small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">
                                <i class="bi bi-plus-circle text-success"></i> 
                                <strong>Nuevos:</strong> <span id="productosNuevos">0</span>
                            </small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">
                                <i class="bi bi-currency-dollar"></i> 
                                <strong>Valor Total:</strong> $<span id="valorTotal">0</span>
                            </small>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
/* Estilos tipo Excel */
.table-container {
    border: 2px solid #dee2e6;
    border-radius: 0.375rem;
}

#tablaInventario {
    font-size: 0.875rem;
    font-family: 'Courier New', monospace;
}

#tablaInventario th {
    background-color: #495057 !important;
    color: white;
    font-weight: 600;
    white-space: nowrap;
    border: 1px solid #6c757d;
    padding: 8px;
    user-select: none;
}

#tablaInventario td {
    padding: 4px;
    border: 1px solid #dee2e6;
    vertical-align: middle;
    position: relative;
}

/* Celdas editables */
.celda-editable {
    background-color: white;
    border: 2px solid transparent;
    cursor: text;
    min-height: 30px;
    outline: none;
    width: 100%;
    border-radius: 3px;
    padding: 4px;
    resize: none;
}

.celda-editable:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.celda-editable.modificado {
    background-color: #fff3cd;
    border-color: #ffc107;
}

.celda-editable.nuevo {
    background-color: #d1e7dd;
    border-color: #198754;
}

.celda-editable.error {
    background-color: #f8d7da;
    border-color: #dc3545;
}

/* Filas */
.fila-seleccionada {
    background-color: #e3f2fd !important;
}

.fila-nueva {
    background-color: #f0fff4;
}

.fila-modificada {
    background-color: #fffbf0;
}

/* Select personalizado */
.select-categoria {
    width: 100%;
    border: 2px solid transparent;
    border-radius: 3px;
    padding: 4px;
    font-size: 0.875rem;
}

.select-categoria:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Checkbox personalizado */
.checkbox-activo {
    transform: scale(1.2);
    margin: 0;
}

/* Botón de acción en fila */
.btn-fila {
    padding: 2px 6px;
    font-size: 0.75rem;
    border-radius: 2px;
}

/* Margen calculado */
.margen-positivo {
    color: #198754;
    font-weight: bold;
}

.margen-negativo {
    color: #dc3545;
    font-weight: bold;
}

/* Sticky header */
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 100;
}

/* Loader */
.cargando {
    position: relative;
}

.cargando::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 16px;
    height: 16px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #0d6efd;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .table-container {
        font-size: 0.75rem;
    }
    
    #tablaInventario th,
    #tablaInventario td {
        padding: 2px;
    }
}
</style>
@endsection

@section('scripts')
<script>
// Variables globales
let productos = [];
let productosModificados = new Set();
let productosNuevos = new Set();
let categorias = [];
let contadorFilas = 0;

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== INVENTARIO MASIVO INICIANDO ===');
    
    // Inicializar
    console.log('Cargando categorías...');
    cargarCategorias();
    
    console.log('Cargando productos...');
    cargarProductos();
    
    // Event listeners
    console.log('Configurando event listeners...');
    setupEventListeners();
    
    console.log('=== INICIALIZACIÓN COMPLETADA ===');
});

// Configurar event listeners
function setupEventListeners() {
    // Botones principales
    document.getElementById('agregarFila').addEventListener('click', agregarNuevaFila);
    document.getElementById('eliminarSeleccionados').addEventListener('click', eliminarFilasSeleccionadas);
    document.getElementById('duplicarFila').addEventListener('click', duplicarFilaSeleccionada);
    document.getElementById('seleccionarTodo').addEventListener('click', seleccionarTodasLasFilas);
    document.getElementById('limpiarSeleccion').addEventListener('click', limpiarSeleccion);
    document.getElementById('guardarTodo').addEventListener('click', guardarTodosLosCambios);
    document.getElementById('deshacerCambios').addEventListener('click', deshacerCambios);
    
    // Select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.fila-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        actualizarBotonesSeleccion();
    });
}

// Cargar categorías
async function cargarCategorias() {
    try {
        const response = await fetch('/api/categorias');
        if (response.ok) {
            let data = await response.json();
            
            // Manejar diferentes formatos de respuesta
            if (data.value && Array.isArray(data.value)) {
                categorias = data.value;
            } else if (Array.isArray(data)) {
                categorias = data;
            } else {
                categorias = [];
            }
            
            console.log('Categorías cargadas:', categorias.length, categorias);
        } else {
            // Categorías por defecto si no hay API
            categorias = [
                {id: 1, nombre: 'Bebidas'},
                {id: 2, nombre: 'Snacks'},
                {id: 3, nombre: 'Servicios'},
                {id: 4, nombre: 'Otros'}
            ];
        }
    } catch (error) {
        console.error('Error al cargar categorías:', error);
        categorias = [{id: null, nombre: 'Sin categoría'}];
    }
}

// Cargar productos existentes
async function cargarProductos() {
    console.log('Iniciando carga de productos...');
    try {
        mostrarCargando(true);
        await cargarProductosDesdeAPI();
    } catch (error) {
        console.error('Error al cargar productos:', error);
        // Crear fila inicial vacía
        agregarNuevaFila();
    } finally {
        mostrarCargando(false);
    }
}

// Cargar productos desde API
async function cargarProductosDesdeAPI() {
    try {
        const response = await fetch('/api/productos');
        if (response.ok) {
            let data = await response.json();
            
            // Manejar diferentes formatos de respuesta
            if (data.value && Array.isArray(data.value)) {
                productos = data.value;
            } else if (Array.isArray(data)) {
                productos = data;
            } else {
                productos = [];
            }
            
            console.log('Productos cargados:', productos.length, productos);
        } else {
            console.error('Error al cargar productos:', response.statusText);
            productos = [];
        }
    } catch (error) {
        console.error('Error en API de productos:', error);
        productos = [];
    }
    
    // Si no hay productos, agregar una fila vacía
    if (productos.length === 0) {
        agregarNuevaFila();
    } else {
        renderizarTabla();
    }
}

// Renderizar toda la tabla
function renderizarTabla() {
    console.log('Renderizando tabla con productos:', productos);
    const tbody = document.getElementById('tablaInventarioBody');
    
    if (!tbody) {
        console.error('No se encontró el tbody con id tablaInventarioBody');
        return;
    }
    
    tbody.innerHTML = '';
    
    productos.forEach((producto, index) => {
        console.log('Creando fila para producto:', producto);
        const fila = crearFilaProducto(producto, index);
        tbody.appendChild(fila);
    });
    
    console.log('Tabla renderizada con', productos.length, 'productos');
    actualizarEstadisticas();
    
    // Configurar event listeners para edición de celdas después del renderizado
    configurarEventListenersEdicion();
}

// Configurar event listeners para edición de celdas
function configurarEventListenersEdicion() {
    const tbody = document.getElementById('tablaInventarioBody');
    
    // Event listener para cambios en inputs
    tbody.addEventListener('input', function(e) {
        if (e.target.classList.contains('celda-editable') || 
            e.target.classList.contains('select-categoria') ||
            e.target.classList.contains('checkbox-activo')) {
            actualizarProductoDesdeInput(e.target);
        }
    });

    tbody.addEventListener('change', function(e) {
        if (e.target.classList.contains('celda-editable') || 
            e.target.classList.contains('select-categoria') ||
            e.target.classList.contains('checkbox-activo')) {
            actualizarProductoDesdeInput(e.target);
        }
    });
}

// Función para actualizar producto desde input
function actualizarProductoDesdeInput(input) {
    const campo = input.dataset.field;
    const filaIndex = parseInt(input.dataset.index);
    let nuevoValor = input.value;
    
    // Validaciones básicas
    if (campo === 'nombre' && !nuevoValor.trim()) {
        return; // No actualizar si está vacío
    }
    
    if (['precio_compra', 'precio_venta', 'stock', 'stock_minimo'].includes(campo)) {
        if (nuevoValor !== '' && (isNaN(nuevoValor) || parseFloat(nuevoValor) < 0)) {
            return; // No actualizar si es inválido
        }
        nuevoValor = nuevoValor !== '' ? parseFloat(nuevoValor) : null;
    }
    
    // Actualizar el producto en el array
    if (campo === 'categoria_id') {
        productos[filaIndex][campo] = nuevoValor !== '' ? parseInt(nuevoValor) : null;
    } else if (campo === 'activo') {
        productos[filaIndex][campo] = input.type === 'checkbox' ? input.checked : nuevoValor === 'true';
    } else {
        productos[filaIndex][campo] = nuevoValor;
    }
    
    // Marcar producto como modificado
    productos[filaIndex].modificado = true;
    
    // Actualizar precios calculados si es necesario
    if (['precio_compra', 'precio_venta'].includes(campo)) {
        actualizarPreciosCalculados(filaIndex);
    }
    
    actualizarEstadisticas();
    marcarFilaModificada(input.closest('tr'));
}

function actualizarPreciosCalculados(filaIndex) {
    const producto = productos[filaIndex];
    const fila = document.querySelector(`tr[data-index="${filaIndex}"]`);
    
    if (producto.precio_compra && producto.precio_venta) {
        const margen = ((producto.precio_venta - producto.precio_compra) / producto.precio_venta * 100).toFixed(1);
        const celdaMargen = fila.querySelector('[data-campo="margen"]');
        if (celdaMargen) {
            celdaMargen.textContent = `${margen}%`;
        }
    }
}

function marcarFilaModificada(fila) {
    fila.classList.add('table-warning');
    // Marcar inputs como modificados
    const inputs = fila.querySelectorAll('.celda-editable, .select-categoria, .checkbox-activo');
    inputs.forEach(input => {
        if (!input.classList.contains('nuevo')) {
            input.classList.add('modificado');
        }
    });
}

// Crear fila de producto
function crearFilaProducto(producto, index) {
    console.log('Creando fila para:', producto, 'index:', index);
    
    const fila = document.createElement('tr');
    fila.className = producto.es_nuevo ? 'fila-nueva' : '';
    fila.dataset.index = index;
    
    const margen = calcularMargen(producto.precio_compra, producto.precio_venta);
    console.log('Margen calculado:', margen);
    
    // Crear celdas de forma segura
    const celdas = [
        createCheckboxCell(),
        createIdCell(producto.id),
        createInputCell('text', producto.nombre || '', 'nombre', index, 'Nombre del producto', true),
        createInputCell('text', producto.codigo || '', 'codigo', index, 'Código'),
        createSelectCell(producto.categoria_id, 'categoria_id', index),
        createInputCell('number', producto.precio_compra || '', 'precio_compra', index, '0.00', false, {step: '0.01', min: '0'}),
        createInputCell('number', producto.precio_venta || '', 'precio_venta', index, '0.00', true, {step: '0.01', min: '0'}),
        createMargenCell(margen),
        createInputCell('number', producto.stock || 0, 'stock', index, '0', false, {min: '0'}),
        createInputCell('number', producto.stock_minimo || 5, 'stock_minimo', index, '5', false, {min: '0'}),
        createTextareaCell(producto.descripcion || '', 'descripcion', index, 'Descripción...'),
        createInputCell('url', producto.imagen_url || '', 'imagen_url', index, 'https://...'),
        createCheckboxActiveCell(producto.activo, 'activo', index),
        createDeleteButtonCell(index)
    ];
    
    celdas.forEach(celda => fila.appendChild(celda));
    
    return fila;
}

// Funciones auxiliares para crear celdas
function createCheckboxCell() {
    const td = document.createElement('td');
    td.className = 'text-center';
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.className = 'form-check-input fila-checkbox';
    checkbox.onchange = actualizarBotonesSeleccion;
    td.appendChild(checkbox);
    return td;
}

function createIdCell(id) {
    const td = document.createElement('td');
    td.className = 'text-center';
    const small = document.createElement('small');
    small.className = 'text-muted';
    small.textContent = id || 'NUEVO';
    td.appendChild(small);
    return td;
}

function createInputCell(type, value, field, index, placeholder = '', required = false, attrs = {}) {
    const td = document.createElement('td');
    const input = document.createElement('input');
    input.type = type;
    input.className = 'celda-editable';
    input.value = value;
    input.setAttribute('data-field', field);
    input.setAttribute('data-index', index);
    if (placeholder) input.placeholder = placeholder;
    if (required) input.required = true;
    
    Object.keys(attrs).forEach(key => {
        input.setAttribute(key, attrs[key]);
    });
    
    td.appendChild(input);
    return td;
}

function createSelectCell(selectedValue, field, index) {
    const td = document.createElement('td');
    const select = document.createElement('select');
    select.className = 'select-categoria';
    select.setAttribute('data-field', field);
    select.setAttribute('data-index', index);
    
    // Opción por defecto
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = 'Sin categoría';
    select.appendChild(defaultOption);
    
    // Opciones de categorías
    categorias.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.id;
        option.textContent = cat.nombre;
        if (cat.id == selectedValue) {
            option.selected = true;
        }
        select.appendChild(option);
    });
    
    td.appendChild(select);
    return td;
}

function createMargenCell(margen) {
    const td = document.createElement('td');
    td.className = 'text-center';
    const small = document.createElement('small');
    small.className = margen >= 0 ? 'margen-positivo' : 'margen-negativo';
    small.textContent = margen !== null ? margen.toFixed(1) + '%' : '-';
    td.appendChild(small);
    return td;
}

function createTextareaCell(value, field, index, placeholder = '') {
    const td = document.createElement('td');
    const textarea = document.createElement('textarea');
    textarea.className = 'celda-editable';
    textarea.value = value;
    textarea.setAttribute('data-field', field);
    textarea.setAttribute('data-index', index);
    textarea.rows = 1;
    if (placeholder) textarea.placeholder = placeholder;
    td.appendChild(textarea);
    return td;
}

function createCheckboxActiveCell(checked, field, index) {
    const td = document.createElement('td');
    td.className = 'text-center';
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.className = 'checkbox-activo';
    checkbox.checked = checked;
    checkbox.setAttribute('data-field', field);
    checkbox.setAttribute('data-index', index);
    td.appendChild(checkbox);
    return td;
}

function createDeleteButtonCell(index) {
    const td = document.createElement('td');
    td.className = 'text-center';
    const button = document.createElement('button');
    button.className = 'btn btn-danger btn-fila';
    button.title = 'Eliminar';
    button.onclick = () => eliminarFila(index);
    
    const icon = document.createElement('i');
    icon.className = 'bi bi-trash';
    button.appendChild(icon);
    
    td.appendChild(button);
    return td;
}



// Validar campo
function validarCampo(elemento) {
    const valor = elemento.value;
    const field = elemento.dataset.field;
    let valido = true;
    
    // Validaciones específicas
    switch (field) {
        case 'nombre':
            valido = valor.trim().length > 0;
            break;
        case 'precio_venta':
            valido = parseFloat(valor) > 0;
            break;
        case 'imagen_url':
            if (valor) {
                valido = /^https?:\/\/.+/.test(valor);
            }
            break;
    }
    
    if (valido) {
        elemento.classList.remove('error');
    } else {
        elemento.classList.add('error');
    }
    
    return valido;
}

// Calcular margen
function calcularMargen(precioCompra, precioVenta) {
    const compra = parseFloat(precioCompra) || 0;
    const venta = parseFloat(precioVenta) || 0;
    
    if (compra <= 0 || venta <= 0) {
        return null;
    }
    return ((venta - compra) / compra) * 100;
}

// Actualizar margen en la fila
function actualizarMargen(index) {
    const fila = document.querySelector(`tr[data-index="${index}"]`);
    if (fila) {
        const producto = productos[index];
        const margen = calcularMargen(producto.precio_compra, producto.precio_venta);
        const celdaMargen = fila.querySelector('td:nth-child(8) small');
        
        if (celdaMargen) {
            celdaMargen.textContent = margen !== null ? margen.toFixed(1) + '%' : '-';
            celdaMargen.className = margen >= 0 ? 'margen-positivo' : 'margen-negativo';
        }
    }
}

// Agregar nueva fila
function agregarNuevaFila() {
    const nuevoProducto = {
        id: null,
        nombre: '',
        codigo: '',
        categoria_id: '',
        precio_compra: '',
        precio_venta: '',
        stock: 0,
        stock_minimo: 5,
        descripcion: '',
        imagen_url: '',
        activo: true,
        es_nuevo: true
    };
    
    productos.push(nuevoProducto);
    const index = productos.length - 1;
    productosNuevos.add(index);
    
    const tbody = document.getElementById('tablaInventarioBody');
    const nuevaFila = crearFilaProducto(nuevoProducto, index);
    tbody.appendChild(nuevaFila);
    
    // Focus en el primer campo
    const primerInput = nuevaFila.querySelector('.celda-editable');
    if (primerInput) primerInput.focus();
    
    actualizarEstadisticas();
}

// Eliminar fila
function eliminarFila(index) {
    if (confirm('¿Estás seguro de eliminar este producto?')) {
        productos.splice(index, 1);
        productosModificados.delete(index);
        productosNuevos.delete(index);
        
        // Reindexar
        reindexarProductos();
        renderizarTabla();
    }
}

// Eliminar filas seleccionadas
function eliminarFilasSeleccionadas() {
    const checkboxes = document.querySelectorAll('.fila-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('No hay filas seleccionadas');
        return;
    }
    
    if (confirm(`¿Estás seguro de eliminar ${checkboxes.length} producto(s)?`)) {
        const indices = Array.from(checkboxes).map(cb => 
            parseInt(cb.closest('tr').dataset.index)
        ).sort((a, b) => b - a); // Orden descendente para eliminar correctamente
        
        indices.forEach(index => {
            productos.splice(index, 1);
            productosModificados.delete(index);
            productosNuevos.delete(index);
        });
        
        reindexarProductos();
        renderizarTabla();
        actualizarBotonesSeleccion();
    }
}

// Duplicar fila seleccionada
function duplicarFilaSeleccionada() {
    const checkboxes = document.querySelectorAll('.fila-checkbox:checked');
    if (checkboxes.length !== 1) {
        alert('Selecciona exactamente una fila para duplicar');
        return;
    }
    
    const index = parseInt(checkboxes[0].closest('tr').dataset.index);
    const productoOriginal = { ...productos[index] };
    
    // Modificar para el duplicado
    productoOriginal.id = null;
    productoOriginal.nombre += ' (Copia)';
    productoOriginal.codigo = '';
    productoOriginal.es_nuevo = true;
    
    productos.push(productoOriginal);
    const nuevoIndex = productos.length - 1;
    productosNuevos.add(nuevoIndex);
    
    const tbody = document.getElementById('tablaInventarioBody');
    const nuevaFila = crearFilaProducto(productoOriginal, nuevoIndex);
    tbody.appendChild(nuevaFila);
    
    actualizarEstadisticas();
}

// Seleccionar todas las filas
function seleccionarTodasLasFilas() {
    const checkboxes = document.querySelectorAll('.fila-checkbox');
    checkboxes.forEach(cb => cb.checked = true);
    document.getElementById('selectAll').checked = true;
    actualizarBotonesSeleccion();
}

// Limpiar selección
function limpiarSeleccion() {
    const checkboxes = document.querySelectorAll('.fila-checkbox');
    checkboxes.forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    actualizarBotonesSeleccion();
}

// Actualizar botones según selección
function actualizarBotonesSeleccion() {
    const seleccionados = document.querySelectorAll('.fila-checkbox:checked').length;
    document.getElementById('eliminarSeleccionados').disabled = seleccionados === 0;
    document.getElementById('duplicarFila').disabled = seleccionados !== 1;
}

// Reindexar productos después de eliminar
function reindexarProductos() {
    // Reindexar sets de modificados y nuevos
    const nuevosModificados = new Set();
    const nuevosNuevos = new Set();
    
    productos.forEach((producto, newIndex) => {
        const oldIndex = producto._oldIndex || newIndex;
        if (productosModificados.has(oldIndex)) {
            nuevosModificados.add(newIndex);
        }
        if (productosNuevos.has(oldIndex)) {
            nuevosNuevos.add(newIndex);
        }
    });
    
    productosModificados = nuevosModificados;
    productosNuevos = nuevosNuevos;
}

// Guardar todos los cambios
async function guardarTodosLosCambios() {
    const cambios = [];
    const nuevos = [];
    
    // Validar todos los campos requeridos
    let hayErrores = false;
    productos.forEach((producto, index) => {
        if (!producto.nombre || !producto.precio_venta) {
            hayErrores = true;
            // Marcar campos con error
            const fila = document.querySelector(`tr[data-index="${index}"]`);
            if (fila) {
                if (!producto.nombre) {
                    fila.querySelector('[data-field="nombre"]').classList.add('error');
                }
                if (!producto.precio_venta) {
                    fila.querySelector('[data-field="precio_venta"]').classList.add('error');
                }
            }
        }
    });
    
    if (hayErrores) {
        alert('Por favor completa todos los campos requeridos (marcados en rojo)');
        return;
    }
    
    // Separar productos nuevos y modificados
    productosNuevos.forEach(index => {
        if (productos[index]) {
            nuevos.push(productos[index]);
        }
    });
    
    productosModificados.forEach(index => {
        if (productos[index] && !productos[index].es_nuevo) {
            cambios.push(productos[index]);
        }
    });
    
    try {
        mostrarCargando(true);
        
        // Guardar productos nuevos
        for (const producto of nuevos) {
            await guardarProducto(producto, true);
        }
        
        // Guardar productos modificados
        for (const producto of cambios) {
            await guardarProducto(producto, false);
        }
        
        // Limpiar estados
        productosModificados.clear();
        productosNuevos.clear();
        
        // Remover clases de modificado
        document.querySelectorAll('.modificado, .nuevo').forEach(el => {
            el.classList.remove('modificado', 'nuevo');
        });
        
        alert(`Cambios guardados exitosamente!\n${nuevos.length} productos nuevos\n${cambios.length} productos modificados`);
        
    } catch (error) {
        console.error('Error al guardar:', error);
        alert('Error al guardar los cambios. Revisa la consola para más detalles.');
    } finally {
        mostrarCargando(false);
    }
}

// Guardar un producto individual
async function guardarProducto(producto, esNuevo) {
    const url = esNuevo ? '/productos' : `/productos/${producto.id}`;
    const method = esNuevo ? 'POST' : 'PUT';
    
    const response = await fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(producto)
    });
    
    if (!response.ok) {
        throw new Error(`Error al guardar producto: ${response.statusText}`);
    }
    
    const resultado = await response.json();
    
    // Si es nuevo, actualizar ID
    if (esNuevo && resultado.product) {
        producto.id = resultado.product.id;
        producto.es_nuevo = false;
    }
    
    return resultado;
}

// Deshacer cambios
function deshacerCambios() {
    if (confirm('¿Estás seguro de deshacer todos los cambios no guardados?')) {
        cargarProductos();
        productosModificados.clear();
        productosNuevos.clear();
    }
}

// Actualizar estadísticas
function actualizarEstadisticas() {
    const total = productos.length;
    const modificados = productosModificados.size;
    const nuevos = productosNuevos.size;
    const valorTotal = productos.reduce((sum, p) => sum + ((p.precio_venta || 0) * (p.stock || 0)), 0);
    
    document.getElementById('contadorFilas').textContent = `${total} productos`;
    document.getElementById('totalProductos').textContent = total;
    document.getElementById('productosModificados').textContent = modificados;
    document.getElementById('productosNuevos').textContent = nuevos;
    document.getElementById('valorTotal').textContent = valorTotal.toLocaleString();
}

// Mostrar/ocultar loading
function mostrarCargando(mostrar) {
    const tabla = document.getElementById('tablaInventario');
    if (mostrar) {
        tabla.classList.add('cargando');
    } else {
        tabla.classList.remove('cargando');
    }
}

// Función para notificaciones toast
function mostrarToast(mensaje, tipo = 'info') {
    // Crear toast dinámico
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${tipo === 'error' ? 'danger' : tipo === 'success' ? 'success' : 'primary'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
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

// Función de debug para probar manualmente
function debugCargarProductos() {
    console.log('=== DEBUG: Forzando carga de productos ===');
    console.log('Categorías actuales:', categorias);
    console.log('Productos actuales:', productos);
    
    cargarProductosDesdeAPI().then(() => {
        console.log('Productos después de cargar:', productos);
        console.log('Total productos:', productos.length);
    });
}

// Función para agregar productos de prueba directamente
function agregarProductosPrueba() {
    console.log('=== AGREGANDO PRODUCTOS DE PRUEBA ===');
    
    const productosPrueba = [
        {
            id: 1,
            nombre: 'Agua',
            codigo: 'AGUA001',
            categoria_id: 1,
            precio_compra: 1000,
            precio_venta: 1500,
            stock: 30,
            stock_minimo: 10,
            descripcion: 'Agua natural',
            imagen_url: '',
            activo: true,
            es_nuevo: false
        },
        {
            id: 2,
            nombre: 'Cerveza',
            codigo: 'CERV001',
            categoria_id: 1,
            precio_compra: 2500,
            precio_venta: 3500,
            stock: 25,
            stock_minimo: 5,
            descripcion: 'Cerveza nacional',
            imagen_url: '',
            activo: true,
            es_nuevo: false
        },
        {
            id: 3,
            nombre: 'Coca Cola',
            codigo: 'COCA001',
            categoria_id: 1,
            precio_compra: 1800,
            precio_venta: 2500,
            stock: 50,
            stock_minimo: 10,
            descripcion: 'Gaseosa Coca Cola',
            imagen_url: '',
            activo: true,
            es_nuevo: false
        }
    ];
    
    productos = productosPrueba;
    renderizarTabla();
}

// Hacer funciones accesibles globalmente para debugging
window.debugCargarProductos = debugCargarProductos;
window.agregarProductosPrueba = agregarProductosPrueba;

// Test simple para verificar que el script se ejecuta
console.log('=== SCRIPT JAVASCRIPT CARGADO ===');
window.testFunction = function() {
    alert('JavaScript funciona correctamente!');
    return 'Test exitoso';
};

console.log('Funciones disponibles:', {
    debugCargarProductos: typeof debugCargarProductos,
    agregarProductosPrueba: typeof agregarProductosPrueba,
    testFunction: typeof window.testFunction
});
</script>
@endsection