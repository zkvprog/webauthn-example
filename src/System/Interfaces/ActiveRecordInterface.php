<?php

namespace App\System\Interfaces;

interface ActiveRecordInterface
{
    public function create(array $data);

    public function read();

    public function update(array $data, string $condition);

    public function delete(string $condition);
}