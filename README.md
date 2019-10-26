# Creta - PHP Fluent MySql Micro ORM
A simple PHP Micro MySQL ORM with Fluent Interface

## Features

- Easy to use.
- Fluent Design and Interface.
- Required minimum dependency i.e. only PHP >=5.3.0.
- Prevent from SQL Injections.
- Exceptional error reporting (with hints, context)

## Installation (with [Composer](https://getcomposer.org))

```bash
composer require kundansingh86/creta
```
To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once('vendor/autoload.php');
```

## Getting Started

Add namespace and create context.

```php
use Creta\MySqlDbContext;


$properties = [
        'host'=>'localhost',
        'username'=>'',
        'password'=>'',
        'database'=>'test'
];

$context = new MySqlDbContext($properties);
```

### Insert Statement

Insert the data to a table, get the generated inserted ID of the record;

```php
$personId = $context->table('person')           // table 
                ->insert(['name'=>"Jhon",       // column with name value pair
                        'age'=>25, 
                        'salary' => '3000', 
                        'department' => 'sales', 
                        'position' => 'executive'])
                ->execute();

echo 'Inserted Person ID :: Jhon ', $personId; 
```

### Update Statement

Update the data to a table with where condition

```php
$context->table('person')     // table
        ->update(['position'=>'assistant manager', 'salary' => '2800'])     // column with name value pair
        ->where(['name' => 'David', 'department'=>'finance']) // AND condition with name value pair
        ->execute();
```

### Delete Statement

Delete the data from a table with where condition

```php
$context->table('person')   // table
        ->delete()
        ->where(['id' => 10])   // condition with name value pair
        ->execute();
```

To delete all records from the table, don't specify the where condition

```php
$context->table('person')   //table
        ->delete()
        ->execute();
```

### Select Statement

Select all columns from the table with the where condition

```php
$result = $context->table('person') // table
                  ->select()
                  ->where(['id' => 2, 'position' => 'manager'])  // condition with name value pair 
                  ->execute();

echo '<pre>';
print_r($result);
```

Select specific columns from the table

```php
$result = $context->table('person') // table
                  ->select(["id", "name", "position"]) // column name array
                  ->execute();

echo '<pre>';
print_r($result);
```

### Query Output

See the sql query output of any statement

```php
echo $context->table('person') // table
             ->select()
             ->where(['id' => 2, 'position' => 'manager'])  // condition with name value pair 
             ->query(); // returns the generated sql query
```

### Close Context (Recommended)

Close the context and connection when operations are over

```php
$context->close();
```
