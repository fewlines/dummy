# fewlines - framework

#### Database usage

> $db = new Database;

##### SELECT 

> $table = $db->select('yourtable');

##### INSERT

> $insert = $table->insert(array(
							'username' => 'yourname', 
							'password' => 'yourpassword'
	);
	$insertResult = $insert->execute();

