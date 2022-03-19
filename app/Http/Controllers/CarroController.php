<?php

namespace App\Http\Controllers;

use App\Http\Requests\Carro\StoreCarroRequest;
use App\Http\Requests\Carro\UpdateCarroRequest;
use App\Models\Carro;
use App\Repositories\CarroRepository;
use Illuminate\Http\Request;

class CarroController extends Controller
{
    public function __construct()
    {
        $this->model = new Carro();
        $this->repository = new CarroRepository($this->model);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('filter_modelo')) {
            $filter_modelo = $request->filter_modelo;
            $filter_modelo = 'modelo:id,' . $filter_modelo;
            $this->repository->selectAtributosRegistrosRelacionados($filter_modelo);
        } else {
            $this->repository->selectAtributosRegistrosRelacionados('modelo');
        }

        if ($request->has('filter')) {
            $this->repository->filter($request->filter);
        }

        if ($request->has('filter_carro')) {
            $filter_carro = $request->filter_carro;
            $filter_carro = $filter_carro. ',modelo_id';
            $this->repository->selectAtributos($filter_carro);
        }

        return response()->json($this->repository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCarroRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCarroRequest $request)
    {
        $payload = $request->validated();

        $result = $this->model->create([
            'modelo_id' => $payload['modelo_id'],
            'placa' => $payload['placa'],
            'disponivel' => $payload['disponivel'],
            'km' => $payload['km']
        ]);

        return response()->json($result, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function show(int $carroId)
    {
        $carro = $this->model->with('modelo')->find($carroId);

        if ($carro === null) {
            return response()->json(['erro' => 'Carro não encontrado.'], 404);
        }

        return response()->json($carro, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCarroRequest  $request
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCarroRequest $request, int $carroId)
    {
        $carro = $this->model->find($carroId);

        if ($carro === null) {
            return response()->json(['erro' => 'Carro não encontrado.'], 404);
        }

        $payload = $request->validated();

        $carro->fill($payload);
        $carro->save();

        return response()->json($carro, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $carroId)
    {
        $carro = $this->model->find($carroId);

        if ($carro === null) {
            return response()->json(['erro' => 'Carro não encontrado.'], 404);
        }

        $carro->delete();

        return response()->json(['msg' => 'O carro foi removido com sucesso.'], 200);
    }
}
