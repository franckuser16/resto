<h1> Effectuer une sauvegarde </h1>
<hr>
<br/><br/><br/><br/><br/><br/><br/><br/>
<br/><br/><br/><br/><br/><br/><br/><br/>
<br/><br/><br/><br/><br/><br/><br/><br/>
<br/><br/><br/><br/><br/><br/><br/><br/>
<br/><br/><br/><br/><br/><br/><br/><br/>
<p> Grâce à ce menu, vous allez pouvoir sauvegarder un ordinateur particulié.<br>
L'ordinateur sera réveillé via Wake On Lan, pour pouvoir démarrer directement par le réseau. <br>
L'ordinateur doit être <a href="<?php echo "".$_SERVER['PHP_SELF']."?menu=actions_distance"; ?>">éteint</a> correctement. <br>
Vérifiez que l'option Wake On Lan est activée dans le BIOS et que votre OS est configuré pour accepter les <a href="http://lmgtfy.com/?q=activer+wol+magic+packet">Magic Packets</a>.<br>

Vous pouvez créer une nouvelle salle via  le menu de gestion des <a href="<?php echo "".$_SERVER['PHP_SELF']."?menu=salles"; ?>">salles</a>.<br>

Attention, vous devez détecter <a href="<?php echo "".$_SERVER['PHP_SELF']."?menu=images&action=detection"; ?>">les images</a> une fois la sauvegarde finie.
</p>
<hr><br>

<div id="style">
<?php

$succes=false;
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

Sélectionnez la salle où se trouve l'ordinateur à sauvegarder : <br>
<form id="formulaire" action="<?php echo "".$_SERVER['PHP_SELF']."?menu=".$menu."#style"; ?>" method="post" >
<select name="room" onchange="document.forms['formulaire'].submit();">
<option value="Sectionnez la salle">Sectionnez la salle</option>
<?php // On soumet le formulaire à la sélection

if ( $connect == true )
{
	$room = $_POST['room']; 
	$sql = "SELECT distinct C_room FROM computers";

	$result = mysql_query($sql);

	while (($row = mysql_fetch_array($result, MYSQL_ASSOC) )) 
	{
			if ($row['C_room'] == $room) // Pour mettre le champ selected
			{
				echo "<option value=\"".$row['C_room']."\" selected=\"selected\">".$row['C_room']."</option>";
			}
			else
			{
				echo "<option value=\"".$row['C_room']."\">".$row['C_room']."</option>";
			}
	}

}
?>
</select><br>
<?php

if (( $connect == true ) && (isset($_POST['room'])) )
{

	$room = $_POST['room']; 
	
	if (isset($_POST['computer_name']))
	{
		$computer_name = $_POST['computer_name'];
	}
	
	$sql = "SELECT C_name FROM computers where C_room='$room'";
	$result = mysql_query($sql);

	?>
	Selectionnez l'ordinateur à sauvegarder :<br>
	<select name="computer_name" onchange="document.forms['formulaire'].submit();">
	<option value="Sectionnez l'ordinateur">Sectionnez l'ordinateur</option>
	<?php

	while (($row = mysql_fetch_array($result, MYSQL_ASSOC) )) 
	{
		if ($row['C_name'] == $computer_name) // Pour mettre le champ selected
		{
			echo "<option value=\"".$row['C_name']."\" selected=\"selected\">".$row['C_name']."</option>";
		}
		else
		{
			echo "<option value=\"".$row['C_name']."\">".$row['C_name']."</option>";
		}
	}

}
?>
</select><br>

<?php

if (isset($_POST['computer_name']))
{

	$room = $_POST['room']; 
	$computer_name = $_POST['computer_name'];
	
	if (isset($_POST['disk_or_parts']))
	{
		$disk_or_parts = $_POST['disk_or_parts'];
	}

// On met les valeurs par défaut ici :
if  (isset($_POST['disk_name']))
{
	$disk_name= $_POST['disk_name'];
}
else
{
	$disk_name= "sda";
}

if  (isset($_POST['parts_name']))
{
	$parts_name = $_POST['parts_name'];
}
else
{
	$parts_name = "sda1 sda2";
}

?>
Que voulez vous sauvegarder ? <br>
<?php

	if (isset($_POST['disk_or_parts']))
	{
		if ($disk_or_parts == "disk" )
		{
			?>
			<input type="radio" name="disk_or_parts" value="disk" checked="checked" /> Tout le disque <br>
			<input type="radio" name="disk_or_parts" value="parts" onchange="document.forms['formulaire'].submit();"/> Une ou des partitions <br>
			Indiquez le nom du disque à sauvegarder : <br>
			<input type="text" title="nom des parts" name="disk_name" value="<?php echo $disk_name; ?>" > <br>
			<?php

			
		}
		else
		{
			?>
			<input type="radio" name="disk_or_parts" value="disk" onchange="document.forms['formulaire'].submit();" /> Tout le disque <br>
			<input type="radio" name="disk_or_parts" value="parts" checked="checked"/> Une ou des partitions <br>
			Indiquez le nom des partitions à sauvegarder : <br>
			<input type="text" title="nom des parts" name="parts_name" value="<?php echo $parts_name; ?>" ><br>
			<?php	
		}


	}
	else
	{
		?>
		<input type="radio" name="disk_or_parts" value="disk" onchange="document.forms['formulaire'].submit();" /> Tout le disque <br>
		<input type="radio" name="disk_or_parts" value="parts" onchange="document.forms['formulaire'].submit();"/> Une ou des partitions <br>
		<?php
	}

}

if (isset($_POST['disk_or_parts']))
{

	$room = $_POST['room']; 
	$computer_name = $_POST['computer_name'];
	$disk_or_parts = $_POST['disk_or_parts'];
	
	if (isset($_POST['image_name']))
	{
		$image_name = $_POST['image_name'];
	}
	else
	{
		$image_name="";
	}

	?>
	Veuillez indiquer le nom de l'image qui sera créé : <br>
	<input type="text" title="nom de l'image" name="image_name" value="<?php echo $image_name; ?>"><br>
	<input type="submit" name="action" value="lancer">
	</form>

	<?php
}


// Traitement du formulaire 

if ( (isset($_POST['action'])) && (isset($_POST['image_name'])) && ($connect == true) )
{

	if ($_POST['image_name'] != "")
	{
		$computer_name = $_POST['computer_name'];
		$image_name = $_POST['image_name'];
		$disk_or_parts = $_POST['disk_or_parts'];
		
		if (isset($_POST['parts_name']))
		{
			$parts_name = $_POST['parts_name'];
		}
		
		if (isset($_POST['disk_name']))
		{
			$disk_name = $_POST['disk_name'];
		}
		
		$sql = "SELECT C_mac_addr FROM computers where C_name='$computer_name'"; // On chope l'adresse Mac de l'ordi à partir de son nom
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$mac_computer= $row['C_mac_addr'];

		if ($disk_or_parts == "disk")
		{
			shell_exec("sudo /opt/drbl/sbin/drbl-ocs -b -q2 -j2 -rm-win-swap-hib -p poweroff -z1p -i 1000000 -l fr_FR.UTF-8 startdisk save ".$image_name." ".$disk_name." > /dev/null 2>&1 &") ;
			//echo "sudo /opt/drbl/sbin/drbl-ocs -b -q2 -j2 -rm-win-swap-hib -p poweroff -z1p -i 1000000 -l fr_FR.UTF-8 startdisk save ".$image_name."";
		}
		else
		{
			shell_exec("sudo /opt/drbl/sbin/drbl-ocs -b -q2 -j2 -rm-win-swap-hib -p poweroff -z1p -i 1000000 -l fr_FR.UTF-8 startparts save ".$image_name." ".$parts_name."  > /dev/null 2>&1 &");
			//echo "sudo /opt/drbl/sbin/drbl-ocs -b -q2 -j2 -rm-win-swap-hib -p poweroff -z1p -i 1000000 -l fr_FR.UTF-8 startparts save ".$image_name." ".$parts_name."";
		}
			shell_exec("sleep 10 && wakeonlan -i ".$addr_broadcast." ".$mac_computer."  > /dev/null 2>&1 &");
			//echo "sleep 10 && wakeonlan -i ".$addr_broadcast." ".$mac_computer."";
			$succes=true;

	}
	else
	{
		echo "<br>Veuillez indiquer un nom d'image";
	}

}



if ($succes==true)
{
	include_once("lightbox.php"); // Contient les cadres qui sont affichés

	start_lightbox("save"); // On démarre la boite de dialogue de succes
}



?>
</div>

