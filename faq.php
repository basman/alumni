<?php
require('top_file.php');
?>
<?
require "db.php";

print "
<h3>H&auml;ufig gestellte Fragen</h3>\n";
$faq = mysql_query("SELECT * FROM faq");
if(mysql_num_rows($faq) < 1) {
	print "(Keine Eintr&auml;ge vorhanden)\n";
}
while($entry = mysql_fetch_assoc($faq)) {
	$datum_formatiert = substr($entry['datum'], 0, 4) . '-' .
				substr($entry['datum'], 4, 2) . '-' .
				substr($entry['datum'], 4, 2);
	print "<table border=0><tr><td><h4>Frage</h4></td><td align=\"right\">$datum_formatiert</td></tr>
	<tr><td colspan=2><!-- $entry[datum] -->\n$entry[frage]</td></tr>
	<tr><td><h4>Antwort</h4></td><td>&nbsp</td></tr>
	<tr><td colspan=2>$entry[antwort]</td></tr>
	</table>
	<hr>\n";
}
?>
</div>
</div>
<div id="background_footer"></div>
</body>
</html>
