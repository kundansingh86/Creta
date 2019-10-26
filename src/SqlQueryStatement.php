<?php
namespace Creta;

class SqlQueryStatement {
    
    private $table;
    private $queryStmt = '';
    private $conditionStmt = '';

    public $types = '';
    public $vars = [];

    function __construct($table) {
        $this->table = $table;
    }

    private function buildColumnTypes(array $columns) {
        if(sizeof($columns) > 0) {
            foreach($columns as $key => $value) {
                $this->vars[] = $value;
                $this->types .= Utils::getValueType($value);
            }
        }
    }

    private function buildConditionTypes(array $conditions, $operator) {    
        $valueSets = [];
        foreach($conditions as $key => $val) {
            $values = [];    
            if(is_array($val)) {
                $values = $val;
            } else {
                $values[] = $val;
            }

            foreach($values as $value) {

                $keyArray = explode(' ', trim($key));

                if(sizeof($keyArray) > 1) {
                    $valueSets[] = "$this->table.$keyArray[0] $keyArray[1] ?";
                } 
                else {
                    $valueSets[] = "$this->table.$key = ?";
                }

                $this->vars[] = $value;
                $this->types .= Utils::getValueType($value);
            }
        }
        
        return '(' . implode(' '.$operator.' ', $valueSets) . ' TBR )';
    }

    function insertQuery(array $columns) {
        $keys = implode(', ', array_keys($columns));
        $values = trim(str_repeat("?, ", sizeof($columns)), ', '); 
        
        $this->buildColumnTypes($columns);
        $this->queryStmt = "INSERT INTO $this->table ($keys) VALUES ($values)";
    }

    function updateQuery(array $columns) {
        $valueSets = array();
        foreach($columns as $key => $value) {
            $valueSets[] = $this->table . '.' . $key . " = ?";
        }

        $this->buildColumnTypes($columns);
        $query = implode(', ', $valueSets);

        $this->queryStmt = "UPDATE $this->table SET $query";
    }

    function deleteQuery() {
        $this->queryStmt = "DELETE FROM $this->table";
    }

    function selectQuery($columns) {
        $columnsString = '*';

        if($columns != null || sizeof($columns) > 0) {
            $valueSets = array();
            foreach($columns as $key => $value) {
                $valueSets[] = $this->table . '.' . $value;
            }

            $columnsString = implode(', ', $valueSets);
        }


        $this->queryStmt = "SELECT $columnsString FROM $this->table";
    } 

    function conditionQuery(array $conditions, $conjunction) {
        if(sizeof($conditions) > 0) {
            switch($conjunction) {
                case Conjunctions::AND:
                    $cond = strlen($this->conditionStmt) <= 0 ? ' ' : ' AND ';
                    $this->conditionStmt .= $cond. $this->buildConditionTypes($conditions, 'AND');
                    break;
                case Conjunctions::OR:
                    $cond = strlen($this->conditionStmt) <= 0 ? ' ' : ' OR ';
                    $this->conditionStmt .= $cond . $this->buildConditionTypes($conditions, 'AND');
                    break;
                case Conjunctions::XOR:
                    $cond = strlen($this->conditionStmt) <= 0 ? ' ' : ' AND ';
                    $this->conditionStmt .= $cond . $this->buildConditionTypes($conditions, 'OR');
                    break;
                case Conjunctions::WITH_AND:
                    $cond = ' AND '. $this->buildConditionTypes($conditions, 'OR');
                    $this->conditionStmt = str_replace(' TBR ', $cond, $this->conditionStmt);
                    break;
                case Conjunctions::WITH_OR:
                    $cond = ' OR '. $this->buildConditionTypes($conditions, 'AND');
                    $this->conditionStmt = str_replace(' TBR ', $cond, $this->conditionStmt);
                    break;
            }
        }

    }

    function end() {
        if(strlen($this->conditionStmt) > 0) {
            $this->conditionStmt = str_replace(' TBR ', '', $this->conditionStmt);
            $this->queryStmt .= ' WHERE ' . $this->conditionStmt;
        }

        $refParams = array();

        $refParams[] = & $this->types;
        for($i = 0; $i < strlen($this->types); $i++) {
          $refParams[] = & $this->vars[$i];
        }

        return [
            $this->queryStmt,
            $this->types,
            $this->vars,
            $refParams
        ];
    }
}