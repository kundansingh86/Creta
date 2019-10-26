<?php 

require_once __DIR__ . '/vendor/autoload.php'; // Autoload files using Composer autoload
use Creta\MySqlDbContext;


$properties = [
        'host'=>'localhost',
        'username'=>'',
        'password'=>'',
        'database'=>'test'
];

$context = new MySqlDbContext($properties);

// INSERT STATEMENT

$personId = $context->table('person')
                ->insert(['name'=>"Jhon", 
                        'age'=>25, 
                        'salary' => '3000', 
                        'department' => 'sales', 
                        'position' => 'executive'])
                ->execute();

echo 'Inserted Person ID :: Jhon ', $personId, '<br></br>'; 


$personId = $context->table('person')
                ->insert(['name'=>"Peter", 
                        'age'=>30, 
                        'salary' => '4000', 
                        'department' => 'sales', 
                        'position' => 'manager'])
                ->execute();

echo 'Inserted Person ID :: Peter ', $personId, '<br></br>'; 


$personId = $context->table('person')
                ->insert(['name'=>"David", 
                        'age'=>28, 
                        'salary' => '2000', 
                        'department' => 'finance', 
                        'position' => 'assistant'])
                ->execute();

echo 'Inserted Person ID :: David ', $personId, '<br></br>'; 

$personId = $context->table('person')
                ->insert(['name'=>"Anthony", 
                        'age'=>30, 
                        'salary' => '4500', 
                        'department' => 'finance', 
                        'position' => 'manager'])
                ->execute();

echo 'Inserted Person ID :: Anthony ', $personId, '<br></br>'; 

// UPDATE STATMENT

echo $context->table('person')
        ->update(['position'=>'assistant manager', 'salary' => '2800'])
        ->where(['name' => 'David', 'department'=>'finance'])
        ->execute(), '<br><br>';


// DELETE STATEMENT

echo $context->table('person')
        ->delete()
        ->where(['id' => 10])
        ->execute(), '<br><br>';


// SELECT STATEMENTS
$result = $context->table('person')
                ->select()
                ->where(['id' => 2])
                ->execute();

echo '<pre>';
print_r($result);

//Close the connection
$context->close();