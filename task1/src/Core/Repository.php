<?php

namespace App\Core;

use PDO;

abstract class Repository
{
    /**
     * MySQL database connection
     *
     * @var \PDO
     */
    protected PDO $db;

    /**
     * Repository construct
     *
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->db = $connection;
    }
}
