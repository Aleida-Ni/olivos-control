<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Despacho;
use App\Http\Controllers\TiendaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CajeroController extends Controller
{
    /**
     * Mostrar cajeros.
     */
    public function index()
    {
        $this->configurarMenuTienda();

        $cajeros = User::where('rol', 'cajero')
            ->orderBy('name')
            ->get();

        return view('cajeros.index', compact('cajeros'));
    }


    /**
     * Mostrar formulario para registrar cajero.
     */
    public function create()
    {
        $this->configurarMenuTienda();

        return view('cajeros.create');
    }


    private function configurarMenuTienda(): void
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();

        app(TiendaController::class)->menuTienda($pendientes);
    }


    /**
     * Guardar cajero.
     */
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',

        'pin' => [
            'required',
            'digits:4',
            'confirmed',
        ],
    ], [
        'name.required' => 'El nombre del cajero es obligatorio.',
        'pin.required' => 'El PIN es obligatorio.',
        'pin.digits' => 'El PIN debe tener exactamente 4 números.',
        'pin.confirmed' => 'Los PIN no coinciden.',
    ]);

    User::create([
        'name' => $request->name,
        'email' => null,
        'password' => Hash::make($request->pin),
        'rol' => 'cajero',
        'pin' => Hash::make($request->pin),
        'activo' => true,
    ]);

    return redirect()
        ->route('cajeros.index')
        ->with('success', 'Cajero registrado correctamente.');
}
}