<?php
require('top_file.php');
?>
<?
require "db.php";

# find current person

if(!isset($_REQUEST[person_id])) {

	if(checkInjection($_REQUEST[name]) ||
	   checkInjection($_REQUEST[vorname]) ||
	   checkInjection($_REQUEST[geboren])) {
		print "Error: invalid input\n";
		exit;
	}

	$q = "SELECT * FROM personen WHERE name = '".mysql_escape_string($_REQUEST[name])."' AND vorname = '".mysql_escape_string($_REQUEST[vorname])."' AND geboren = '".mysql_escape_string($_REQUEST[geboren])."'";
	$person = mysql_query($q);

	if(mysql_num_rows($person) == 0) {
		print "<h3>Anfrage verweigert:</h3> Person unbekannt.<p><b>Bemerkung:</b><br>Klassenlisten k&ouml;nnen nur angezeigt werden, wenn die geforderten Angaben korrekt sind.
<p>
<form action=\"classlist_show.php\" method=\"post\">
<input type=submit value=\"Erneut versuchen\">
<input type=hidden name=\"name\" value=\"$_REQUEST[name]\">
<input type=hidden name=\"vorname\" value=\"$_REQUEST[vorname]\">
<input type=hidden name=\"geboren\" value=\"$_REQUEST[geboren]\">
</form>\n
</div>\n
</div>\n
<div id=\"background_footer\"></div>\n
</body>\n
</html>\n";
		exit;
	}
} else {
	$person = mysql_query("SELECT * FROM personen WHERE id = '".mysql_escape_string($_REQUEST[person_id])."'");
}

$p = mysql_fetch_assoc($person);
$person_id = $p[id];
print "<h3>Klassenliste von $p[vorname] $p[name]</h3>\n";

# find all classes this person belongs to
$classes = mysql_query("SELECT klassen.* FROM klassen, klassenpersonen WHERE personen_id = '$person_id' AND klassen.id = klassen_id");

if(mysql_num_rows($classes) == 0) {
	print "Du bist noch keiner Klasse zugeordnet. ";

	if($p[art] == 'lehrer' || $p[art] == 'schüler') {
		print "Aber <a href=\"editform.php?name=".urlencode($_REQUEST[name])."&vorname=".urlencode($_REQUEST[vorname])."&geboren=".urlencode($_REQUEST[geboren])."\">das l&auml;sst sich &auml;ndern...</a>\n";
	} else {
		print "Klassenzuordnungen k&ouml;nnen nur f&uuml;r Lehrer und Sch&uuml;ler erstellt werden.\n";
	}
	exit;
} else if(mysql_num_rows($classes) > 1) {
	print "<form action=\"$PHP_SELF\" method=\"post\">
<font size=+1>W&auml;hle eine Klasse aus: </font>
<select name=\"class_id\">\n";
	while($line = mysql_fetch_assoc($classes)) {
		if($_REQUEST[class_id]==$line[id] || !isset($selclass))
			$selclass = $line;
		print "<option value=\"$line[id]\"".($_REQUEST[class_id]==$line[id]?' selected':'').">$line[beginn]-$line[ende] $line[kommentar]</option>\n";
	}
	print "</select>
	<input type=submit value=\"Anzeigen\">
	<input type=hidden name=\"person_id\" value=\"$person_id\">
	<input type=hidden name=\"sort\" value=\"$_REQUEST[sort]\">
	<input type=hidden name=\"sortdir\" value=\"$_REQUEST[sortdir]\">
</form>";
} else { # genau eine Klasse vorhanden
	$selclass = mysql_fetch_assoc($classes);
}

# find all class members and their newest address
mysql_query("CREATE TEMPORARY TABLE tmp ( personen_id INT DEFAULT '0' , version INT )") || show_error();
mysql_query("LOCK TABLES adressen READ, personen READ, klassenpersonen READ") || show_error();
mysql_query("INSERT INTO tmp SELECT adressen.personen_id, MAX(version) FROM adressen,klassenpersonen WHERE klassenpersonen.personen_id = adressen.personen_id AND klassenpersonen.klassen_id = '$selclass[id]' GROUP BY personen_id") || show_error();
$q = "SELECT * FROM personen, adressen, tmp WHERE tmp.personen_id = personen.id AND adressen.personen_id = personen.id AND tmp.version = adressen.version ORDER BY art";
if(!empty($_REQUEST[sortdir])) {
	$q .= ",".mysql_escape_string($_REQUEST[sort])." ".mysql_escape_string($_REQUEST[sortdir]);
}
$members = mysql_query($q);
if(!$members) show_error();
mysql_query("UNLOCK TABLES") || show_error();
mysql_query("DROP TABLE tmp") || show_error();


# show huge table of selected class and their members (teachers first)
# action field: expand to all addresses

print "<table border=0>
<tr><td bgcolor=\"#003070\">Einschulung</td><td bgcolor=\"#003070\">$selclass[beginn]</td></tr>
<tr><td bgcolor=\"#003070\">Ende</td><td bgcolor=\"#003070\">$selclass[ende]</td></tr>
<tr><td bgcolor=\"#003070\">Kommentar</td><td bgcolor=\"#003070\">$selclass[kommentar]</td></tr>
<tr><td bgcolor=\"#003070\">Personen</td><td bgcolor=\"#003070\">".mysql_num_rows($members)."</td></tr>
</table>\n<p>\n";
print "<table border=0>\n";

# print header line
$fields = array(
	"Vorname" => "vorname",
	"Name" => "name",
	"Geboren" => "geboren",
	"Eintritt" => "eintritt",
	"Austritt" => "austritt",
	"Beruf" => "beruf",
	"Adresse" => "adresse",
	"Email" => "email",
	"Homepage" => "homepage",
	"Adresse g&uuml;ltig seit" => "seit");
print "<tr>";
$argv="";
foreach($_REQUEST as $k => $v) {
	if($k == 'sort' || $k == 'sortdir') continue;
	$argv .= $k . '=' . urlencode($v) . '&';
}
if($_REQUEST[sortdir] == "ASC") {
	$sortdirnew = "DESC";
} else {
	$sortdirnew = "ASC";
}
foreach($fields as $k => $v) {
	$myargv = $argv . "sort=$v&sortdir=$sortdirnew";
	print "<th bgcolor=\"#000070\"><a href=\"$PHP_SELF?$myargv\">$k</a></th>";
}
print "</tr>\n";

# loop over all members
while($line = mysql_fetch_assoc($members)) {
	print "<tr>";
	reset($fields);
	foreach($fields as $key => $val) {
		$color_start = '<font color="white">';
		$color_stop = '';
		if($line[art] == 'lehrer') {
			$color_start = "<font color=\"red\">";
		} else if($line[personen_id] == $person_id) {
			$color_start = "<font color=\"green\">";
		}
		if(!empty($color_start)) $color_stop = "</font>";
		if($key == "Adresse") $line[$val] = '<pre>'.$line[$val].'</pre>';
		if($key == 'Email') $line[$val] = "<a href=\"mailto:$line[$val]\">$line[$val]</a>";
		if($key == 'Homepage') {
			$protocol = '';
			if(!preg_match('/^http:\/\//', $line[$val])) $protocol = 'http://';
			$line[$val] = "<a href=\"$protocol$line[$val]\" target=\"_new\">$line[$val]</a>";
		}
		if($key == 'Geboren') {
			$line[$val] = substr($line[$val], 0, 4);
		}
		print "<td bgcolor=\"#003070\">$color_start$line[$val]$color_stop</td>";
	}
	print "</tr>\n";
} 
print "</table>\n";
print "<p><table bgcolor=\"#003070\"><tr><td valign=\"top\"><font size=-1>Bedeutung der Farben:</font></td><td><font color=\"red\" size=-1>Lehrer</font><br><font color=\"green\" size=-1>Du selbst</font>
</td></tr></table>";
?>

<p>
Die Liste kann nach allen Spalten sortiert werden, indem man auf den Spaltenkopf klickt. Erneutes Klicken kehrt die Sortierreihenfolge um.
</div>
</div>
<div id="background_footer"></div>
</body>
</html>
