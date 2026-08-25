<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Mostrar todos los productos.
     */
    public function index()
    {
        $productos = Producto::with('categoria')
            ->orderBy('nombre')
            ->get();

        return view('productos.index', compact('productos'));
    }

    /**
     * Mostrar formulario para registrar producto.
     */
    public function create()
    {
        $categorias = Categoria::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('productos.create', compact('categorias'));
    }

    /**
     * Guardar producto.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required|exists:categorias,id',
            'precio' => 'required|numeric|min:0',
        ]);

        $categoria = Categoria::findOrFail($request->categoria_id);

        // Prefijo según la categoría
        $prefijos = [
            'TORTAS' => 'TOR',
            'QUEQUES' => 'QUE',
            'POSTRES' => 'POS',
            'ESPECIALES' => 'ESP',
            'CHEESECAKE Y TARTAS' => 'CT',
        ];

        $nombreCategoria = strtoupper(trim($categoria->nombre));

        $prefijo = $prefijos[$nombreCategoria] ?? 'PRO';

        // Buscar el último producto con ese prefijo
        $ultimoProducto = Producto::where('codigo', 'like', $prefijo . '-%')
            ->orderByRaw('CAST(SUBSTRING_INDEX(codigo, "-", -1) AS UNSIGNED) DESC')
            ->first();

        if ($ultimoProducto) {
            $numero = (int) substr($ultimoProducto->codigo, strlen($prefijo) + 1);
            $numero++;
        } else {
            $numero = 1;
        }

        $codigo = $prefijo . '-' . str_pad($numero, 3, '0', STR_PAD_LEFT);

        Producto::create([
            'codigo' => $codigo,
            'nombre' => $request->nombre,
            'precio' => $request->precio,
            'categoria_id' => $request->categoria_id,
            'activo' => true,
        ]);

        return redirect()
            ->route('productos.index')
            ->with('success', "Producto registrado correctamente con código {$codigo}.");
    }

    /**
     * Mostrar formulario para editar producto.
     */
    public function edit(Producto $producto)
    {
        $categorias = Categoria::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('productos.edit', compact('producto', 'categorias'));
    }

    /**
     * Actualizar producto.
     */
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required|exists:categorias,id',
            'precio' => 'required|numeric|min:0',
        ]);

        $producto->update([
            'nombre' => $request->nombre,
            'precio' => $request->precio,
            'categoria_id' => $request->categoria_id,
        ]);

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Eliminar producto.
     */
    public function destroy(Producto $producto)
    {
        $producto->update([
            'activo' => false,
        ]);

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto desactivado correctamente.');
    }
}