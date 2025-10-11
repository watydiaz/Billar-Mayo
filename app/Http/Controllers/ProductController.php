<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();
        
        // Filtro de búsqueda
        if ($request->has('search') && !empty($request->search)) {
            $query->buscar($request->search);
        }
        
        // Filtro por categoría
        if ($request->has('categoria_id') && $request->categoria_id !== '') {
            $query->where('categoria_id', $request->categoria_id);
        }
        
        // Filtro por estado
        if ($request->has('activo') && $request->activo !== '') {
            $query->where('activo', $request->activo);
        }
        
        $products = $query->orderBy('nombre')->paginate(50);
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
            'precio_venta' => 'required|numeric|min:0',
            'precio_costo' => 'nullable|numeric|min:0',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'unidad_medida' => 'nullable|string|max:20',
            'imagen_url' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::create($request->all());

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
        $product = Product::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:200',
            'codigo' => 'nullable|string|max:50|unique:productos,codigo,' . $id,
            'descripcion' => 'nullable|string',
            'categoria_id' => 'nullable|integer|exists:categorias,id',
            'precio_venta' => 'required|numeric|min:0',
            'precio_costo' => 'nullable|numeric|min:0',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'unidad_medida' => 'nullable|string|max:20',
            'imagen_url' => 'nullable|string|max:500',
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
        $product = Product::findOrFail($id);
        $field = $request->field;
        $value = $request->value;

        // Validaciones por campo
        $rules = [];
        switch ($field) {
            case 'nombre':
                $rules = ['value' => 'required|string|max:200'];
                break;
            case 'precio_venta':
                $rules = ['value' => 'required|numeric|min:0'];
                break;
            case 'precio_costo':
                $rules = ['value' => 'nullable|numeric|min:0'];
                break;
            case 'stock_actual':
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
            case 'unidad_medida':
                $rules = ['value' => 'nullable|string|max:20'];
                break;
            case 'imagen_url':
                $rules = ['value' => 'nullable|string|max:500'];
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
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado exitosamente'
        ]);
    }

    /**
     * Toggle product status
     */
    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->activo = !$product->activo;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado exitosamente',
            'activo' => $product->activo
        ]);
    }
}
