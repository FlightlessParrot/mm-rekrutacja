<?php

interface MysqlManagerInterface
{
    public function sanitize($value): string;

    public function getContracts(int $sort, array $params): array;

    public function addWhere(array $params): string;

    public function bindValue(array $paramsWithPdoType, PDOStatement $stmt): void;

    public function addOrderByToSql(string $sql, int $sort): string;
}