<?php
interface MysqlManagerInterface
{
    public function sanitize($value): string;

    public function getContracts(int $sort, array $params): array;

    public function addOrderByToSql(string $sql, int $sort): string;
}