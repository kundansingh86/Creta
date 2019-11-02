<?php

namespace Creta;

interface iDbContext {
    public function table($table);
    public function insert(array $columns);
    public function update(array $columns);
    public function delete();
    public function select(array $columns);
    public function where(array $conditions);
    public function whereOr(array $conditions);
    public function orWhere(array $conditions);
    public function withAnd(array $conditions);
    public function withOr(array $conditions);
    public function orderBy(...$columns);
    public function orderByDesc(...$columns);
    public function limit($limit, $offset);
    public function query();
    public function execute();
    public function close();
}