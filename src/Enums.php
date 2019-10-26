<?php

namespace Creta;

class Operations {
    const INSERT = 1;
    const UPDATE = 2;
    const SELECT = 3;
    const DELETE = 4;
}

class Conjunctions {
    const AND = 'AND';
    const OR = 'OR';
    const XOR = 'XOR';
    const WITH_AND = 'WITH_AND';
    const WITH_OR = 'WITH_OR';
}