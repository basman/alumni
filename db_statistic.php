<?php
require('top_file.php');
?>

<h1>Datenbank Statistik</h1>
<?
require("db.php");

function telltable($q, $limit) {
	$result = mysql_query($q);
	$i = 0;
	while($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if($i >= $limit) break;
		if($i == 0) {
			print "  <tr>";
			foreach($line as $f => $v) {
                if($f != "geboren") {
    				print "<th>$f</th>";
                }    
			}
			print "</tr>\n";
		}
		print "  <tr>";
		foreach($line as $f => $v) {
            if($f != "geboren") {
    			print "<td>$v</td>";
            }
		}
		print "</tr>\n";
		$i++;
	}
}

function tellval($val, $text) {
  print "<tr><td>$text</td><td>$val</td></tr>\n";
}

function tell($q, $text) {
  $result = mysql_query($q);
  $line = mysql_fetch_row($result);
  $val = $line[0];
  print "<tr><td>$text</td><td>$val</td></tr>\n";
  return $val;
}

print "<h2>Kennzahlen</h2>
<table border=1>\n";

# anzahl personen
$query = "SELECT COUNT(id) FROM personen;";
$personen = tell($query, "Personen");
# anzahl adressen
$query = "SELECT COUNT(id) FROM adressen;";
$adressen = tell($query, "Adressen");

# anzahl adressen/person im Schnitt
tellval($personen != 0?sprintf('%.2f',$adressen/$personen):0, "Adressen pro Person im Schnitt");

# klassen
$klassen = getval("SELECT COUNT(id) FROM klassen");
tellval($klassen, "Klassen");

# personen pro klasse im schnitt
mysql_query("create temporary table tmp (n int NULL)");
mysql_query("insert into tmp select count(personen_id) as n from klassenpersonen group by klassen_id") || print mysql_error();
$sum = getval("select sum(n) from tmp");
mysql_query("drop table tmp") || print mysql_error();
tellval($klassen != 0?sprintf('%.2f', $sum/$klassen):0, "Personen pro Klasse im Schnitt");

# letzter Eintrag getätigt
$query = "SELECT eingetragen FROM adressen ORDER BY eingetragen DESC;";
$last = tell($query, "Neuester Eintrag");
# erster Eintrag getätigt
$query = "SELECT eingetragen FROM adressen ORDER BY eingetragen ASC;";
$first = tell($query, "&Auml;ltester Eintrag");

# Einträge pro Tag im Schnitt
$t_last = preg_replace("/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/", 
	"\\1,\\2,\\3,\\4,\\5,\\6", $last);
$a = preg_split("/,/", $t_last);
$t_last = mktime($a[3], $a[4], $a[5], $a[1], $a[2], $a[0]);

$t_first = preg_replace("/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/", 
	"\\1,\\2,\\3,\\4,\\5,\\6", $first);
$a = preg_split("/,/", $t_first);
$t_first = mktime($a[3], $a[4], $a[5], $a[1], $a[2], $a[0]);

if($t_last == $t_first) { $t_last+=3600*24; } // avoid division by zero
$entriesPerDay=$adressen * 3600 * 24 / ($t_last-$t_first);
if($entriesPerDay < 1.0 && $entriesPerDay > 0) {
	$entriesPerDay = '1/' . sprintf("%.2f", 1/$entriesPerDay);
} else {
	$entriesPerDay = sprintf("%.2f", $entriesPerDay);
}
tellval($entriesPerDay, "Eintr&auml;ge pro Tag im Schnitt");
print "</table>\n";

# 10 zuletzt geänderte Adressen
/*
print "<h2>10 zuletzt ge&auml;nderte Eintr&auml;ge</h2>
<table border=1>\n";

$query = "SELECT personen.* FROM personen, adressen WHERE personen.id = personen_id ORDER BY adressen.eingetragen DESC;";

telltable($query, 10);

print "</table>\n";
*/
?>

</div>
</div>
<div id="background_footer"></div>
</body>
</html>
