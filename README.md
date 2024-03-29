# Creta - PHP Micro ORM with MySQL
A simple PHP Micro MySQL ORM with Fluent Interface

## Features

- Easy to use.
- Fluent Design and Interface.
- Required minimum dependency i.e. only PHP >=5.3.0.
- Prevent from SQL Injections.
- Where conditions in hierarchy with AND and OR operators.
- Order by clause with ascending and descending.
- Limit and offset.

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
                  ->orderBy('name') // order by name in ascending
                  ->execute();

echo '<pre>';
print_r($result);
```

Select specific columns from the table

```php
$result = $context->table('person') // table
                  ->select(["id", "name", "position"]) // column name array
                  ->orderByDesc('salary', 'position') // order by salary then by position in descending
                  ->limit(2, 1) // limit with 2 records and skip the 1st record
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
             ->orderBy('name') // order by name in ascending
             ->query(); // returns the generated sql query
```

### Close Context (Recommended)

Close the context and connection when operations are over

```php
$context->close();
```
*Note: A test.sql and test.php files are available in the repo for demo purpose.*

## Advance Topics

Where conditions formation in various scenarios with AND & OR Cojuctions and other Operators i.e <, >, <= , >=, like

#### Where clause with AND and OR operators

###### **Example 1:** SELECT * FROM person where id = 2 AND position = 'manager'

```php
$result = $context->table('person')
                  ->select()
                  ->where(['id' => 2, 'position' => 'manager'])  
                  ->execute();
```

###### **Example 2:** SELECT * FROM person WHERE (position = 'manager' OR (salary >= 2000 AND salary <= 3000))

```php
$result = $context->table('person')
                  ->select()
                  ->where(['position' => 'manager'])
                  ->withOr(['salary >=' => 2000, 'salary <=' => 3000]) 
                  ->execute();
```

###### **Example 3:** SELECT * FROM person WHERE (salary > 3000 AND (position = 'manager' OR position = 'executive'))

```php
$result = $context->table('person')
                  ->select()
                  ->where(['salary >' => 3000])
                  ->withAnd(['position' => ['manager', 'executive']]) 
                  ->execute();
```

###### **Example 4:** SELECT * FROM person WHERE position = 'manager' OR position = 'executive'

```php
$result = $context->table('person')
                  ->select()
                  ->whereOr(['position' => ['manager', 'executive']]) 
                  ->execute();
```

###### **Example 5:** SELECT * FROM person WHERE (age > 20 AND age < 22) OR (age > 25 AND age < 28) ORDER BY name asc

```php
$result = $context->table('person')
                  ->select()
                  ->where(['age >' => 20, 'age <' => 22]) 
                  ->orWhere(['age >' => 25, 'age <' => 28])
                  ->orderBy('name')  
                  ->execute();
```

## Roadmap

- Insert mutliple records at once.
- Group by and Having Clause.
- Aggregate Functions.
- JOIN in tables.

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](https://choosealicense.com/licenses/mit/)