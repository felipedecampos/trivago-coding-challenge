<?php

namespace App\Repositories;


use App\Models\Sommelier;
use App\Repositories\RepositoryInterface\RepositoryInterface;

/**
 * Class SommelierRepository
 * @codeCoverageIgnore
 */
class SommelierRepository implements RepositoryInterface
{
    /**
     * @var Sommelier
     */
    public $sommelier;

    function __construct(Sommelier $sommelier) {
        $this->sommelier = $sommelier;
    }

    public function getAll()
    {
        return $this->sommelier->all();
    }

    public function find($id)
    {
        return $this->sommelier->find($id);
    }

    public function put($sommelier)
    {
        if (isset($sommelier['id'])) {
            $model = $this->sommelier->find($sommelier['id']);
        }

        $sommelierModel = ! isset($model)
            ? new Sommelier()
            : $model;

        $sommelierModel->fill($sommelier);

        if (isset($sommelier['id'])) {
            $sommelierModel->setAttribute('id', $sommelier['id']);
        }

        return $sommelierModel->save();
    }

    public function delete($id)
    {
        return $this->sommelier->find($id)->delete();
    }
}