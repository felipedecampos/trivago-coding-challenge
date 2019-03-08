<?php

namespace App\Repositories\RepositoryInterface;


interface RepositoryInterface
{
    public function getAll();

    public function find($id);

    public function delete($id);
}