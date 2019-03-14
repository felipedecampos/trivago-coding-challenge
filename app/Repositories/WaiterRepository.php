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
        return $this->waiter->query()
            ->orderBy('first_name', 'ASC')
            ->orderBy('last_name', 'ASC')
            ->get();
    }

    public function getAllAvailable()
    {
        return $this->waiter->query()
            ->where('available', '=', true)
            ->orderBy('first_name', 'ASC')
            ->orderBy('last_name', 'ASC')
            ->get();
    }

    private function setAvailability(int $id, bool $availability)
    {
        $this->waiter = $this->find($id);

        if (is_null($this->waiter)) {
            return false;
        }

        $this->waiter->available = $availability;

        return $this->waiter->save();
    }

    public function setUnavailable($id)
    {
        return $this->setAvailability($id, false);
    }

    public function setAvailable(int $id)
    {
        return $this->setAvailability($id, true);
    }

    public function find($id)
    {
        return $this->waiter->find($id);
    }

    public function put($waiter)
    {
        if (isset($waiter['id'])) {
            $model = $this->waiter->find($waiter['id']);
        }

        $waiterModel = ! isset($model)
            ? new Waiter()
            : $model;

        $waiterModel->fill($waiter);

        if (isset($waiter['id'])) {
            $waiterModel->setAttribute('id', $waiter['id']);
        }

        return $waiterModel->save();
    }

    public function delete($id)
    {
        return $this->waiter->find($id)->delete();
    }
}