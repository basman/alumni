<?php
require('top_file.php');
?>
<?

require("db.php");

# Formularwerte überprüfen (Datumformat etc.)
$bad = 0;
$badfields = "";
$aufforderung = "Bitte ausf&uuml;llen";
if(strlen($_REQUEST[vorname]) < 3 || preg_match("/$aufforderung/", $_REQUEST[vorname]) || checkInjection($_REQUEST[vorname])) {
	$bad++; 
	$badfields .= "vorname ";
	$_REQUEST[vorname] = $aufforderung;
}
if(strlen($_REQUEST[name]) < 3 || preg_match("/$aufforderung/", $_REQUEST[name]) || checkInjection($_REQUEST[name])) {
	$bad++; 
	$badfields .= "name ";
	$_REQUEST[name] = $aufforderung;
}
if($_REQUEST[geboren] == "" || preg_match("/$aufforderung/", $_REQUEST[geboren]) || checkInjection($_REQUEST[geboren])) {
	$bad++;
	$badfields .= "geboren ";
	$_REQUEST[geboren] = $aufforderung;
} elseif(!preg_match ("/^\d{4}-\d{1,2}-\d{1,2}$/", $_REQUEST[geboren])) {
	$bad++; 
	$badfields .= "geboren ";
	$_REQUEST[geboren] = "&quot;$_REQUEST[geboren]&quot; ist ein ung&uuml;ltiges Datumsformat";
}
if($_REQUEST[eintritt] != "" && $_REQUEST[eintritt] != 0 && !preg_match('/^\d{4,4}$/', $_REQUEST[eintritt])) {
	$bad++;
	$badfields .= "eintritt";
	$_REQUEST[eintritt] = "&quot;$_REQUEST[eintritt]&quot; ist ein ung&uuml;ltiges Format";
} 
if($_REQUEST[austritt] != "" && $_REQUEST[austritt] != 0 && !preg_match('/^\d{4,4}$/', $_REQUEST[austritt])) {
	$bad++;
	$badfields .= "austritt";
	$_REQUEST[austritt] = "&quot;$_REQUEST[austritt]&quot; ist ein ung&uuml;ltiges Format";
} 
if(!preg_match ("/^\d{4}-\d{1,2}-\d{1,2}$/", $_REQUEST[seit])) {
	$bad++; 
	$badfields .= "seit ";
	$_REQUEST[seit] = "&quot;$_REQUEST[seit]&quot; ist ein ung&uuml;ltiges Datumsformat";
}

if($bad > 0) {
	# redirect to input form
	$url = "insertform.php?bad=".urlencode($badfields) .
		"&name=" .      urlencode($_REQUEST[name]) .
		"&vorname=" .	urlencode($_REQUEST[vorname]) .
		"&seit=" .	urlencode($_REQUEST[seit]) .
		"&adresse=" .	urlencode($_REQUEST[adresse]) .
		"&email=" .	urlencode($_REQUEST[email]) .
		"&geboren=" .	urlencode($_REQUEST[geboren]) .
		"&art=" .	urlencode($_REQUEST[art]) .
		"&eintritt=" .  urlencode($_REQUEST[eintritt]) .
		"&austritt=" .  urlencode($_REQUEST[austritt]) .
		"&beruf=" .     urlencode($_REQUEST[beruf]) .
		"&homepage=" .	urlencode($_REQUEST[homepage]);
	if(isset($_REQUEST[edit]))
		$url .= "&edit=1";
	print "<a href=\"$url\">Seite wird geladen...</a></div></div><div id=\"background_footer\"></div></body></html>";
	exit;
}

//print "
//<head>
//<link type=\"text/css\" rel=stylesheet href=\"../body.css\">
//</head>
//<body>\n";


# check if already known person started new entry
if(empty($_REQUEST[personen_id])) {
    $pid = getval("SELECT id FROM personen WHERE name = '".mysql_escape_string($_REQUEST[name])."' AND vorname = '".mysql_escape_string($_REQUEST[vorname])."' AND geboren = '".mysql_escape_string($_REQUEST[geboren])."'");
    if(!empty($pid)) $_REQUEST[personen_id] = $pid;
}


if(strlen($_REQUEST['personen_id']) > 0) {
	# Person ist bereits bekannt
	$query = "SELECT * FROM personen WHERE id = '".mysql_escape_string($_REQUEST[personen_id])."'";
	$result = mysql_query($query);
	$found = 1;
	$line = mysql_fetch_array($result, MYSQL_ASSOC);
	$person_id = $line[id];
	# ... und die nächste Versionsnummer ihrer Adressen
	$query = "SELECT * FROM adressen WHERE personen_id = '$person_id' ORDER BY version DESC;";
	$result = mysql_query($query);
	$line = mysql_fetch_array($result, MYSQL_ASSOC);
	$adr_version = $line[version] + 1;
	# haben sich Daten geändert?
	if($_REQUEST[vorname] != $line[vorname] || $_REQUEST[name] != $line[name] || $_REQUEST[geboren] != $line[geboren] || $_REQUEST[eintritt] != $line[eintritt] || $_REQUEST[austritt] != $line[austritt] || "$_REQUEST[art]" != "$line[art]" || "$_REQUEST[beruf]" != "$line[beruf]") {
		$q = "UPDATE personen SET vorname='".mysql_escape_string($_REQUEST[vorname])."',name='".mysql_escape_string($_REQUEST[name])."',geboren='".mysql_escape_string($_REQUEST[geboren])."',eintritt='".mysql_escape_string($_REQUEST[eintritt])."',austritt='".mysql_escape_string($_REQUEST[austritt])."',art='".mysql_escape_string($_REQUEST[art])."',beruf='".mysql_escape_string($_REQUEST[beruf])."' WHERE id=$person_id";
		if(!mysql_query($q)) {
			print "<pre>".mysql_error()."</pre>\n";
		}
		print "<!-- person already known. query='$q' -->\n";
	}
} else {
	# nein: neue Person einfügen, dann id grabschen (mysql_insert_id())
	$found = 0;
	$query = "INSERT INTO personen (name, vorname, geboren, art, eintritt, austritt, beruf) VALUES ('".mysql_escape_string($_REQUEST[name])."','".mysql_escape_string($_REQUEST[vorname])."','".mysql_escape_string($_REQUEST[geboren])."','".mysql_escape_string($_REQUEST[art])."','".mysql_escape_string($_REQUEST[eintritt])."','".mysql_escape_string($_REQUEST[austritt])."','".mysql_escape_string($_REQUEST[beruf])."')";
	$result = mysql_query($query);
	$person_id = mysql_insert_id();
	$adr_version = 0;
}
# Adresse einfügen, wenn adr_version == 0 oder sich ein Adressfeld geändert hat
if($adr_version == 0 || $line[adresse] != $_REQUEST[adresse] || 
			$line[email] != $_REQUEST[email] ||
			$line[homepage] != $_REQUEST[homepage] ||
			$line[seit] != $_REQUEST[seit]) {
	$query = "INSERT INTO adressen (personen_id, adresse, email, homepage, seit, version) VALUES ($person_id, '".mysql_escape_string($_REQUEST[adresse])."', '".mysql_escape_string($_REQUEST[email])."', '".mysql_escape_string($_REQUEST[homepage])."', '".mysql_escape_string($_REQUEST[seit])."', $adr_version);";
	$result = mysql_query($query);
}

print "<h3>Daten wurden gespeichert:</h3>

<table border=0>
<tr><td>Vorname</td><td bgcolor=\"#003070\">$_REQUEST[vorname]</td></tr>
<tr><td>Name</td><td bgcolor=\"#003070\">$_REQUEST[name]</td></tr>
<tr><td>Geboren</td><td bgcolor=\"#003070\">$_REQUEST[geboren]</td></tr>
<tr><td>Rolle</td><td bgcolor=\"#003070\">".ucfirst($_REQUEST[art])."</td></tr>
<tr><td>Eintrittsjahr</td><td bgcolor=\"#003070\">$_REQUEST[eintritt]</td></tr>
<tr><td>Austrittsjahr</td><td bgcolor=\"#003070\">$_REQUEST[austritt]</td></tr>
<tr><td>Ausbildung/Beruf</td><td bgcolor=\"#003070\">$_REQUEST[beruf]</td></tr>
<tr><td valign=top>Adresse</td><td bgcolor=\"#003070\"><pre>$_REQUEST[adresse]</pre></td></tr>
<tr><td>G&uuml;ltig seit</td><td bgcolor=\"#003070\">$_REQUEST[seit]</td></tr>
<tr><td>Email</td><td bgcolor=\"#003070\">$_REQUEST[email]</td></tr>
<tr><td>Homepage</td><td bgcolor=\"#003070\">$_REQUEST[homepage]</td></tr>
</table> 

<p><font size=-2>(Person " . ($found ? "bekannt":"bisher unbekannt") . ".)</font><br>";

print "<p>&nbsp;<p>\n";

if($_REQUEST[art] == 'lehrer' || $_REQUEST[art] == 'schüler') {
	$classlistURL="classlist_edit.php?personen_id=$person_id&name=".urlencode($_REQUEST[name])."&vorname=".urlencode($_REQUEST[vorname]);
	if(isset($_REQUEST[edit])) {
		print "<font size=+1>Zum Editieren der <a href=\"$classlistURL\">Klassenzuordnungen bitte hier entlang</a></font>";
	} else {
		print "<h3>Wichtig:</h3><font size=+1>Hier kannst Du Dich <a href=\"$classlistURL\">in Deine Klasse eintragen.</a></font>\n";
	}
}
?>
<p>&nbsp;<p>&nbsp;<p>
<a href="insertform.php">Zur&uuml;ck zur Eingabe</a>&nbsp;<a href="search.php">Suchen</a>

</div>
</div>
<div id="background_footer"></div>
</body>
</html>
