#!/usr/bin/env php
<?

/*
	simple removal script for persons.

	call: ./remove_person.php 45
		where 45 is the person's id to be removed.
*/


require "db.php";

$id = $argv[1];

mysql_query("delete from personen where id = $id;");
mysql_query("delete from adressen where personen_id = $id;");
mysql_query("delete from klassenpersonen where personen_id = $id;");
?>
