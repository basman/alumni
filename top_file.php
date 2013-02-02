<?
header('Content-Type: text/html;charset=ISO-8859-1');
?>
<html>
<head>
<meta HTTP-EQUIV=\"refresh\" CONTENT=\"0;URL=$url\">
<link type="text/css" rel=stylesheet href="body.css">
<title>Ehemaligen Datenbank der Rudolf Steiner Schule Kreuzlingen</title>

<!--Mail-FUNKTION-->
<script language="JavaScript" type="text/JavaScript">
<!--
function mask(end,middle,start,subject){
var one ='mai';
var two='lto:';
var three='?Subject=';
//start,middle,end,subject;
var putogether= one+two+start+middle+end+three+subject;
document.location.href=eval('"'+putogether+'"');
window.status=putogether;
}
//-->
</script>
<!--MENU-FUNKTION-->
<script type="text/javascript">
<!--
window.onload=montre;
function montre(id) {
var d = document.getElementById(id);
	for (var i = 1; i<=10; i++) {
		if (document.getElementById('smenu'+i)) {document.getElementById('smenu'+i).style.display='none';}
	}
if (d) {d.style.display='block';}
}
//-->
</script>
</head>
<body>
<div id="background">
<div class="r">
<div class="l">
<div class="ru">
<div class="lu">
<div id="logo"><div align="center"><img src="pics/bg/133.jpg"></div></div>
<div id="topbalken"><table width="100%"><tr><td bgcolor="#008195" width="100%">&nbsp;</td></tr></table></div>
<div id="menu">
		<dl>
			<dt onmouseover="javascript:montre();"><a href="index.php" title="Retour &agrave; l'accueil">Home</a></dt>
		</dl>
		<dl>
	    	<dt onmouseover="javascript:montre();"><a href="insertform.php">Neuer Eintrag</a></dt>
		</dl>
		<dl>
	    	<dt onmouseover="javascript:montre();"><a href="editform.php">Eintrag &auml;ndern</a></dt>
		</dl>
      <dl>
	    	<dt onmouseover="javascript:montre();"><a href="search.php">Suchen</a></dt>
		</dl>
		<dl>
	    	<dt onmouseover="javascript:montre();"><a href="classlist_show.php">Klassenlisten</a></dt>
		</dl>
		<dl>
	    	<dt onmouseover="javascript:montre();"><a href="faq.php">FAQ</a></dt>
		</dl>
		<dl>
	    	<dt onmouseover="javascript:montre();"><a href="db_statistic.php">Statistik</a></dt>
		</dl>
		<dl>
	    	<dt onmouseover="javascript:montre();"><a href="http://www.steinerschulekreuzlingen.ch" target="_blank">Schule</a></dt>
		</dl>	
		
</div>
<div class="inhalt" onmouseover="javascript:montre();">
