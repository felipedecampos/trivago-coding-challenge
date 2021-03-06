<?php

namespace App\Repositories;

use App\Models\Wine;
use App\Repositories\RepositoryInterface\RepositoryInterface;

/**
 * Class WineSpectatorRepository
 * @codeCoverageIgnore
 */
class WineSpectatorRepository implements RepositoryInterface
{
    /**
     * @var Wine
     */
    public $wine;

    public function __construct(Wine $wine)
    {
        $this->wine = $wine;
    }

    public function getAll()
    {
        return $this->wine->query()
            ->orderBy('title', 'ASC')
            ->get();
    }

    public function find($id)
    {
        return $this->wine->find($id);
    }

    public function put($wine)
    {
        $model = $this->wine->find($wine['guid']);

        $wineModel = is_null($model)
            ? new Wine()
            : $model;

        $wineModel->fill($wine);

        if (is_null($model)) {
            $wineModel->setAttribute('guid', $wine['guid']);
        }

        return $wineModel->save();
    }

    public function delete($id)
    {
        return $this->wine->find($id)->delete();
    }
}
