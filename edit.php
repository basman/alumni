<?php
require('top_file.php');
?>
<?
require "db.php";

if(checkInjection($_REQUEST[vorname]) || 
   checkInjection($_REQUEST[name]) || 
   checkInjection($_REQUEST[geboren])) {
	print "Error: invalid input\n";
	exit;
}

if($_REQUEST[person] != "") {
	$query = "SELECT adressen.id, personen_id, name, vorname, geboren, art, eintritt, austritt, beruf, adresse, email, homepage, seit FROM personen, adressen WHERE personen.id = personen_id AND personen.id = '".mysql_escape_string($_REQUEST[person])."' ORDER BY seit DESC LIMIT 1";
} else {
	$query = "SELECT adressen.id, personen_id, name, vorname, geboren, art, eintritt, austritt, beruf, adresse, email, homepage, seit FROM personen, adressen WHERE personen.id = personen_id AND vorname = '".mysql_escape_string($_REQUEST[vorname])."' AND name = '".mysql_escape_string($_REQUEST[name])."' AND geboren = '".mysql_escape_string($_REQUEST[geboren])."' ORDER BY seit DESC LIMIT 1";
}
$result = mysql_query($query);
if(mysql_num_rows($result) == 0) {
	print "<h3>Anfrage verweigert:</h3> Person unbekannt.<p><b>Bemerkung:</b><br>Eintr&auml;ge k&ouml;nnen nur ge&auml;ndert werden, wenn die geforderten Angaben korrekt sind.
<p>
<!-- Button für neuen Versuch -->
<form action=\"editform.php\" method=\"post\">
<input type=submit value=\"Erneut versuchen\">
<input type=hidden name=\"name\" value=\"$_REQUEST[name]\">
<input type=hidden name=\"vorname\" value=\"$_REQUEST[vorname]\">
<input type=hidden name=\"geboren\" value=\"$_REQUEST[geboren]\">
</form>
</div>
</div>
<div id=\"background_footer\"></div>
</body>
</html>\n";
	exit;
}

$line = mysql_fetch_assoc($result);
if(preg_match('/klassen/i', $_REQUEST[action])) {
	if($line[art] == 'lehrer' || $line[art] == 'schüler') {
		$url = "classlist_edit.php?";
	} else {
		print "Nur Sch&uuml;ler und Lehrer k&ouml;nnen Klassenzuordnungen vornehmen.</div></div><div id=\"background_footer\"></div></body></html>\n";
		exit;
	}
} else {
	$url = "insertform.php?edit=1&";
}

foreach($line as $field => $val) {
	$url .= "$field=" . urlencode($val) . "&";
}
//print "
//<meta HTTP-EQUIV=\"refresh\" CONTENT=\"0;URL=$url\">
//</head>
//";
print "<a href=\"$url\">Seite wurde geladen. <u>Bitte hier klicken.</u></a>";
?>
</div>
</div>
<div id="background_footer"></div>
</body>
</html>
