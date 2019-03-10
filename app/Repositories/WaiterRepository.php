<?php

namespace App\Repositories;


use App\Models\Waiter;
use App\Repositories\RepositoryInterface\RepositoryInterface;

/**
 * Class WaiterRepository
 * @codeCoverageIgnore
 */
class WaiterRepository implements RepositoryInterface
{
    /**
     * @var Waiter
     */
    public $waiter;

    function __construct(Waiter $waiter) {
        $this->waiter = $waiter;
    }

    public function getAll()
    {
        return $this->waiter->all();
    }

    public function find($id)
    {
        return $this->waiter->find($id);
    }

    public function put($waiter)
    {
        $model = $this->waiter->find($waiter['id']);

        $waiterModel = is_null($model)
            ? new Waiter()
            : $model;

        $waiterModel->fill($waiter);

        if (is_null($model)) {
            $waiterModel->setAttribute('id', $waiter['id']);
        }

        return $waiterModel->save();
    }

    public function delete($id)
    {
        return $this->waiter->find($id)->delete();
    }
}