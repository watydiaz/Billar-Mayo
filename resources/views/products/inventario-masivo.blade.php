@extends('layouts.app')

@section('title', 'Inventario Masivo')

@section('styles')
<style>
/* Estilos del inventario masivo */
.toolbar-inventario {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.tabla-inventario {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.tabla-inventario table {
    margin: 0;
}

.tabla-inventario th {
    background: #f8f9fa;
    font-weight: 600;
    border: none;
    padding: 12px 8px;
    font-size: 0.85rem;
}

.celda-editable, .select-categoria, .checkbox-activo {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 6px 8px;
    font-size: 0.9rem;
    width: 100%;
    transition: border-color 0.3s;
}

.celda-editable:focus, .select-categoria:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 5px rgba(0,123,255,0.3);
}

.celda-editable.modificado {
    background-color: #fff3cd;
    border-color: #ffc107;
}

.celda-editable.nuevo {
    background-color: #d1ecf1;
    border-color: #17a2b8;
}

.celda-editable.error {
    background-color: #f8d7da;
    border-color: #dc3545;
}

.margen-positivo {
    color: #28a745;
    font-weight: bold;
}

.margen-negativo {
    color: #dc3545;
    font-weight: bold;
}

.fila-nueva {
    background-color: rgba(23, 162, 184, 0.1);
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1);
}

.btn-fila {
    padding: 4px 8px;
    font-size: 0.8rem;
}

.estadisticas-footer {
    background: #f8f9fa;
    border-top: 2px solid #dee2e6;
    padding: 15px;
    border-radius: 0 0 10px 10px;
}

.estadistica-item {
    text-align: center;
    padding: 10px;
}

.estadistica-numero {
    font-size: 1.5rem;
    font-weight: bold;
    color: #495057;
}

.estadistica-label {
    font-size: 0.85rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.cargando {
    opacity: 0.7;
    pointer-events: none;
}

@media (max-width: 768px) {
    .tabla-inventario {
        overflow-x: auto;
    }
    
    .tabla-inventario th,
    .tabla-inventario td {
        min-width: 120px;
        font-size: 0.8rem;
    }
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Toolbar Superior -->
    <div class="toolbar-inventario">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-2">
                    <i class="bi bi-table me-2"></i>
                    Inventario Masivo
                </h4>
                <p class="mb-0 opacity-75">
                    Gestión completa de productos con edición masiva tipo Excel
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <span id="contadorFilas" class="badge bg-light text-dark fs-6 me-2">0 productos</span>
                <a href="{{ route('productos.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left me-1"></i>
                    Volver al Inventario
                </a>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="btn-toolbar" role="toolbar">
                <div class="btn-group me-2" role="group">
                    <button type="button" class="btn btn-success" id="agregarFila">
                        <i class="bi bi-plus-circle me-1"></i>
                        Agregar Producto
                    </button>
                    <button type="button" class="btn btn-danger" id="eliminarSeleccionados">
                        <i class="bi bi-trash me-1"></i>
                        Eliminar Seleccionados
                    </button>
                </div>
                <div class="btn-group me-2" role="group">
                    <button type="button" class="btn btn-primary" id="guardarTodo">
                        <i class="bi bi-cloud-upload me-1"></i>
                        Guardar Cambios
                    </button>
                    <button type="button" class="btn btn-secondary" id="deshacerCambios">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Deshacer Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla Principal -->
    <div class="tabla-inventario">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tablaInventario">
                <thead class="sticky-top">
                    <tr>
                        <th width="40">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th width="60">#</th>
                        <th width="150">
                            <i class="bi bi-card-text me-1"></i>Nombre *
                        </th>
                        <th width="120">
                            <i class="bi bi-upc me-1"></i>Código
                        </th>
                        <th width="130">
                            <i class="bi bi-tags me-1"></i>Categoría
                        </th>
                        <th width="120">
                            <i class="bi bi-cash me-1"></i>P. Compra
                        </th>
                        <th width="120">
                            <i class="bi bi-currency-dollar me-1"></i>P. Venta *
                        </th>
                        <th width="80">
                            <i class="bi bi-percent me-1"></i>Margen
                        </th>
                        <th width="80">
                            <i class="bi bi-box me-1"></i>Stock
                        </th>
                        <th width="90">
                            <i class="bi bi-exclamation-triangle me-1"></i>S.Min
                        </th>
                        <th width="200">
                            <i class="bi bi-chat-text me-1"></i>Descripción
                        </th>
                        <th width="150">
                            <i class="bi bi-image me-1"></i>Imagen URL
                        </th>
                        <th width="70">
                            <i class="bi bi-toggle-on me-1"></i>Activo
                        </th>
                        <th width="80">
                            <i class="bi bi-gear me-1"></i>Acciones
                        </th>
                    </tr>
                </thead>
                <tbody id="tablaInventarioBody">
                    <!-- Las filas se generarán dinámicamente -->
                </tbody>
            </table>
        </div>

        <!-- Footer con Estadísticas -->
        <div class="estadisticas-footer">
            <div class="row">
                <div class="col-md-3">
                    <div class="estadistica-item">
                        <div class="estadistica-numero" id="totalProductos">0</div>
                        <div class="estadistica-label">Total</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="estadistica-item">
                        <div class="estadistica-numero text-warning" id="productosModificados">0</div>
                        <div class="estadistica-label">Modificados</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="estadistica-item">
                        <div class="estadistica-numero text-info" id="productosNuevos">0</div>
                        <div class="estadistica-label">Nuevos</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="estadistica-item">
                        <div class="estadistica-numero text-success" id="valorTotal">$0</div>
                        <div class="estadistica-label">Valor Total</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
console.log('🔥 SCRIPT INICIANDO...');

// Variables globales
window.productos = [];
window.categorias = [];
window.productosModificados = new Set();
window.productosNuevos = new Set();

// Test inmediato
console.log('🧪 Test inmediato - Variables creadas:', {
    productos: Array.isArray(window.productos),
    categorias: Array.isArray(window.categorias)
});

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎬 === INVENTARIO MASIVO INICIANDO ===');
    console.log('🌐 Variables iniciales:', {
        productos: window.productos?.length || 0,
        categorias: window.categorias?.length || 0
    });
    
    // Cargar datos iniciales
    inicializarInventario();
    
    // Configurar event listeners
    configurarEventListeners();
    
    console.log('⚡ Event listeners configurados');
});

// Función principal de inicialización
async function inicializarInventario() {
    try {
        console.log('Cargando categorías...');
        await cargarCategorias();
        
        console.log('Cargando productos...');
        await cargarProductos();
        
        console.log('=== INICIALIZACIÓN COMPLETADA ===');
    } catch (error) {
        console.error('Error en inicialización:', error);
        // Agregar una fila vacía si no se pueden cargar productos
        agregarNuevaFila();
    }
}

// Cargar categorías desde la API
async function cargarCategorias() {
    try {
        // Usar ruta alternativa si la API no funciona
        const response = await fetch(`${window.location.origin}/api/categorias`);
        if (response.ok) {
            const data = await response.json();
            // Manejar diferentes formatos de respuesta
            if (data.value && Array.isArray(data.value)) {
                categorias = data.value;
            } else if (Array.isArray(data)) {
                categorias = data;
            } else {
                categorias = [];
            }
            console.log('Categorías cargadas:', categorias.length);
        } else {
            console.log('⚠️ API de categorías falló, usando datos por defecto');
            // Categorías por defecto
            window.categorias = [
                {id: 1, nombre: 'Bebidas'},
                {id: 2, nombre: 'Comidas'},
                {id: 3, nombre: 'Cervezas'},
                {id: 4, nombre: 'Servicios'},
                {id: 5, nombre: 'Snacks'}
            ];
        }
    } catch (error) {
        console.error('Error al cargar categorías:', error);
        categorias = [{id: null, nombre: 'Sin categoría'}];
    }
}

// Cargar productos desde la API
async function cargarProductos() {
    console.log('🚀 Iniciando carga de productos...');
    try {
        console.log('📡 Haciendo petición a /api/productos...');
        const baseUrl = window.location.origin;
        const response = await fetch(`${baseUrl}/api/productos`);
        console.log('📨 Respuesta recibida:', response.status, response.statusText);
        
        if (response.ok) {
            const data = await response.json();
            console.log('📄 Datos recibidos:', data);
            
            // Manejar diferentes formatos de respuesta
            if (data.value && Array.isArray(data.value)) {
                window.productos = data.value;
                productos = window.productos;
                console.log('✅ Formato con .value detectado');
            } else if (Array.isArray(data)) {
                window.productos = data;
                productos = window.productos;
                console.log('✅ Formato array directo detectado');
            } else {
                window.productos = [];
                productos = window.productos;
                console.log('❌ Formato no reconocido:', typeof data);
            }
            
            console.log('🎯 Productos finales:', productos.length, productos);
            
            if (productos.length === 0) {
                console.log('📋 No hay productos, agregando fila vacía');
                agregarNuevaFila();
            } else {
                console.log('🎨 Renderizando tabla con productos');
                renderizarTabla();
            }
        } else {
            console.error('❌ Error en API:', response.status, response.statusText);
            const errorText = await response.text();
            console.error('❌ Respuesta de error:', errorText);
            
            // Cargar datos de prueba si la API falla
            console.log('🔄 Cargando datos de prueba...');
            window.productos = [
                {
                    id: 5,
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
                    id: 6,
                    nombre: 'Cerveza',
                    codigo: 'CERV001',
                    categoria_id: 3,
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
                    id: 4,
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
            
            console.log('✅ Datos de prueba cargados:', window.productos.length);
            renderizarTabla();
        }
    } catch (error) {
        console.error('💥 Error al cargar productos:', error);
        agregarNuevaFila();
    }
}

// Renderizar toda la tabla
function renderizarTabla() {
    console.log('Renderizando tabla...');
    const tbody = document.getElementById('tablaInventarioBody');
    
    if (!tbody) {
        console.error('No se encontró tbody');
        return;
    }
    
    tbody.innerHTML = '';
    
    productos.forEach((producto, index) => {
        const fila = crearFilaProducto(producto, index);
        tbody.appendChild(fila);
    });
    
    actualizarEstadisticas();
    console.log('Tabla renderizada con', productos.length, 'productos');
}

// Crear fila de producto
function crearFilaProducto(producto, index) {
    const fila = document.createElement('tr');
    fila.dataset.index = index;
    
    // Crear celdas
    fila.appendChild(createCheckboxCell());
    fila.appendChild(createTextCell(producto.id || 'NUEVO'));
    fila.appendChild(createInputCell('text', producto.nombre || '', 'nombre', index, 'Nombre del producto'));
    fila.appendChild(createInputCell('text', producto.codigo || '', 'codigo', index, 'Código'));
    fila.appendChild(createSelectCell(producto.categoria_id, 'categoria_id', index));
    fila.appendChild(createInputCell('number', producto.precio_compra || '', 'precio_compra', index, '0.00'));
    fila.appendChild(createInputCell('number', producto.precio_venta || '', 'precio_venta', index, '0.00'));
    fila.appendChild(createMargenCell(producto.precio_compra, producto.precio_venta));
    fila.appendChild(createInputCell('number', producto.stock || 0, 'stock', index, '0'));
    fila.appendChild(createInputCell('number', producto.stock_minimo || 5, 'stock_minimo', index, '5'));
    fila.appendChild(createTextareaCell(producto.descripcion || '', 'descripcion', index));
    fila.appendChild(createInputCell('url', producto.imagen_url || '', 'imagen_url', index, 'https://...'));
    fila.appendChild(createCheckboxActiveCell(producto.activo, index));
    fila.appendChild(createDeleteButtonCell(index));
    
    return fila;
}

// Funciones auxiliares para crear celdas
function createCheckboxCell() {
    const td = document.createElement('td');
    td.className = 'text-center';
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.className = 'form-check-input';
    td.appendChild(checkbox);
    return td;
}

function createTextCell(text) {
    const td = document.createElement('td');
    td.className = 'text-center';
    const small = document.createElement('small');
    small.className = 'text-muted';
    small.textContent = text;
    td.appendChild(small);
    return td;
}

function createInputCell(type, value, field, index, placeholder = '') {
    const td = document.createElement('td');
    const input = document.createElement('input');
    input.type = type;
    input.className = 'celda-editable';
    input.value = value;
    input.setAttribute('data-field', field);
    input.setAttribute('data-index', index);
    input.placeholder = placeholder;
    
    if (type === 'number') {
        input.step = field.includes('precio') ? '0.01' : '1';
        input.min = '0';
    }
    
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

function createMargenCell(precioCompra, precioVenta) {
    const td = document.createElement('td');
    td.className = 'text-center';
    const small = document.createElement('small');
    
    const compra = parseFloat(precioCompra) || 0;
    const venta = parseFloat(precioVenta) || 0;
    
    if (compra > 0 && venta > 0) {
        const margen = ((venta - compra) / compra * 100);
        small.textContent = margen.toFixed(1) + '%';
        small.className = margen >= 0 ? 'margen-positivo' : 'margen-negativo';
    } else {
        small.textContent = '-';
        small.className = 'text-muted';
    }
    
    td.appendChild(small);
    return td;
}

function createTextareaCell(value, field, index) {
    const td = document.createElement('td');
    const textarea = document.createElement('textarea');
    textarea.className = 'celda-editable';
    textarea.value = value;
    textarea.setAttribute('data-field', field);
    textarea.setAttribute('data-index', index);
    textarea.rows = 1;
    textarea.placeholder = 'Descripción...';
    td.appendChild(textarea);
    return td;
}

function createCheckboxActiveCell(checked, index) {
    const td = document.createElement('td');
    td.className = 'text-center';
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.className = 'checkbox-activo';
    checkbox.checked = checked;
    checkbox.setAttribute('data-field', 'activo');
    checkbox.setAttribute('data-index', index);
    td.appendChild(checkbox);
    return td;
}

function createDeleteButtonCell(index) {
    const td = document.createElement('td');
    td.className = 'text-center';
    const button = document.createElement('button');
    button.className = 'btn btn-danger btn-sm';
    button.onclick = () => eliminarFila(index);
    button.innerHTML = '<i class="bi bi-trash"></i>';
    td.appendChild(button);
    return td;
}

// Configurar event listeners
function configurarEventListeners() {
    console.log('🔧 Configurando event listeners...');
    
    // Botones principales con validación
    const agregarBtn = document.getElementById('agregarFila');
    const eliminarBtn = document.getElementById('eliminarSeleccionados');
    const guardarBtn = document.getElementById('guardarTodo');
    const deshacerBtn = document.getElementById('deshacerCambios');
    
    if (agregarBtn) {
        agregarBtn.addEventListener('click', agregarNuevaFila);
        console.log('✅ Event listener agregado a agregarFila');
    } else {
        console.error('❌ No se encontró el botón agregarFila');
    }
    
    if (eliminarBtn) {
        eliminarBtn.addEventListener('click', eliminarFilasSeleccionadas);
        console.log('✅ Event listener agregado a eliminarSeleccionados');
    } else {
        console.error('❌ No se encontró el botón eliminarSeleccionados');
    }
    
    if (guardarBtn) {
        guardarBtn.addEventListener('click', guardarTodosLosCambios);
        console.log('✅ Event listener agregado a guardarTodo');
    } else {
        console.error('❌ No se encontró el botón guardarTodo');
    }
    
    if (deshacerBtn) {
        deshacerBtn.addEventListener('click', deshacerCambios);
        console.log('✅ Event listener agregado a deshacerCambios');
    } else {
        console.error('❌ No se encontró el botón deshacerCambios');
    }
    
    // Event listeners para inputs dinámicos
    const tbody = document.getElementById('tablaInventarioBody');
    tbody.addEventListener('input', function(e) {
        if (e.target.classList.contains('celda-editable') || 
            e.target.classList.contains('select-categoria') ||
            e.target.classList.contains('checkbox-activo')) {
            actualizarProducto(e.target);
        }
    });
}

// Actualizar producto desde input
function actualizarProducto(input) {
    const field = input.getAttribute('data-field');
    const index = parseInt(input.getAttribute('data-index'));
    let value = input.value;
    
    if (input.type === 'checkbox') {
        value = input.checked;
    } else if (input.type === 'number') {
        value = parseFloat(value) || 0;
    }
    
    if (productos[index]) {
        productos[index][field] = value;
        productos[index].modificado = true;
        
        // Marcar como modificado si no es nuevo
        if (!productos[index].es_nuevo) {
            productosModificados.add(index);
            input.classList.add('modificado');
        }
        
        actualizarEstadisticas();
    }
}

// Agregar nueva fila
function agregarNuevaFila() {
    const nuevoProducto = {
        id: null,
        nombre: '',
        codigo: '',
        categoria_id: null,
        precio_compra: null,
        precio_venta: null,
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
        renderizarTabla();
    }
}

// Eliminar filas seleccionadas
function eliminarFilasSeleccionadas() {
    const checkboxes = document.querySelectorAll('#tablaInventarioBody input[type="checkbox"]:checked');
    if (checkboxes.length === 0) {
        alert('Selecciona al menos un producto para eliminar');
        return;
    }
    
    if (confirm(`¿Estás seguro de eliminar ${checkboxes.length} producto(s)?`)) {
        // Obtener índices de las filas seleccionadas
        const indices = Array.from(checkboxes).map(cb => {
            const fila = cb.closest('tr');
            return parseInt(fila.dataset.index);
        }).sort((a, b) => b - a); // Ordenar descendente para eliminar desde el final
        
        // Eliminar productos
        indices.forEach(index => {
            productos.splice(index, 1);
            productosModificados.delete(index);
            productosNuevos.delete(index);
        });
        
        renderizarTabla();
    }
}

// Guardar todos los cambios
function guardarTodosLosCambios() {
    const productosParaGuardar = productos.filter((p, index) => 
        productosNuevos.has(index) || productosModificados.has(index)
    );
    
    if (productosParaGuardar.length === 0) {
        alert('No hay cambios para guardar');
        return;
    }
    
    console.log('Guardando productos:', productosParaGuardar);
    alert(`Se guardarían ${productosParaGuardar.length} cambios.\n\n(Funcionalidad de guardado pendiente de implementación)`);
}

// Deshacer cambios
function deshacerCambios() {
    if (confirm('¿Estás seguro de deshacer todos los cambios?')) {
        productosModificados.clear();
        productosNuevos.clear();
        cargarProductos();
    }
}

// Actualizar estadísticas
function actualizarEstadisticas() {
    const total = productos.length;
    const modificados = productosModificados.size;
    const nuevos = productosNuevos.size;
    const valorTotal = productos.reduce((sum, p) => {
        const precio = parseFloat(p.precio_venta) || 0;
        const stock = parseInt(p.stock) || 0;
        return sum + (precio * stock);
    }, 0);
    
    // Actualizar elementos con validación
    const elementos = [
        {id: 'contadorFilas', valor: `${total} productos`},
        {id: 'totalProductos', valor: total},
        {id: 'productosModificados', valor: modificados},
        {id: 'productosNuevos', valor: nuevos},
        {id: 'valorTotal', valor: `$${valorTotal.toLocaleString()}`}
    ];
    
    elementos.forEach(elem => {
        const elemento = document.getElementById(elem.id);
        if (elemento) {
            elemento.textContent = elem.valor;
        } else {
            console.warn(`⚠️ Elemento ${elem.id} no encontrado`);
        }
    });
}

// Funciones de debug
window.debugCargar = function() {
    console.log('=== DEBUG INFO ===');
    console.log('Productos:', productos);
    console.log('Categorías:', categorias);
    console.log('Productos modificados:', productosModificados);
    console.log('Productos nuevos:', productosNuevos);
};

window.testTabla = function() {
    const testData = [
        {id: 1, nombre: 'Agua', codigo: 'AGUA001', categoria_id: 1, precio_compra: 1000, precio_venta: 1500, stock: 30, stock_minimo: 10, descripcion: 'Agua natural', activo: true},
        {id: 2, nombre: 'Cerveza', codigo: 'CERV001', categoria_id: 3, precio_compra: 2500, precio_venta: 3500, stock: 25, stock_minimo: 5, descripcion: 'Cerveza nacional', activo: true}
    ];
    
    productos = testData;
    renderizarTabla();
    console.log('Datos de prueba cargados');
};

// Funciones de debug disponibles globalmente
window.testScript = function() {
    console.log('✅ JavaScript funciona!');
    console.log('📊 Variables:', {
        productos: window.productos,
        categorias: window.categorias
    });
    return 'Script OK';
};

window.cargarManual = function() {
    console.log('🔄 Cargando productos manualmente...');
    const baseUrl = window.location.origin; // Usar la URL base actual
    fetch(`${baseUrl}/api/productos`)
        .then(r => {
            console.log('📡 Respuesta:', r.status);
            return r.json();
        })
        .then(data => {
            console.log('📄 Datos:', data);
            if (data.value) {
                window.productos = data.value;
            } else if (Array.isArray(data)) {
                window.productos = data;
            }
            console.log('✅ Productos cargados:', window.productos.length);
        })
        .catch(e => console.error('❌ Error:', e));
};

console.log('🎯 === SCRIPT CARGADO CORRECTAMENTE ===');
console.log('🛠️ Funciones disponibles: testScript(), cargarManual()');

// Test automático
setTimeout(() => {
    console.log('⏰ Test automático después de 1 segundo...');
    if (typeof window.testScript === 'function') {
        window.testScript();
    }
}, 1000);
</script>
@endpush