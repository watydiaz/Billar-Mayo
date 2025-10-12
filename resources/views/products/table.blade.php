<table class="table table-striped table-hover mb-0">
    <thead class="table-dark">
        <tr>
            <th width="5%">
                <i class="bi bi-hash"></i> ID
            </th>
            <th width="20%">
                <i class="bi bi-box"></i> Producto
            </th>
            <th width="10%">
                <i class="bi bi-tags"></i> Categoría
            </th>
            <th width="8%">
                <i class="bi bi-arrow-down-circle text-danger"></i> P. Compra
            </th>
            <th width="8%">
                <i class="bi bi-arrow-up-circle text-success"></i> P. Venta
            </th>
            <th width="7%">
                <i class="bi bi-percent"></i> Margen
            </th>
            <th width="8%">
                <i class="bi bi-boxes"></i> Stock
            </th>
            <th width="7%">
                <i class="bi bi-exclamation-triangle"></i> S.Mín
            </th>
            <th width="8%">
                <i class="bi bi-upc-scan"></i> Código
            </th>
            <th width="6%">
                <i class="bi bi-image"></i> Img
            </th>
            <th width="6%">
                <i class="bi bi-gear"></i> Tipo
            </th>
            <th width="7%">
                <i class="bi bi-toggle-on"></i> Estado
            </th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $product)
            @php
                $stockBajo = !$product->es_servicio && $product->stock <= ($product->stock_minimo ?? 5);
                $stockCritico = !$product->es_servicio && $product->stock <= ($product->stock_minimo ?? 5) / 2;
            @endphp
            <tr class="product-row {{ !$product->activo ? 'table-secondary' : '' }} {{ $stockCritico ? 'table-danger' : ($stockBajo ? 'table-warning' : '') }}" 
                data-id="{{ $product->id }}">
                
                <!-- ID -->
                <td class="fw-bold text-center">
                    <span class="badge bg-primary">#{{ $product->id }}</span>
                </td>
                
                <!-- Nombre del Producto -->
                <td>
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="fw-bold editable" data-field="nombre" data-type="text">
                                {{ $product->nombre }}
                            </div>
                            @if($product->descripcion)
                                <small class="text-muted">{{ Str::limit($product->descripcion, 40) }}</small>
                            @endif
                        </div>
                        @if($stockCritico)
                            <i class="bi bi-exclamation-triangle-fill text-danger ms-2" title="Stock crítico"></i>
                        @elseif($stockBajo)
                            <i class="bi bi-exclamation-triangle text-warning ms-2" title="Stock bajo"></i>
                        @endif
                    </div>
                </td>
                
                <!-- Categoría -->
                <td>
                    <span class="badge bg-secondary">
                        @if($product->categoria)
                            {{ $product->categoria->nombre }}
                        @else
                            Sin categoría
                        @endif
                    </span>
                </td>
                
                <!-- Precio de Compra -->
                <td class="text-center">
                    <div class="text-danger editable" data-field="precio_compra" data-type="number" data-step="0.01">
                        @if($product->precio_compra)
                            ${{ number_format($product->precio_compra, 0, ',', '.') }}
                        @else
                            <small class="text-muted">N/A</small>
                        @endif
                    </div>
                </td>

                <!-- Precio de Venta -->
                <td class="text-center">
                    <div class="text-success fw-bold editable" data-field="precio_venta" data-type="number" data-step="0.01">
                        ${{ number_format($product->precio_venta, 0, ',', '.') }}
                    </div>
                </td>

                <!-- Margen de Ganancia -->
                <td class="text-center">
                    @if($product->precio_compra && $product->precio_venta && $product->precio_compra > 0)
                        @php
                            $margen = (($product->precio_venta - $product->precio_compra) / $product->precio_compra) * 100;
                            $colorMargen = $margen >= 50 ? 'text-success' : ($margen >= 25 ? 'text-warning' : 'text-danger');
                        @endphp
                        <span class="badge {{ $margen >= 50 ? 'bg-success' : ($margen >= 25 ? 'bg-warning' : 'bg-danger') }}">
                            {{ number_format($margen, 1) }}%
                        </span>
                    @else
                        <small class="text-muted">-</small>
                    @endif
                </td>
                
                <!-- Stock Actual -->
                <td class="text-center">
                    @if($product->es_servicio)
                        <span class="badge bg-info">
                            <i class="bi bi-infinite"></i> Servicio
                        </span>
                    @else
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="editable fw-bold {{ $stockCritico ? 'text-danger' : ($stockBajo ? 'text-warning' : 'text-success') }}" 
                                  data-field="stock" data-type="number">
                                {{ $product->stock }}
                            </span>
                            <small class="text-muted ms-1">uds</small>
                        </div>
                    @endif
                </td>
                
                <!-- Stock Mínimo -->
                <td class="text-center">
                    @if($product->es_servicio)
                        <span class="text-muted">N/A</span>
                    @else
                        <span class="editable text-muted" data-field="stock_minimo" data-type="number">
                            {{ $product->stock_minimo ?? 5 }}
                        </span>
                    @endif
                </td>
                
                <!-- Código -->
                <td>
                    <code class="editable" data-field="codigo" data-type="text">
                        {{ $product->codigo ?: 'Sin código' }}
                    </code>
                </td>

                <!-- Imagen -->
                <td class="text-center">
                    @if($product->imagen_url)
                        <div class="position-relative">
                            <img src="{{ $product->imagen_url }}" 
                                 alt="{{ $product->nombre }}" 
                                 class="rounded" 
                                 style="width: 40px; height: 40px; object-fit: cover; cursor: pointer;"
                                 onclick="mostrarImagenCompleta('{{ $product->imagen_url }}', '{{ addslashes($product->nombre) }}')"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <div class="text-muted" style="display: none;">
                                <i class="bi bi-image" title="Error al cargar imagen"></i>
                            </div>
                        </div>
                    @else
                        <button class="btn btn-outline-secondary btn-sm" 
                                onclick="editarImagen({{ $product->id }})"
                                title="Agregar imagen">
                            <i class="bi bi-plus"></i>
                        </button>
                    @endif
                </td>
                
                <!-- Tipo -->
                <td class="text-center">
                    <span class="badge {{ $product->es_servicio ? 'bg-info' : 'bg-primary' }}">
                        <i class="bi {{ $product->es_servicio ? 'bi-gear' : 'bi-box' }}"></i>
                        {{ $product->es_servicio ? 'Servicio' : 'Producto' }}
                    </span>
                </td>
                
                <!-- Estado -->
                <td class="text-center">
                    <div class="form-check form-switch d-flex justify-content-center">
                        <input class="form-check-input status-toggle" 
                               type="checkbox" 
                               {{ $product->activo ? 'checked' : '' }}
                               data-id="{{ $product->id }}"
                               title="{{ $product->activo ? 'Desactivar' : 'Activar' }} producto">
                    </div>
                </td>
            </tr>
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