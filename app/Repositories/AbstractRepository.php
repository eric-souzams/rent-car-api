<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository 
{
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function selectAtributosRegistrosRelacionados($atributos)
    {
        $this->model = $this->model->with($atributos);
    }

    public function filter($filters)
    {
        $filtros = explode(';', $filters);
            
        foreach($filtros as $condicionais) {                
            $condicional = explode('=', $condicionais);
            $this->model = $this->model->where($condicional[0], 'like', $condicional[1].'%');
        }
    }

    public function selectAtributos($atributos)
    {
        $this->model = $this->model->selectRaw($atributos);
    }

    public function getResultado()
    {
        return $this->model->get();
    }
}