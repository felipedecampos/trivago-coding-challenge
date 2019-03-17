<?php

namespace App\Repositories;

use App\Models\Waiter;
use App\Repositories\RepositoryInterface\RepositoryInterface;
use Illuminate\Http\Response;

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

    public function __construct(Waiter $waiter)
    {
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

    public function getOneAvailable()
    {
        $available = $this->getAllAvailable();

        return $available->count()
            ? $available->offsetGet(array_rand($available->toArray()) ?? null) ?? null
            : null;
    }

    /**
     * @param int $id
     * @param bool $availability
     * @return bool
     * @throws \Exception
     */
    private function setAvailability(int $id, bool $availability)
    {
        $this->waiter = $this->find($id);

        if (is_null($this->waiter)) {
            $exceptionMessage = sprintf('Waiter not found with id: %s', $id);
            throw new \Exception($exceptionMessage, Response::HTTP_NOT_FOUND);
        }

        $this->waiter->available = $availability;

        return $this->waiter->save();
    }

    /**
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function setUnavailable($id)
    {
        return $this->setAvailability($id, false);
    }

    /**
     * @param int $id
     * @return bool
     * @throws \Exception
     */
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
