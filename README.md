# fewlines - framework

### Database usage
```php
$db = new \Fewlines\Component\Database\Database();
```

##### SELECT 
```php
$table = $db->select('yourtable');
```

##### TRUNCATE
```php
$truncate = $table->truncate();
$result = $truncate->execute();
```

##### DROP (Table)
```php
$dropTable = $dropTable->drop();
$result = $dropTable->execute();
```

##### CREATE (Table)
```php
$result = $db->createTable('yourtable', array(
	'id' => array(
		'type' => 'int',
		'autoIncreament' => true,
		'notNull' => true,
		'index' => 'primary'
	),
	'username' => array(
		'type' => 'varchar'
		'length' => 255
	),
	'password' => array(
		'type' => 'password',
		'length' => 255
	)
));
```

##### INSERT
```php
$insert = $table->insert(array(
	'username' => 'yourname', 
	'password' => 'yourpassword'
));

$result = $insert->execute();
```

##### UPDATE
```php
$update = $table->update(array(
	'username' => 'updatedcontent', 
	'password' => 'updatedpassword'
));

$update->where(array('id', '=', 1, 'OR'))
       ->where(array('id', '=', 5));

$result = $upate->execute();
```

##### DELETE
```php
$records = $table->where(array('id', '=', 2, 'AND'))
                 ->where(array('id', '=', 3));

$result = $records->delete()->execute();
```

##### FETCH
```php
$fetch = $db->select('yourtable', '*');
$fetch = $fetch->where(array('id', '>', 1))
               ->where(array('id', '<', 4))
               ->where(array('username', 'LIKE', '%bot%'))
               ->limit(0, 5);

$result = $fetch->fetchAll();
```
