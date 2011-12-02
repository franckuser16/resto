<h1> Actions à distance </h1>
<hr>
<p> Grâce à ce menu, vous allez pouvoir éteindre ou redémarrer des ordinateurs.<br>

Les ordinateurs vont accepter la commande grâce à un serveur ssh. Référez vous à la documentation pour préparer une image qui accepte les commandes du serveur DRBL.<br>
</p>
<hr><br>

<div id="style">
<form id="formulaire" action="<?php echo "".$_SERVER['PHP_SELF']."?menu=".$menu.""; ?>" method="post" >
Que voulez vous faire ? <br>
<input type="radio" name="action" value="shutdown" checked="checked" "/> Eteindre les ordinateurs
<input type="radio" name="action" value="reboot" "/> Redémarrer les ordinateurs <br>

Quel est le nom du compte administrateur sur les clients windows ? <br>
<input type="text" title="nom du compte admin" name="admin_account"><br>

Quelle est la première ip de la plage ? <br>
<input type="text" title="première ip" name="first_ip_a" size="3" maxlength="3">.<input type="text" title="première ip" name="first_ip_b" size="3" maxlength="3">.<input type="text" title="première ip" name="first_ip_c" size="3" maxlength="3">.<input type="text" title="première ip" name="first_ip_d" size="3" maxlength="3"><br>

Quelle est la dernière ip de la plage ? <br>
<input type="text" title="dernière ip" name="last_ip_a" size="3" maxlength="3">.<input type="text" title="dernière ip" name="last_ip_b" size="3" maxlength="3">.<input type="text" title="dernière ip" name="last_ip_c" size="3" maxlength="3">.<input type="text" title="dernière ip" name="last_ip_d" size="3" maxlength="3"><br>

<input type="submit" name="go" value="lancer">

<?php


if (isset($_POST["go"]))
{
	$admin_account=$_POST["admin_account"];
	$first_ip_a=$_POST["first_ip_a"];
	$first_ip_b=$_POST["first_ip_b"];
	$first_ip_c=$_POST["first_ip_c"];
	$first_ip_d=$_POST["first_ip_d"];

	$last_ip_d=$_POST["last_ip_d"];
	
	if ($_POST["action"] == "reboot")
	{
		$action_windows = "/usr/bin/shutdown -f -r now";
		$action_linux = "/sbin/reboot";
	}
	else
	{
		$action_windows = "/usr/bin/shutdown -f -s -x now";
		$action_linux = "/sbin/halt";
	}

	
	// On génère la liste d'ip
	for ($i=$first_ip_d;$i<=$last_ip_d;$i++)
	{
		$host_list="".$host_list." ".$first_ip_a.".".$first_ip_b.".".$first_ip_c.".".$i."";
	}
	
	//echo "<br>sudo ".$var_www."/scripts/ssh_action.sh -h \"$host_list\" -u $admin_account $action_windows > /dev/null 2>&1 & <br>";
	//echo "sudo ".$var_www."/scripts/ssh_action.sh -h \"$host_list\" -u root $action_linux > /dev/null 2>&1 &<br>";

	shell_exec("sudo ".$var_www."/scripts/ssh_action.sh -h \"$host_list\" -u $admin_account $action_windows > /dev/null 2>&1 &");
	shell_exec("sudo ".$var_www."/scripts/ssh_action.sh -h \"$host_list\" -u $admin_account $action_linux > /dev/null 2>&1 &");

}
?>
</div>
