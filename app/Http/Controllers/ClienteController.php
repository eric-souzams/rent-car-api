<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Requests\Cliente\StoreClienteRequest;
use App\Http\Requests\Cliente\UpdateClienteRequest;
use App\Repositories\ClienteRepository;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function __construct()
    {
        $this->model = new Cliente();
        $this->repository = new ClienteRepository($this->model);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('filter')) {
            $this->repository->filter($request->filter);
        }

        if ($request->has('filter_cliente')) {
            $filter_cliente = $request->filter_cliente;
            $this->repository->selectAtributos($filter_cliente);
        }

        return response()->json($this->repository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreClienteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClienteRequest $request)
    {
        $payload = $request->validated();

        $result = $this->model->create([
            'nome' => $payload['nome']
        ]);

        return response()->json($result, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function show(int $clienteId)
    {
        $cliente = $this->model->find($clienteId);

        if ($cliente === null) {
            return response()->json(['erro' => 'Cliente não encontrado.'], 404);
        }

        return response()->json($cliente, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateClienteRequest  $request
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClienteRequest $request, int $clienteId)
    {
        $cliente = $this->model->find($clienteId);

        if ($cliente === null) {
            return response()->json(['erro' => 'Cliente não encontrado.'], 404);
        }

        $payload = $request->validated();

        $cliente->fill($payload);
        $cliente->save();

        return response()->json($cliente, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $clienteId)
    {
        $cliente = $this->model->find($clienteId);

        if ($cliente === null) {
            return response()->json(['erro' => 'Cliente não encontrado.'], 404);
        }

        $cliente->delete();

        return response()->json(['msg' => 'O cliente foi removido com sucesso.'], 200);
    }
}
