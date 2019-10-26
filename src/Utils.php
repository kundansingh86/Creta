<?php
namespace Creta;

class Utils {

    static function getValueType($value) {
        switch(gettype($value)) {
            case 'integer':
                return 'i';
            case 'double':
                return 'd';
            case 'string':
                return 's';
            default:
                return 'b';
        }
    }

    static function isAssociativeArray(array $array) {
        if(array_keys($array) !== range(0, count($array) - 1)) {
            return true;
        }
    }

    static function isSequentialArray(array $array) {
        return !(Utils::isAssociativeArray($array));
    }

}