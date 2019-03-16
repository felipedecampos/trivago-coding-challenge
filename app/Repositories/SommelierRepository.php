<?php

namespace App\Repositories;


use App\Models\Sommelier;
use App\Repositories\RepositoryInterface\RepositoryInterface;
use Illuminate\Http\Response;

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

    /**
     * @param int $id
     * @param bool $availability
     * @return bool
     * @throws \Exception
     */
    private function setAvailability(int $id, bool $availability)
    {
        $this->sommelier = $this->find($id);

        if (is_null($this->sommelier)) {
            $exceptionMessage = sprintf('Sommelier not found with id: %s', $id);
            throw new \Exception($exceptionMessage, Response::HTTP_NOT_FOUND);
        }

        $this->sommelier->available = $availability;

        return $this->sommelier->save();
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