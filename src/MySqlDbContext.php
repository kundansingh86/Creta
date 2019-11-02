<?php

namespace Creta;

spl_autoload_register(function($className) {
    $parts = explode('\\', $className);
    $className = end($parts);
    
    switch($className) {
        case 'Operations':
        case 'Conjunctions':
            $className = 'Enums';
            break;
    }

	include_once $className. '.php';
});

class MySqlDbContext implements iDbContext {
    private $connection;
    private $table;
    private $columns;
    private $operation = 0;
    private $sqlQueryStmt;


    function __construct($properties) {
        $this->connection = new \mysqli($properties['host']
            , $properties['username']
            , $properties['password']
            , $properties['database']);

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    private function setOperation($operation) {
        if($this->operation > 0) {
            die("Only one operation among Insert, Update, Select and Delete is allowed at a time.");
        }

        $this->operation = $operation;
    }

    public function table($table) {
        $this->table = $table;
        $this->sqlQueryStmt = new SqlQueryStatement($this->table);
        return $this;
    }

    public function insert(array $columns) {
        if(Utils::isAssociativeArray($columns)) {
            $this->setOperation(Operations::INSERT);
            $this->columns = $columns;
            $this->sqlQueryStmt->insertQuery($columns);
            return $this;
        }

        die('Sequential array is not allowed');
    }

    public function update(array $columns) {
        if(Utils::isAssociativeArray($columns)) {
            $this->setOperation(Operations::UPDATE);
            $this->columns = $columns;
            $this->sqlQueryStmt->updateQuery($columns);
            return $this;
        }

        die('Sequential array is not allowed');
    }

    public function delete() {
        $this->setOperation(Operations::DELETE);
        $this->sqlQueryStmt->deleteQuery();
        return $this;
    }

    public function select(array $columns = NULL) {
        if($columns == null || Utils::isSequentialArray($columns)) {
            $this->setOperation(Operations::SELECT);
            $this->columns = $columns;
            $this->sqlQueryStmt->selectQuery($columns);
            return $this;
        }

        die('Associative array is not allowed');
    }

    private function buildConditionStatement(array $conditions, $conjuction) {
        if(Utils::isAssociativeArray($conditions)) {
            $this->sqlQueryStmt->whereClause($conditions, $conjuction);
            return $this;
        }

        die('Sequential array is not allowed');
    }

    private function buildOrderStatement($asc, $columns) {
        if(Utils::isSequentialArray($columns)) {
            $this->sqlQueryStmt->orderByClause($asc, $columns);
            return $this;
        }

        die('Associative array is not allowed');
    }

    public function where(array $conditions) {
        return $this->buildConditionStatement($conditions, Conjunctions::AND);
    }

    public function whereOr(array $conditions) {
        return $this->buildConditionStatement($conditions, Conjunctions::XOR);
    }

    public function orWhere(array $conditions) {
        return $this->buildConditionStatement($conditions, Conjunctions::OR);
    }

    public function withAnd(array $conditions) {
        return $this->buildConditionStatement($conditions, Conjunctions::WITH_AND);
    }

    public function withOr(array $conditions) {
        return $this->buildConditionStatement($conditions, Conjunctions::WITH_OR);
    }

    public function orderBy(...$columns) {
        return $this->buildOrderStatement(true, $columns);
    }

    public function orderByDesc(...$columns) {
        return $this->buildOrderStatement(false, $columns);
    }

    public function limit($limit, $offset = 0) {
        $this->sqlQueryStmt->limit($limit, $offset);
        return $this;
    }

    public function query() {
        list($query, $types, $vars) = $this->sqlQueryStmt->end();
        $this->operation = 0;
        return $query;
    }

    public function execute() {
        try {
            list($query, $types, $vars, $ref) = $this->sqlQueryStmt->end();
           
            if($stmt = $this->connection->prepare($query)) {
                if($stmt === false) {
                    throw new Exception('Wrong SQL: ' . $query );
                }

                if(!empty($vars)) {
                    call_user_func_array(array($stmt,'bind_param'), $ref);
                }

                $stmt->execute();

                switch($this->operation) {
                    case Operations::INSERT:
                        return $this->connection->insert_id;
                    case Operations::UPDATE:
                    case Operations::DELETE:
                        return $stmt->affected_rows === 0 
                            ? "No rows are affected"
                            : "$stmt->affected_rows row(s) are affected";
                    case Operations::SELECT:
                        $result = $stmt->get_result();
                        return $result->fetch_all(MYSQLI_ASSOC);
                }

            } else {
                var_dump($this->connection->error);
            }
        }
        catch(Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), '\n Error: ' , $this->connection->errno , ' ' , $this->connection->error;
        }
        finally {
            $this->operation = 0;
            if($stmt === true) $stmt->close();
        }        
    }

    public function close() {
        if(isset($this->connection)) {
            $this->connection->close();
            $this->connection = null;
        }
    }
}
