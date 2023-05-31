<?php

namespace App\Repositories;

use App\System\AbstractRepository;

class UserRepository extends AbstractRepository
{
    protected string $table = 'users';
}