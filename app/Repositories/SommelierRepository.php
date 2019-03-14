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
        return $this->sommelier->query()
            ->orderBy('first_name', 'ASC')
            ->orderBy('last_name', 'ASC')
            ->get();
    }

    public function getAllAvailable()
    {
        return $this->sommelier->query()
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

    private function setAvailability(int $id, bool $availability)
    {
        $this->sommelier = $this->find($id);

        if (is_null($this->sommelier)) {
            return false;
        }

        $this->sommelier->available = $availability;

        return $this->sommelier->save();
    }

    public function setUnavailable($id)
    {
        return $this->setAvailability($id, false);
    }

    public function setAvailable(int $id)
    {
        return $this->setAvailability($id, true);
    }
}