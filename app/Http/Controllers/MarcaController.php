<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Http\Requests\Marca\StoreMarcaRequest;
use App\Http\Requests\Marca\UpdateMarcaRequest;

class MarcaController extends Controller
{
    public Marca $model;

    public function __construct(Marca $marca)
    {
        $this->model = $marca;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $marcas = $this->model->all();

        return response()->json($marcas, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMarcaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMarcaRequest $request)
    {
        $payload = $request->validated();

        $image_urn = $request->file('image')->store('images', 'public');

        $result = $this->model->create([
            'nome' => $payload['nome'],
            'image' => $image_urn
        ]);

        return $result;
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer  $marcaId
     * @return \Illuminate\Http\Response
     */
    public function show(int $marcaId)
    {
        $marca = $this->model->find($marcaId);
        
        if ($marca === null) {
            return response()->json(['erro' => 'Marca não encontrada.'], 404);
        }

        return response()->json($marca, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMarcaRequest  $request
     * @param  Integer  $marcaId
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMarcaRequest $request, int $marcaId)
    {
        $marca = $this->model->find($marcaId);
        
        if ($marca === null) {
            return response()->json(['erro' => 'Marca não encontrada.'], 404);
        }
        
        $payload = $request->validated();
        $marca->update($payload);

        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer  $marcaId
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $marcaId)
    {
        $marca = $this->model->find($marcaId);

        if ($marca === null) {
            return response()->json(['erro' => 'Marca não encontrada.'], 404);
        }

        $marca->delete();

        return response()->json(['msg' => 'A marca foi removida com sucesso.'], 200);
    }
}
