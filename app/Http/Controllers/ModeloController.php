<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Http\Requests\Modelo\StoreModeloRequest;
use App\Http\Requests\Modelo\UpdateModeloRequest;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{
    public Modelo $model;

    public function __construct(Modelo $modelo)
    {
        $this->model = $modelo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modelos = $this->model->all();

        return response()->json($modelos, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreModeloRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreModeloRequest $request)
    {
        $payload = $request->validated();

        $image_urn = $request->file('imagem')->store('images/modelos', 'public');

        $result = $this->model->create([
            'marca_id' => $payload['marca_id'],
            'nome' => $payload['nome'],
            'imagem' => $image_urn,
            'numero_portas' => $payload['numero_portas'],
            'lugares' => $payload['lugares'],
            'air_bag' => $payload['air_bag'],
            'abs' => $payload['abs']
        ]);

        return response()->json($result, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer  $modeloId
     * @return \Illuminate\Http\Response
     */
    public function show(int $modeloId)
    {
        $modelo = $this->model->find($modeloId);

        if ($modelo === null) {
            return response()->json(['erro' => 'Modelo não encontrado.'], 404);
        }

        return response()->json($modelo, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateModeloRequest  $request
     * @param  Integer  $modeloId
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateModeloRequest $request, int $modeloId)
    {
        $modelo = $this->model->find($modeloId);

        if ($modelo === null) {
            return response()->json(['erro' => 'Modelo não encontrado.'], 404);
        }

        $payload = $request->validated();

        $image_urn = $request->file('imagem')->store('images/modelos', 'public');
        
        //delete old image file
        if ($request->file('image')) {
            Storage::disk('public')->delete($modelo->imagem);
        }

        $this->model->update([
            'marca_id' => $payload['marca_id'],
            'nome' => $payload['nome'],
            'imagem' => $image_urn,
            'numero_portas' => $payload['numero_portas'],
            'lugares' => $payload['lugares'],
            'air_bag' => $payload['air_bag'],
            'abs' => $payload['abs']
        ]);

        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer  $modeloId
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $modeloId)
    {
        $modelo = $this->model->find($modeloId);

        if ($modelo === null) {
            return response()->json(['erro' => 'Modelo não encontrado.'], 404);
        }

        //delete saved image file
        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();

        return response()->json(['msg' => 'O modelo foi removido com sucesso.'], 200);
    }
}
