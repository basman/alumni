<?php
require('top_file.php');
?>
<h3>Menschen suchen</h3>

<form action="searchresult.php" method="post">
<table border=0>
<tr><td>Vorname</td><td><input name="vorname" size=30 maxlen=120></td></tr>
<tr><td>Name</td><td><input name="name" size=30 maxlen=120></td></tr>
<tr><td>Geboren</td><td><input name="geboren" size=30 maxlen=30></td></tr>
<tr><td>Rolle</td><td><select name="art">
				<option value="">Alle</option>
				<option value="schüler">Sch&uuml;ler</option>
				<option value="lehrer">Lehrer</option>
				<option value="eltern">Eltern</option>
				<option value="sonstige">Sonstige</option>
                        </select></td></tr>
<tr><td>Eintrittsjahr</td><td><input name="eintritt" size=30 maxlen=30></td></tr>
<tr><td>Austrittsjahr</td><td><input name="austritt" size=30 maxlen=30></td></tr>
<tr><td>Ausbildung/Beruf</td><td><input name="beruf" size=30 maxlen=30></td></tr>
<tr><td>Adresse</td><td><input name="adresse" size=30 maxlen=120></td></tr>
<tr><td>Email</td><td><input name="email" size=30 maxlen=120></td></tr>
<tr><td>Homepage</td><td><input name="homepage" size=30 maxlen=120></td></tr>
<tr><td colspan=2>&nbsp;</td></tr>
<tr><td><input type="submit" value="Suche starten"></td><td><input type="reset" value="Abbrechen"></td></tr>
</table>
</form>
</div>
</div>
<div id="background_footer"></div>
</body>
</html>