<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Http\Requests\Modelo\StoreModeloRequest;
use App\Http\Requests\Modelo\UpdateModeloRequest;
use App\Repositories\ModeloRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{
    public function __construct()
    {
        $this->model = new Modelo();
        $this->repository = new ModeloRepository($this->model);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $repository = new ModeloRepository();

        if ($request->has('filter_marca')) {
            $filter_marca = $request->filter_marca;
            $filter_marca = 'marca:id,' . $filter_marca;
            $this->repository->selectAtributosRegistrosRelacionados($filter_marca);
        } else {
            $this->repository->selectAtributosRegistrosRelacionados('marca');
        }

        if ($request->has('filter')) {
            $this->repository->filter($request->filter);
        }

        if ($request->has('filter_modelo')) {
            $filter_modelo = $request->filter_modelo;
            $filter_modelo = $filter_modelo. ',marca_id';
            $this->repository->selectAtributos($filter_modelo);
        }

        return response()->json($this->repository->getResultado(), 200);
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
        $modelo = $this->model->with('marca')->find($modeloId);

        if ($modelo === null) {
            return response()->json(['erro' => 'Modelo n??o encontrado.'], 404);
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
            return response()->json(['erro' => 'Modelo n??o encontrado.'], 404);
        }

        $payload = $request->validated();

        if ($request->hasFile('image;')) {
            $image_urn = $request->file('imagem')->store('images/modelos', 'public');
        }
        
        //delete old image file
        if ($request->file('image')) {
            Storage::disk('public')->delete($modelo->imagem);
        }

        $modelo->fill($payload);
        if ($request->hasFile('imagem')) { $modelo->imagem = $image_urn; }
        $modelo->save();

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
            return response()->json(['erro' => 'Modelo n??o encontrado.'], 404);
        }

        //delete saved image file
        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();

        return response()->json(['msg' => 'O modelo foi removido com sucesso.'], 200);
    }
}
