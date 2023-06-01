<?php

namespace App\System\Interfaces;

interface ActiveRecord
{
    public function create(array $data);

    public function read();

    public function update(array $data, string $condition);

    public function delete(string $condition);
}