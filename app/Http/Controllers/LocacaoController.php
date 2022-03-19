<?php

namespace App\Http\Controllers;

use App\Models\Locacao;
use App\Http\Requests\Locacao\StoreLocacaoRequest;
use App\Http\Requests\Locacao\UpdateLocacaoRequest;
use App\Repositories\LocacaoRepository;
use Illuminate\Http\Request;

class LocacaoController extends Controller
{
    public function __construct()
    {
        $this->model = new Locacao();
        $this->repository = new LocacaoRepository($this->model);
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

        if ($request->has('filter_locacao')) {
            $filter_locacao = $request->filter_locacao;
            $filter_locacao = $filter_locacao. ',cliente_id,carro_id';
            $this->repository->selectAtributos($filter_locacao);
        }

        return response()->json($this->repository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreLocacaoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLocacaoRequest $request)
    {
        $payload = $request->validated();

        $result = $this->model->create([
            'cliente_id' => $payload['cliente_id'],
            'carro_id' => $payload['carro_id'],
            'data_inicio_periodo' => $payload['data_inicio_periodo'],
            'data_final_previsto_previsto' => $payload['data_final_previsto_previsto'],
            'data_final_realizado_previsto' => $payload['data_final_realizado_previsto'],
            'valor_diaria' => $payload['valor_diaria'],
            'km_inicial' => $payload['km_inicial'],
            'km_final' => $payload['km_final']
        ]);

        return response()->json($result, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function show(int $locacaoId)
    {
        $locacao = $this->model->find($locacaoId); //with('modelo')->

        if ($locacao === null) {
            return response()->json(['erro' => 'Locação não encontrada.'], 404);
        }

        return response()->json($locacao, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLocacaoRequest  $request
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLocacaoRequest $request, int $locacaoId)
    {
        $locacao = $this->model->find($locacaoId);

        if ($locacao === null) {
            return response()->json(['erro' => 'Locação não encontrada.'], 404);
        }

        $payload = $request->validated();

        $locacao->fill($payload);
        $locacao->save();

        return response()->json($locacao, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $locacaoId)
    {
        $locacao = $this->model->find($locacaoId);

        if ($locacao === null) {
            return response()->json(['erro' => 'Locação não encontrado.'], 404);
        }

        $locacao->delete();

        return response()->json(['msg' => 'A locação foi removida com sucesso.'], 200);
    }
}
