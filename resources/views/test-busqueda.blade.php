<!DOCTYPE html>
<html>
<head>
    <title>Test Búsqueda Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Test de Búsqueda de Productos</h2>
        
        <div class="row">
            <div class="col-6">
                <label class="form-label">Buscar Producto por Nombre</label>
                <div class="position-relative">
                    <input type="text" 
                           class="form-control buscar-producto" 
                           placeholder="Escribe el nombre del producto..."
                           id="buscarProductoTest"
                           autocomplete="off">
                    <div class="position-absolute w-100 bg-white border rounded shadow-sm resultados-busqueda" 
                         style="top: 100%; z-index: 1050; display: none; max-height: 200px; overflow-y: auto;"></div>
                </div>
                <small class="text-muted">Total productos: {{ $productos->count() }}</small>
            </div>
            <div class="col-6">
                <h5>Productos Disponibles (primeros 10):</h5>
                <ul>
                    @foreach($productos->take(10) as $producto)
                        <li>{{ $producto->nombre }} - ${{ number_format($producto->precio_venta) }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Base de datos de productos en JavaScript
        let productosDisponibles = [
            @foreach($productos as $producto)
            {
                id: {{ $producto->id }},
                nombre: '{{ addslashes($producto->nombre) }}',
                precio: {{ $producto->precio }},
                categoria: '@if($producto->categoria){{ addslashes($producto->categoria->nombre) }}@else Sin categoría @endif'
            }@if(!$loop->last),@endif
            @endforeach
        ];

        console.log('🛒 Productos cargados:', productosDisponibles.length);
        console.log('📊 Muestra:', productosDisponibles.slice(0, 3));

        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('buscarProductoTest');
            const resultadosDiv = input.parentElement.querySelector('.resultados-busqueda');

            input.addEventListener('input', function() {
                console.log('Input:', this.value);
                const query = this.value.toLowerCase().trim();
                
                if (query.length < 2) {
                    resultadosDiv.style.display = 'none';
                    return;
                }

                const resultados = productosDisponibles.filter(producto => 
                    producto.nombre.toLowerCase().includes(query)
                ).slice(0, 8);

                console.log('Resultados:', resultados.length);

                if (resultados.length === 0) {
                    resultadosDiv.innerHTML = `<div class="p-3 text-muted">No se encontraron productos</div>`;
                    resultadosDiv.style.display = 'block';
                    return;
                }

                const html = resultados.map(producto => `
                    <div class="p-2 border-bottom" style="cursor: pointer;">
                        <strong>${producto.nombre}</strong><br>
                        <small>$${producto.precio.toLocaleString()}</small>
                    </div>
                `).join('');

                resultadosDiv.innerHTML = html;
                resultadosDiv.style.display = 'block';
            });
        });
    </script>
</body>
</html>