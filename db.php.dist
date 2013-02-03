<?
$PHP_SELF = $_SERVER[PHP_SELF];

$link = mysql_connect("localhost", "XXXXX", "YYYYYYZZZZZZ")
    or die("Keine Datenbankverbindung möglich!");
mysql_select_db("webssk_alumni")
    or die("Auswahl der Datenbank fehlgeschlagen");

# returns the number of addresses of the given person
function count_addresses_person($pid) {
	$query = "SELECT count(adressen.id) FROM personen,adressen WHERE personen.id = '$pid' AND personen.id = personen_id";
 	
	return getval($query);
}

# perform SQL query and return single value (first row, first column)
function getval($q) {
	$r = mysql_query($q);
    if(mysql_num_rows($r) > 0) {
    	$l = mysql_fetch_row($r);
	    return $l[0];
    }    
    return;
}

function show_error() {
	print "<!-- ".mysql_error()." -->";
}

function checkInjection($fieldValue) {
	if(preg_match('/^\s+$/', $fieldValue) ||
	   preg_match('/[<>";]/', $fieldValue) ) {
		return true;
	}
	return false;
}
?>
