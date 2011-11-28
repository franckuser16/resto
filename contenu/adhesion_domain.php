<h1> Adhésion au domaine </h1>
<hr>
<p> Grâce à ce menu, vous allez pouvoir faire adhérer plusieurs ordinateurs au domaine.<br>
L'ordinateur doit être démarré sur windows. <br>
Vous pouvez par exemple après une <a href="<?php echo "".$_SERVER['PHP_SELF']."?menu=restore"; ?>">restauration</a>, sélectionner un redémarrage des ordinateurs. <br>

Les ordinateurs vont accepter la commande grâce à un serveur ssh. Référez vous à la documentation pour préparer une image qui accepte les commandes du serveur DRBL<br>
Vous pouvez ajouter un nouveau domaine grâce au menu <a href="<?php echo "".$_SERVER['PHP_SELF']."?menu=domaines"; ?>">gestion des domaines</a>.
</p>
<hr><br>

<div id="style">

<?php

$connect=false;

$resultat="<h1 class=\"erreur\">Echec de connexion à la base !</h1>";

if($db = mysql_connect($mysql_hote, $mysql_login, $mysql_password))
{
	if ($id_db = mysql_select_db($mysql_database))
	{
		$connect=true;
	}
	else
	{
		mysql_close($db);
	}
}

?>
Sélectionnez le domaine : <br>
<form id="formulaire" action="<?php echo "".$_SERVER['PHP_SELF']."?menu=".$menu.""; ?>" method="post" >
<select name="domain" onchange="document.forms['formulaire'].submit();">
<option value="Sectionnez le domaine">Sectionnez le domaine</option>
<?php // On soumet le formulaire à la sélection

if ( $connect == true )
{
	$domain = $_POST['domain']; 
	$sql = "SELECT D_name FROM domain";

	$result = mysql_query($sql);

	while (($row = mysql_fetch_array($result, MYSQL_ASSOC) )) 
	{
			if ($row['D_name'] == $domain) // Pour mettre le champ selected
			{
				echo "<option value=\"".$row['D_name']."\" selected=\"selected\">".$row['D_name']."</option>";
			}
			else
			{
				echo "<option value=\"".$row['D_name']."\">".$row['D_name']."</option>";
			}
	}

}
?>
</select><br>
<?php

if (isset($_POST["domain"]))
{
	?>
	Quelle est la première ip de la plage ? <br>
	<input type="text" title="première ip" name="first_ip_a" size="3" maxlength="3">.<input type="text" title="première ip" name="first_ip_b" size="3" maxlength="3">.<input type="text" title="première ip" name="first_ip_c" size="3" maxlength="3">.<input type="text" title="première ip" name="first_ip_d" size="3" maxlength="3"><br>

	Quelle est la dernière ip de la plage ? <br>
	<input type="text" title="dernière ip" name="last_ip_a" size="3" maxlength="3">.<input type="text" title="dernière ip" name="last_ip_b" size="3" maxlength="3">.<input type="text" title="dernière ip" name="last_ip_c" size="3" maxlength="3">.<input type="text" title="dernière ip" name="last_ip_d" size="3" maxlength="3"><br>

	<input type="submit" name="go" value="lancer">
	<?php

}

if (isset($_POST["go"]))
{
	$domain=$_POST["domain"];
	$first_ip_a=$_POST["first_ip_a"];
	$first_ip_b=$_POST["first_ip_b"];
	$first_ip_c=$_POST["first_ip_c"];
	$first_ip_d=$_POST["first_ip_d"];

	$last_ip_d=$_POST["last_ip_d"];
	
	$sql = "SELECT * FROM domain";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result, MYSQL_ASSOC); 
	$admin_account=$row['D_host_admin'];
	$domain_admin=$row['D_domain_admin'];
	$domain_password=$row['D_domain_password'];

	
	// On génère la liste d'ip
	for ($i=$first_ip_d;$i<=$last_ip_d;$i++)
	{
		$host_list="".$host_list." ".$first_ip_a.".".$first_ip_b.".".$first_ip_c.".".$i."";
	}
	

	if (($first_ip_d != "") || ($last_ip_d != ""))
	{

	//echo "<br>sudo ".$var_www."/scripts/ssh_action.sh -h \"$host_list\" -u $admin_account  /bin/autologin.sh $domain $domain_admin $domain_password > /dev/null 2>&1 & <br>";
	
	shell_exec("sudo ".$var_www."/scripts/ssh_action.sh -h \"$host_list\" -u $admin_account  /bin/autologin.sh $domain $domain_admin $domain_password > /dev/null 2>&1 &");
	}

}
?>
</div>
