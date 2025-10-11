<table class="table table-striped table-hover mb-0">
    <thead class="table-dark">
        <tr>
            <th width="5%">ID</th>
            <th width="20%">Nombre</th>
            <th width="12%">Categoría</th>
            <th width="10%">Precio Venta</th>
            <th width="8%">Precio Costo</th>
            <th width="8%">Stock</th>
            <th width="10%">Código</th>
            <th width="8%">Unidad</th>
            <th width="8%">Estado</th>
            <th width="11%">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $product)
            <tr class="product-row {{ !$product->activo ? 'inactive' : '' }}" data-id="{{ $product->id }}">
                <td class="fw-bold">{{ $product->id }}</td>
                <td class="editable" data-field="nombre" data-type="text">{{ $product->nombre }}</td>
                <td class="text-muted">
                    {{ $product->categoria ? $product->categoria->nombre : 'Sin categoría' }}
                </td>
                <td class="editable" data-field="precio_venta" data-type="number" data-step="0.01">
                    ${{ number_format($product->precio_venta, 2) }}
                </td>
                <td class="editable" data-field="precio_costo" data-type="number" data-step="0.01">
                    @if($product->precio_costo)
                        ${{ number_format($product->precio_costo, 2) }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td class="editable" data-field="stock_actual" data-type="number">
                    <span class="{{ $product->stock_actual <= ($product->stock_minimo ?? 5) ? 'text-danger fw-bold' : '' }}">
                        {{ $product->stock_actual }}
                    </span>
                </td>
                <td class="editable" data-field="codigo" data-type="text">{{ $product->codigo }}</td>
                <td class="editable" data-field="unidad_medida" data-type="text">
                    {{ $product->unidad_medida ?: 'unidad' }}
                </td>
                <td>
                    <span class="badge status-badge {{ $product->activo ? 'bg-success' : 'bg-secondary' }}" 
                          data-id="{{ $product->id }}" 
                          title="Clic para cambiar estado">
                        {{ $product->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-info btn-sm" 
                                title="Ver descripción" 
                                data-bs-toggle="tooltip"
                                onclick="showDescription({{ $product->id }}, '{{ addslashes($product->descripcion) }}')">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-danger btn-sm delete-product" 
                                data-id="{{ $product->id }}"
                                title="Eliminar producto"
                                data-bs-toggle="tooltip">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="text-muted">
                        <i class="bi bi-box" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">No hay productos disponibles</h5>
                        <p>Agrega tu primer producto para comenzar</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Modal para mostrar descripción -->
<div class="modal fade" id="descriptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Descripción del Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="descriptionContent"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
function showDescription(id, description) {
    const modal = new bootstrap.Modal(document.getElementById('descriptionModal'));
    document.getElementById('descriptionContent').textContent = description || 'Sin descripción disponible';
    modal.show();
}
</script>