<?php

namespace App\Repositories\RepositoryInterface;

/**
 * Interface RepositoryInterface
 * @codeCoverageIgnore
 */
interface RepositoryInterface
{
    public function getAll();

    public function find($id);

    public function delete($id);
}
