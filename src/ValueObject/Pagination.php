<?php
declare(strict_types=1);

namespace App\ValueObject;
final class Pagination
{
    private int $limit;
    private  int $offset;

    public function __construct(int $limit = 20, int $offset = 0)
    {
        if ($limit < 20 || $limit > 100){
            throw new \InvalidArgumentException("Invalid limit: $limit");
        }

        if ($offset < 0){
            throw new \InvalidArgumentException("Invalid offset: $offset");
        }

        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function offset(): int
    {
        return $this->offset;
    }
}