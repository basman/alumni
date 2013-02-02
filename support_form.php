<?php
require('top_file.php');
?>
<?

print "support form has been disabled.\n";
exit;

include "db.php";
if($_REQUEST[action] == 'submit') {

	if(strlen($_REQUEST[message]) < 5 || strlen($_REQUEST[email]) < 5) {
		print "Fehler: Keine Nachricht eingegeben.\n";
	} else if(checkInjection($_REQUEST[email])) {
		print "Error: invalid input\n";
		exit; // asure conformity to other forms with same error message (no closing body and html tag).
	} else {

		if(mail("rha_alumniSupport@disconnect.ch", "Alumni DB support mail", $_REQUEST[message], "From: $_REQUEST[email]\nCc: david.b_alumniSupport@disconnect.ch")) {
			print "Deine Anfrage wurde abgeschickt.";
		} else {
			print "Interner Systemfehler. Anfrage konnte nicht &uuml;bermittelt werden.";
		}
	}
	print "</div></div><div id=\"background_footer\"></div></body></html>\n";
	exit;
}

print"
<h3>Support-Anfrage erfassen</h3>

Hier kannst Du den Entwickler dieser Datenbank um Hilfe bitten, 
wenn Du feststeckst, oder etwas nicht geht.

<p>
<form action=\"$PHP_SELF\" method=\"post\">"
?>
<table border=0>
<tr><td>Deine Emailadresse:</td><td><input name="email" size=50></td></tr>
<tr valign="top"><td>Anfrage:</td><td><textarea name="message" rows=15 cols=50></textarea></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" value="Abschicken"></td></tr>
</table>
<input type="hidden" name="action" value="submit">
</form>

</body>
</html>
