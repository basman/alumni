<?php
require('top_file.php');
?>
<h3>Ergebnis der Suche</h3>
<?php

require("db.php");

# Datenbankabfrage aufbauen...

$q1 = "SELECT personen.id,personen_id,version FROM personen, adressen ";
$q2 = "";
$nichtEiner = 0; # Helfer, um Suche wie "alle Schüler" zu verhindern
$kriterien = 0;

if($_REQUEST[vorname] != "" && strlen($_REQUEST[vorname]) > 2 && !checkInjection($_REQUEST[vorname])) {
	$q2 .= " AND vorname LIKE '%".mysql_escape_string($_REQUEST[vorname])."%' ";
	$kriterien++;
}
if($_REQUEST[name] != "" && strlen($_REQUEST[name]) > 2 && !checkInjection($_REQUEST[name])) {
	$q2 .= " AND name LIKE '%".mysql_escape_string($_REQUEST[name])."%' ";
	$kriterien++;
}
if($_REQUEST[geboren] != "" && strlen($_REQUEST[geboren]) > 2 && !checkInjection($_REQUEST[geboren])) {
	$q2 .= " AND geboren LIKE '%".mysql_escape_string($_REQUEST[geboren])."%' ";
	$kriterien++;
}
if($_REQUEST[art] != "" && !checkInjection($_REQUEST[art])) {
	$q2 .= " AND FIND_IN_SET('".mysql_escape_string($_REQUEST[art])."', art) > 0 ";
	$kriterien++;
	$nichtEiner = 1;
}
if($_REQUEST[eintritt] != "" && strlen($_REQUEST[eintritt]) > 2 && !checkInjection($_REQUEST[eintritt])) {
	$q2 .= " AND eintritt LIKE '%".mysql_escape_string($_REQUEST[eintritt])."%' ";
	$kriterien++;
}
if($_REQUEST[austritt] != "" && strlen($_REQUEST[austritt]) > 2 && !checkInjection($_REQUEST[austritt])) {
	$q2 .= " AND austritt LIKE '%".mysql_escape_string($_REQUEST[austritt])."%' ";
	$kriterien++;
}
if($_REQUEST[beruf] != "" && strlen($_REQUEST[beruf]) > 2 && !checkInjection($_REQUEST[beruf])) {
	$q2 .= " AND beruf LIKE '%".mysql_escape_string($_REQUEST[beruf])."%' ";
	$kriterien++;
}
if($_REQUEST[adresse] != "" && strlen($_REQUEST[adresse]) > 3 && !checkInjection($_REQUEST[adresse])) {
	$q2 .= " AND adresse LIKE '%".mysql_escape_string($_REQUEST[adresse])."%' ";
	$kriterien++;
}
if($_REQUEST[email] != "" && strlen($_REQUEST[email]) > 3 && !checkInjection($_REQUEST[email])) {
	$q2 .= " AND email LIKE '%".mysql_escape_string($_REQUEST[email])."%' ";
	$kriterien++;
}
if($_REQUEST[homepage] != "" && strlen($_REQUEST[homepage]) > 4 && !checkInjection($_REQUEST[homepage])) {
	$q2 .= " AND homepage LIKE '%".mysql_escape_string($_REQUEST[homepage])."%' ";
	$kriterien++;
}

$query = $q1;
$query .= " WHERE personen_id = personen.id ";
if($q2 != "" && !($nichtEiner && $kriterien <= 1)) { 
	$query .= $q2;
} else {
	print "<h3>Anfrage verweigert:</h3>Es muss mindestens ein Suchkriterium angegeben 
werden und Suchkriterien m&uuml;ssen lang genug sein. Diese Einschr&auml;nkung
soll das Auslesen der gesamten Datenbank durch Sammler von Werbeadressen
erschweren. 
<p>\n<a href=\"search.php\">Neuer Versuch</a>\n";
	print "</div></div><div id=\"background_footer\"></div></body></html>\n";
	exit;
}
$query .= " ORDER BY personen.id,version DESC;"; 

print "\n<!-- Query: \"$query\" -->\n";

# zutreffende datensätze anfragen (nur IDs)
$result = mysql_query($query);

# ausgeben
if(($n=mysql_num_rows($result)) == 0) {
	print "<b>Keine &Uuml;bereinstimmungen</b><br>\n";
} else {
	# finde anzahl personen
	$countquery = "SELECT COUNT(personen_id) FROM personen, adressen WHERE personen_id = personen.id AND version = 0 " . $q2 . ";";
	$countresult = mysql_query($countquery);
	$p = mysql_result($countresult, 0, 0);
	print "<b>$p Person".($p != 1?"en":"")." ($n Adresse".($n != 1?"n":"").")</b>\n<p>\n";

	print "<table border=0>\n";
	$i = 0; # zählt Personen durch
	$oldID = -1;
	while($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if($oldID == $line[personen_id]) {
			continue;
		}
		$oldID = $line[personen_id];
		$i++;
		print "<tr><td colspan=2 bgcolor=\"blue\">$i ";
		if(count_addresses_person($line[personen_id]) > 1) {
			print "<a href=\"show_all_versions.php?person=$line[personen_id]\">".
			"alle bisherigen Adressen dieser Person anzeigen</a>";
		}
		print	" <!--a href=\"edit.php?person=$line[personen_id]\">&auml;ndern</a--></td></tr>\n";

		# konkrete Daten anfragen
		$query = "SELECT * FROM personen, adressen WHERE personen.id = personen_id AND personen.id = $line[personen_id] AND version = $line[version];";
		$resdata = mysql_query($query);
		$line2 = mysql_fetch_array($resdata, MYSQL_ASSOC);
		foreach($line2 as $field => $val) {
			if($field == "id" || $field == "eingetragen" || preg_match("/_id$/", $field)) continue;
			if($field == "adresse") $val = "<pre>".$val."</pre>";
			if($field == "seit") $field = "G&uuml;ltig seit";
			if($field == "art") { $val = ucfirst($val); $field = "Rolle"; }
			if($field == "eintritt") $field = "Eintrittsjahr";
			if($field == "austritt") $field = "Austrittsjahr";
			if($field == "beruf") $field = "Ausbildung/Beruf";
			if($field == "email") $val = "<a href=\"mailto:$val\">$val</a>";
			if($field == "homepage") $val = "<a href=\"". (preg_match("/^http:\/\//", $val)?"":"http://") ."$val\" target=\"_new\">$val</a>";
			if($field == "geboren") $val = substr($val, 0, 4);
			$field = ucfirst($field);
			print "<tr><td bgcolor=\"blue\" valign=top><font color=\"white\">$field</font></td><td bgcolor=\"#003070\">$val</td></tr>";
		}
		print "<tr><td colspan=2>&nbsp;</td></tr>\n";
	}
	print "</table>\n";
}

print "<a href=\"search.php\">Neue Suche</a>&nbsp;<a href=\"insertform.php\">Eingabe</a>";
?>
</div>
</div>
<div id="background_footer"></div>
</body>
</html>
