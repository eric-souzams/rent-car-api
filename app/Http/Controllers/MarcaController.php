<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Http\Requests\Marca\StoreMarcaRequest;
use App\Http\Requests\Marca\UpdateMarcaRequest;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{
    public function __construct()
    {
        $this->model = new Marca();
        $this->repository = new MarcaRepository($this->model);
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
            $filter_modelo = 'modelos:marca_id,'.$filter_modelo;
            $this->repository->selectAtributosRegistrosRelacionados($filter_modelo);
        } else {
            $this->repository->selectAtributosRegistrosRelacionados('modelos');
        }

        if ($request->has('filter')) {
            $this->repository->filter($request->filter);
        }

        if ($request->has('filter_marca')) {
            $filter_marca = $request->filter_marca;
            $filter_marca = 'id,'.$filter_marca;
            $this->repository->selectAtributos($filter_marca);
        }

        return response()->json($this->repository->getResultado(), 200);
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

        $image_urn = $request->file('image')->store('images/marcas', 'public');

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
        $marca = $this->model->with('modelos')->find($marcaId);
        
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

        if ($request->hasFile('image')) {
            $image_urn = $request->file('image')->store('images/marcas', 'public');
        }
        
        //delete old image file
        if ($request->file('image')) {
            Storage::disk('public')->delete($marca->image);
        }

        $marca->fill($payload);
        if ($request->hasFile('image')) { $marca->image = $image_urn; }
        $marca->save();

        // $marca->update([
        //     'nome' => $payload['nome'],
        //     'image' => $image_urn
        // ]);

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

        //delete saved image file
        Storage::disk('public')->delete($marca->image);

        $marca->delete();

        return response()->json(['msg' => 'A marca foi removida com sucesso.'], 200);
    }
}
