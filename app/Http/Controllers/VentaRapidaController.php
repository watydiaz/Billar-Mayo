<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class VentaRapidaController extends Controller
{
    public function procesarVenta(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0',
            'tipo_pago' => 'string|nullable'
        ]);

        DB::beginTransaction();

        try {
            // 1. Crear venta
            $venta = Venta::create([
                'numero_venta' => Venta::generarNumeroVenta(),
                'total' => $request->total,
                'subtotal' => 0, // Se calculará después
                'descuento' => 0,
                'tipo_pago' => $request->tipo_pago ?? 'efectivo',
                'estado' => '1', // Completado
                'observaciones' => 'Venta rápida de mostrador - ' . now()->format('d/m/Y H:i:s')
            ]);

            $totalCalculado = 0;
            $productosVendidos = [];

            // 2. Procesar cada item del carrito
            foreach ($request->items as $item) {
                $producto = Producto::findOrFail($item['id']);
                
                // Verificar stock disponible
                if ($producto->stock_actual < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para {$producto->nombre}. Disponible: {$producto->stock_actual}, Solicitado: {$item['cantidad']}");
                }

                $subtotal = $producto->precio_venta * $item['cantidad'];
                $totalCalculado += $subtotal;

                // Crear detalle de la venta
                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio_venta,
                    'subtotal' => $subtotal
                ]);

                // Descontar del inventario
                $producto->decrement('stock_actual', $item['cantidad']);

                $productosVendidos[] = [
                    'producto' => $producto->nombre,
                    'cantidad' => $item['cantidad'],
                    'precio' => $producto->precio_venta,
                    'subtotal' => $subtotal,
                    'stock_anterior' => $producto->stock_actual + $item['cantidad'],
                    'stock_nuevo' => $producto->stock_actual
                ];
            }

            // 3. Actualizar subtotal de la venta
            $venta->update(['subtotal' => $totalCalculado]);

            // 4. Verificar que el total coincida
            if (abs($totalCalculado - $request->total) > 0.01) {
                throw new \Exception("El total calculado ($totalCalculado) no coincide con el total enviado ({$request->total})");
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta procesada exitosamente',
                'data' => [
                    'venta_id' => $venta->id,
                    'numero_venta' => $venta->numero_venta,
                    'total' => $venta->total,
                    'productos_vendidos' => $productosVendidos,
                    'fecha' => $venta->created_at->format('d/m/Y H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la venta: ' . $e->getMessage()
            ], 400);
        }
    }

    public function obtenerProductos()
    {
        try {
            $productos = Producto::with('categoria')->where('activo', true)
                ->where('stock_actual', '>', 0) // Solo productos con stock
                ->get()
                ->map(function($producto) {
                    return [
                        'id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'precio' => $producto->precio_venta,
                        'categoria' => 'Productos', // Categoría genérica por ahora
                        'stock' => $producto->stock_actual
                    ];
                });
            
            return response()->json($productos);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar productos: ' . $e->getMessage()
            ], 500);
        }
    }
}