<?php

$_REQUEST['action'] = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;

switch($_REQUEST['action'])
{
	case 'install' :
		$objDb = new NeoDB('objDb');

		$host = $_REQUEST['host'];
		$dbname  = $_REQUEST['dbname'];
		$password  = $_REQUEST['password'];
		$username  = $_REQUEST['username'];

		$cnx = $objDb->getDB(array ('host' => $host,'username' => $username,'password' => $password,'dbname'   => $dbname));

		$cnx->query('CREATE DATABASE IF NOT EXISTS cms_' . $_REQUEST['dbname']);
		$cnx->query('USE ' . $_REQUEST['dbname']);

		$sql = file_get_contents(__DIR__ . '/cms.sql', 'r');
		$cnx->query($sql);
		break;



	default :

		echo '
			<form>
				<h1>Cr�ation de la DB</h1>
				<ul>
					<li>Host
					<input type="text" name="host" /></li>
					<li>DB Name
					<input type="text" name="dbname" /></li>
					<li>User DB name
					<input type="text" name="username" /></li>
					<li>user DB password
					<input type="password" name="password" /></li>
				</ul>
				<input type="hidden" name="action" value="install" />
				<input type="submit" />
			</form> 
	
	';

		break;
}

