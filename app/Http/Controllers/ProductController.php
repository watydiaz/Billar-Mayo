<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Producto::query();
        
        // Filtro de búsqueda
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }
        
        // Filtro por categoría
        if ($request->has('categoria_id') && $request->categoria_id !== '') {
            $query->where('categoria_id', $request->categoria_id);
        }
        
        // Filtro por estado
        if ($request->has('activo') && $request->activo !== '') {
            $query->where('activo', $request->activo);
        }
        
        // Filtro por stock
        if ($request->has('stock') && $request->stock !== '') {
            switch ($request->stock) {
                case 'bajo':
                    $query->whereRaw('stock <= stock_minimo AND es_servicio = false');
                    break;
                case 'critico':
                    $query->whereRaw('stock <= (stock_minimo / 2) AND es_servicio = false');
                    break;
                case 'agotado':
                    $query->where('stock', 0)->where('es_servicio', false);
                    break;
            }
        }
        
        $products = $query->with('categoria')->orderBy('nombre')->paginate(50);
        $categorias = \App\Models\Categoria::all();
        
        if ($request->ajax()) {
            return view('products.table', compact('products'))->render();
        }
        
        return view('products.index', compact('products', 'categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:200',
            'codigo' => 'nullable|string|max:50|unique:productos,codigo',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'nullable|integer|exists:categorias,id',
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'imagen_url' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Producto::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Producto creado exitosamente',
            'product' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Producto::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:200',
            'codigo' => 'nullable|string|max:50|unique:productos,codigo,' . $id,
            'descripcion' => 'nullable|string',
            'categoria_id' => 'nullable|integer|exists:categorias,id',
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'imagen_url' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $product->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado exitosamente',
            'product' => $product
        ]);
    }

    /**
     * Update single field via AJAX
     */
    public function updateField(Request $request, $id)
    {
        $product = Producto::findOrFail($id);
        $field = $request->field;
        $value = $request->value;

        // Validaciones por campo
        $rules = [];
        switch ($field) {
            case 'nombre':
                $rules = ['value' => 'required|string|max:200'];
                break;
            case 'precio_compra':
                $rules = ['value' => 'nullable|numeric|min:0'];
                break;
            case 'precio_venta':
                $rules = ['value' => 'required|numeric|min:0'];
                break;
            case 'stock':
                $rules = ['value' => 'required|integer|min:0'];
                break;
            case 'stock_minimo':
                $rules = ['value' => 'nullable|integer|min:0'];
                break;
            case 'categoria_id':
                $rules = ['value' => 'nullable|integer|exists:categorias,id'];
                break;
            case 'codigo':
                $rules = ['value' => 'nullable|string|max:50|unique:productos,codigo,' . $id];
                break;
            case 'descripcion':
                $rules = ['value' => 'nullable|string'];
                break;
            case 'imagen_url':
                $rules = ['value' => 'nullable|url|max:500'];
                break;
            case 'activo':
                $rules = ['value' => 'required|boolean'];
                break;
        }

        $validator = Validator::make(['value' => $value], $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('value')
            ], 422);
        }

        $product->update([$field => $value]);

        return response()->json([
            'success' => true,
            'message' => 'Campo actualizado exitosamente'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Producto::findOrFail($id);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado exitosamente'
        ]);
    }

    /**
     * Toggle product status
     */
    public function toggleStatus(Request $request, $id)
    {
        $product = Producto::findOrFail($id);
        $product->activo = $request->input('activo', !$product->activo);
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado exitosamente',
            'activo' => $product->activo
        ]);
    }

    /**
     * Preview import data
     */
    public function previewImport(Request $request)
    {
        try {
            $request->validate([
                'archivo' => 'required|file|mimes:csv,xlsx,xls|max:5120' // 5MB máximo
            ]);

            $archivo = $request->file('archivo');
            $extension = $archivo->getClientOriginalExtension();
            
            $data = [];
            
            if ($extension === 'csv') {
                $data = $this->procesarCSV($archivo);
            } elseif (in_array($extension, ['xlsx', 'xls'])) {
                // Para Excel necesitaríamos PhpSpreadsheet, por ahora solo CSV
                return response()->json([
                    'success' => false,
                    'message' => 'Formato Excel no soportado aún. Por favor usa CSV.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'total' => count($data)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar archivo: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Import products from file
     */
    public function import(Request $request)
    {
        try {
            $request->validate([
                'archivo' => 'required|file|mimes:csv,xlsx,xls|max:5120'
            ]);

            $archivo = $request->file('archivo');
            $actualizarExistentes = $request->boolean('actualizar_existentes');
            $crearCategorias = $request->boolean('crear_categorias');
            
            $data = $this->procesarCSV($archivo);
            $procesados = 0;
            $errores = [];

            foreach ($data as $index => $fila) {
                try {
                    $this->procesarFilaProducto($fila, $actualizarExistentes, $crearCategorias);
                    $procesados++;
                } catch (\Exception $e) {
                    $errores[] = "Fila " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Importación completada. Productos procesados: {$procesados}";
            if (!empty($errores)) {
                $message .= ". Errores: " . implode(', ', array_slice($errores, 0, 3));
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'procesados' => $procesados,
                'errores' => $errores
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en importación: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Procesar archivo CSV
     */
    private function procesarCSV($archivo)
    {
        $data = [];
        $handle = fopen($archivo->getPathname(), 'r');
        
        if ($handle !== false) {
            // Leer encabezados
            $headers = fgetcsv($handle, 1000, ',');
            
            // Normalizar encabezados
            $headers = array_map(function($header) {
                return strtolower(trim($header));
            }, $headers);
            
            // Leer datos
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($row) === count($headers)) {
                    $data[] = array_combine($headers, $row);
                }
            }
            
            fclose($handle);
        }
        
        return $data;
    }

    /**
     * Procesar una fila individual del archivo
     */
    private function procesarFilaProducto($fila, $actualizarExistentes, $crearCategorias)
    {
        // Validar campos requeridos
        if (empty($fila['nombre']) || empty($fila['precio'])) {
            throw new \Exception('Nombre y precio son campos requeridos');
        }

        // Buscar o crear categoría
        $categoriaId = null;
        if (!empty($fila['categoria'])) {
            $categoria = Categoria::where('nombre', $fila['categoria'])->first();
            
            if (!$categoria && $crearCategorias) {
                $categoria = Categoria::create([
                    'nombre' => $fila['categoria'],
                    'descripcion' => 'Creada automáticamente durante importación'
                ]);
            }
            
            $categoriaId = $categoria ? $categoria->id : null;
        }

        // Preparar datos del producto
        $datosProducto = [
            'nombre' => $fila['nombre'],
            'descripcion' => $fila['descripcion'] ?? null,
            'precio_compra' => isset($fila['precio_compra']) ? (float) str_replace(',', '.', $fila['precio_compra']) : null,
            'precio_venta' => (float) str_replace(',', '.', $fila['precio_venta'] ?? $fila['precio'] ?? 0),
            'stock' => isset($fila['stock']) ? (int) $fila['stock'] : 0,
            'stock_minimo' => isset($fila['stock_minimo']) ? (int) $fila['stock_minimo'] : 5,
            'imagen_url' => $fila['imagen_url'] ?? null,
            'categoria_id' => $categoriaId,
            'activo' => true
        ];

        // Buscar producto existente por código o nombre
        $productoExistente = null;
        
        if (!empty($fila['codigo'])) {
            $productoExistente = Producto::where('codigo', $fila['codigo'])->first();
            $datosProducto['codigo'] = $fila['codigo'];
        }
        
        if (!$productoExistente) {
            $productoExistente = Producto::where('nombre', $fila['nombre'])->first();
        }

        if ($productoExistente) {
            if ($actualizarExistentes) {
                $productoExistente->update($datosProducto);
            }
        } else {
            // Generar código si no existe
            if (empty($datosProducto['codigo'])) {
                $datosProducto['codigo'] = $this->generarCodigo($fila['nombre']);
            }
            
            Producto::create($datosProducto);
        }
    }

    /**
     * Generar código automático para producto
     */
    private function generarCodigo($nombre)
    {
        $palabras = explode(' ', strtoupper($nombre));
        $codigo = '';
        
        foreach ($palabras as $palabra) {
            $codigo .= substr($palabra, 0, 2);
        }
        
        $codigo = substr($codigo, 0, 4);
        $contador = 1;
        $codigoOriginal = $codigo;
        
        // Asegurar que el código sea único
        while (Producto::where('codigo', $codigo)->exists()) {
            $codigo = $codigoOriginal . str_pad($contador, 2, '0', STR_PAD_LEFT);
            $contador++;
        }
        
        return $codigo;
    }

    /**
     * Nueva vista en blanco
     */
    public function inventarioMasivo()
    {
        return view('products.inventario-masivo');
    }

    /**
     * Get categorias for API
     */
    public function getCategorias()
    {
        $categorias = \App\Models\Categoria::all();
        return response()->json($categorias);
    }

    /**
     * Get productos for API
     */
    public function getProductos()
    {
        $productos = Producto::with('categoria')
            ->orderBy('nombre')
            ->get()
            ->map(function($producto) {
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'codigo' => $producto->codigo,
                    'categoria_id' => $producto->categoria_id,
                    'precio_compra' => $producto->precio_compra,
                    'precio_venta' => $producto->precio_venta,
                    'stock' => $producto->stock,
                    'stock_minimo' => $producto->stock_minimo,
                    'descripcion' => $producto->descripcion,
                    'imagen_url' => $producto->imagen_url,
                    'activo' => $producto->activo,
                    'es_nuevo' => false
                ];
            });
        
        return response()->json($productos);
    }
}
