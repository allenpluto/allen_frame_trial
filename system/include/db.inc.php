<?php
// Include Class Object
// Name: db
// Description: database functions

// Database Related Functions


class db
{
	private static $_conn = null;

	function db_get_connection()
	{
		if (!self::$_conn)
		{
			$dbLocation = 'mysql:dbname='.DATABASE_NAME.';host='.DATABASE_HOST;
			$dbUser = DATABASE_USER;
			$dbPass = DATABASE_PASSWORD;
			$db = new PDO($dbLocation, $dbUser, $dbPass);

			self::$_conn = $db;
		}

		return self::$_conn;
	}
}
