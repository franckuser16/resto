<h1> Effectuer une restauration </h1>

<hr>
<p> Grâce à ce menu, vous allez pouvoir restaurer une salle entière via multicast ou uniquement certains ordinateurs de la salle.<br>
Les ordinateurs seront réveillés via Wake On Lan, pour pouvoir démarrer directement par le réseau. <br>
Les ordinateurs doivent être <a href="<?php echo "".$_SERVER['PHP_SELF']."?menu=actions_distance"; ?>">éteint</a> correctement. <br>
Vérifiez que l'option Wake On Lan est activée dans le BIOS et que votre OS est configuré pour accepter les <a href="http://lmgtfy.com/?q=activer+wol+magic+packet">Magic Packets</a>.<br>

Vous pouvez ajouter une image via le menu de <a href="<?php echo "".$_SERVER['PHP_SELF']."?menu=save"; ?>">sauvegarde</a> ou créer une nouvelle salle via  le menu de gestion des <a href="<?php echo "".$_SERVER['PHP_SELF']."?menu=salles"; ?>">salles</a>.<br>


Attention, pour que les noms des clients windows soient changé, il faut nommer les <a href="<?php echo "".$_SERVER['PHP_SELF']."?menu=salles&action=visualisation"; ?>">ordinateurs</a> et générer le fichier <a href="<?php echo "".$_SERVER['PHP_SELF']."?menu=salles&action=generate"; ?>">mac-hostname</a>.
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
<form id="formulaire" action="<?php echo "".$_SERVER['PHP_SELF']."?menu=".$menu."#style"; ?>" method="post" >
<?php

if ( $connect == true )
{
	?>
	Que voulez vous restaurer ? <br>
	<?php

	if (isset($_POST['room_or_computer']))
	{
		if ($_POST['room_or_computer'] == "room")
		{
			?>
			<input type="radio" name="room_or_computer" value="room" checked="checked" onchange="document.forms['formulaire'].submit();"/> Une salle entière
			<input type="radio" name="room_or_computer" value="computer" onchange="document.forms['formulaire'].submit();"/> Un ou des ordinateurs d'une salle <br>
			<?php
		}
		else
		{
			?>
			<input type="radio" name="room_or_computer" value="room" onchange="document.forms['formulaire'].submit();"/> Une salle entière
			<input type="radio" name="room_or_computer" value="computer" checked="checked" onchange="document.forms['formulaire'].submit();"/> Un ou des ordinateurs d'une salle <br>
			<?php
	
		}
	}
	else
	{
		?>
		<input type="radio" name="room_or_computer" value="room" onchange="document.forms['formulaire'].submit();"/> Une salle entière
		<input type="radio" name="room_or_computer" value="computer" onchange="document.forms['formulaire'].submit();"/> Un ou des ordinateurs d'une salle <br>
		<?php
	}

}


if (( $connect == true ) && (isset($_POST['room_or_computer'])))
{
?>
Sélectionnez la salle à restaurer : <br>
<select name="room" onchange="document.forms['formulaire'].submit();">
<option value="Sectionnez la salle">Sectionnez la salle</option>
<?php
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

	if ( $_POST['room_or_computer'] == "computer" )
	{

		$sql = "SELECT C_name FROM computers where C_room='$room' order by C_name";
		$result = mysql_query($sql);
		$i=1;

		echo "Choisissez les ordinateurs à restaurer : <br>";
		while (($row = mysql_fetch_array($result, MYSQL_ASSOC) )) 
		{
	
			$computer_name=$row['C_name'];
			if (isset($_POST["$computer_name"])) // Si computer_name existe, il faut le cocher
			{
				echo "<input type=\"checkbox\" name=\"".$row['C_name']."\" checked=\"checked\" /> ".$row['C_name']."";
			}
			else
			{
				echo "<input type=\"checkbox\" name=\"".$row['C_name']."\" /> ".$row['C_name']."";
			}

			if ( $i%3 == 0 ) // On saute une ligne une fois sur 3
			{	
				echo "<br>";
			}

			$i=$i+1;
		}	

		if ( $i%3 == 0 ) 
		{	
			echo "<br>";
		}

	}
	else
	{
		$computer_name="none";
	}





}


if (( $connect == true ) && (isset($computer_name)) )
{

	 if (isset($_POST['image_name']))
	{
		$image_name = $_POST['image_name'];
	}
	
	$sql = "SELECT I_name FROM images";
	$result = mysql_query($sql);

	?>
	Sélectionnez l'image à restaurer :<br>
	<select name="image_name" onchange="document.forms['formulaire'].submit();">
	<option value="Sectionnez l'image">Sectionnez l'image</option>
	<?php

	while (($row = mysql_fetch_array($result, MYSQL_ASSOC) )) 
	{
		if ($row['I_name'] == $image_name ) // Pour mettre le champ selected
		{
			echo "<option value=\"".$row['I_name']."\" selected=\"selected\">".$row['I_name']."</option>";
		}
		else
		{
			echo "<option value=\"".$row['I_name']."\">".$row['I_name']."</option>";
		}
	}

}
?>
</select><br>

<?php

if (isset($_POST['image_name']))
{

	$room = $_POST['room']; 
	$image_name = $_POST['image_name'];
	
	if (isset($_POST['disk_or_parts']))
	{
		$disk_or_parts = $_POST['disk_or_parts'];
	}
	
	$sql = "SELECT I_disk_name FROM images where I_name='$image_name'";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$disk_name= $row['I_disk_name'];


	if (($disk_name=="sda") || ($disk_name=="hda") )
	{
		
		echo "Que voulez vous restaurer ? <br>";
	
		if (isset($_POST['disk_or_parts']))
		{
			if ($disk_or_parts == "disk" )
			{
				?>
				<input type="radio" name="disk_or_parts" value="disk" checked="checked" /> Tout le disque <br>
				<input type="radio" name="disk_or_parts" value="parts" onchange="document.forms['formulaire'].submit();"/> Une ou des partitions <br>
				<?php

			
			}
			else
			{
				?>
				<input type="radio" name="disk_or_parts" value="disk" onchange="document.forms['formulaire'].submit();" /> Tout le disque <br>
				<input type="radio" name="disk_or_parts" value="parts" checked="checked"/> Une ou des partitions <br>
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
	else
	{
		$disk_or_parts = "parts";
	}

	// Pour afficher les partitions
	$sql = "SELECT I_id FROM images where I_name='$image_name'";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$image_id= $row['I_id'];


	if ((isset($disk_or_parts)) && ($disk_or_parts == "parts" ))
	{
		$sql = "SELECT P_name,P_description FROM partitions where P_id_image='$image_id' order by P_name";
		$result = mysql_query($sql);

		echo "Choisissez la ou les partitions à restaurer : <br>";
		while (($row = mysql_fetch_array($result, MYSQL_ASSOC) )) 
		{
			$parts_name=$row['P_name'];
			if (isset($_POST["$parts_name"])) // Si parts_name existe, il faut le cocher
			{
				echo "<input type=\"checkbox\" name=\"".$row['P_name']."\" checked=\"checked\" /> ".$row['P_name']."  ".$row['P_description']."<br>";
			}
			else
			{
				echo "<input type=\"checkbox\" name=\"".$row['P_name']."\" /> ".$row['P_name']."  ".$row['P_description']."<br>";
			}
		}

	}
}

if ((isset($disk_or_parts)) && ($disk_or_parts != "" ))
{

$room = $_POST['room']; 
$image_name = $_POST['image_name'];

?>
Réveiller la salle ?<br>
<?php
if (isset($_POST['wakeup']))
{
	if ($_POST['wakeup'] == "yes")
	{
		?>
		<input type="radio" name="wakeup" value="yes" checked="checked" onchange="document.forms['formulaire'].submit();"/> Oui
		<input type="radio" name="wakeup" value="no" onchange="document.forms['formulaire'].submit();"/> Non <br>
		<?php
	}
	else
	{
		?>
		<input type="radio" name="wakeup" value="yes" onchange="document.forms['formulaire'].submit();"/> Oui
		<input type="radio" name="wakeup" value="no" checked="checked" onchange="document.forms['formulaire'].submit();"/> Non <br>
		<?php
	
	}
}
else
{
	?>
	<input type="radio" name="wakeup" value="yes" checked="checked" onchange="document.forms['formulaire'].submit();"/> Oui
	<input type="radio" name="wakeup" value="no" onchange="document.forms['formulaire'].submit();"/> Non <br>
	<?php
}

?>
Que faire après la restauration ?<br>
<?php
if (isset($_POST['postaction']))
{
	if ($_POST['postaction'] == "poweroff")
	{
		?>
		<input type="radio" name="postaction" value="poweroff" checked="checked" onchange="document.forms['formulaire'].submit();"/> Eteindre la salle
		<input type="radio" name="postaction" value="reboot" onchange="document.forms['formulaire'].submit();"/> Redémarrer la salle <br>
		<?php
	}
	else
	{
		?>
		<input type="radio" name="postaction" value="poweroff" onchange="document.forms['formulaire'].submit();"/> Eteindre la salle
		<input type="radio" name="postaction" value="reboot" checked="checked" onchange="document.forms['formulaire'].submit();"/> Redémarrer la salle <br>
		<?php
	
	}
}
else
{
		?>
		<input type="radio" name="postaction" value="poweroff" checked="checked" onchange="document.forms['formulaire'].submit();"/> Eteindre la salle
		<input type="radio" name="postaction" value="reboot" onchange="document.forms['formulaire'].submit();"/> Redémarrer la salle <br>
		<?php
}


?>
Limiter le débit multicast ?<br>
<?php
// On met les valeurs par défaut ici :
if  (isset($_POST['max_bitrate']))
{
	$max_bitrate= $_POST['max_bitrate'];
}
else
{
	$max_bitrate= "100";
}

if (isset($_POST['qos']))
{
	if ($_POST['qos'] == "yes")
	{
		?>
		<input type="radio" name="qos" value="yes" checked="checked" onchange="document.forms['formulaire'].submit();"/> Oui
		<input type="radio" name="qos" value="no" onchange="document.forms['formulaire'].submit();"/> Non <br>

		Quel est le débit max (megabits/sec)? <br>
		<input type="text" title="débit max" name="max_bitrate" value="<?php echo $max_bitrate; ?>"><br>

		<?php
	}
	else
	{
		?>
		<input type="radio" name="qos" value="yes" onchange="document.forms['formulaire'].submit();"/> Oui
		<input type="radio" name="qos" value="no" checked="checked" onchange="document.forms['formulaire'].submit();"/> Non <br>
		<?php
	
	}


}
else
{
	?>
	<input type="radio" name="qos" value="yes" onchange="document.forms['formulaire'].submit();"/> Oui
	<input type="radio" name="qos" value="no" checked="checked" onchange="document.forms['formulaire'].submit();"/> Non <br>
	<?php
}

///// Timeout //////
?>
Voulez vous démarrer la restauration même si tous les clients ne sont pas prêt ?<br>
<?php
// On met les valeurs par défaut ici :
if  (isset($_POST['max_time_to_wait_value']))
{
	$max_time_to_wait_value= $_POST['max_time_to_wait_value'];
}
else
{
	$max_time_to_wait_value= "100";
}

if (isset($_POST['timeout']))
{
	if ($_POST['timeout'] == "yes")
	{
		?>
		<input type="radio" name="timeout" value="yes" checked="checked" onchange="document.forms['formulaire'].submit();"/> Oui
		<input type="radio" name="timeout" value="no" onchange="document.forms['formulaire'].submit();"/> Non <br>

		Quel est le temps maximum à attendre (en secondes)? <br> 
		<input type="text" title="temps maximum à attendre" name="max_time_to_wait_value" value="<?php echo $max_time_to_wait_value; ?>"><br>

		<?php
	}
	else
	{
		?>
		<input type="radio" name="timeout" value="yes" onchange="document.forms['formulaire'].submit();"/> Oui
		<input type="radio" name="timeout" value="no" checked="checked" onchange="document.forms['formulaire'].submit();"/> Non <br>
		<?php
	
	}


}
else
{
	?>
	<input type="radio" name="timeout" value="yes" onchange="document.forms['formulaire'].submit();"/> Oui
	<input type="radio" name="timeout" value="no" checked="checked" onchange="document.forms['formulaire'].submit();"/> Non <br>
	<?php
}



?>
<input type="submit" name="action" value="lancer">
</form>
<?php

}




/////////////////////////////////////////////////
// Traitement des valeurs
////////////////////////////////////////////////


if (isset($_POST['action']))
{
	$room = $_POST['room']; // On récupère la salle
	$wakeup= $_POST['wakeup'];
	$image_name = $_POST['image_name'];
	$qos=$_POST['qos'];
	$postaction=$_POST['postaction'];
	$timeout=$_POST['timeout'];
	$all_parts_selected="";
	
	if (isset($_POST['max_time_to_wait_value']))
	{
		$max_time_to_wait_value=$_POST['max_time_to_wait_value'];
	}
	
	$room_or_computer = $_POST['room_or_computer'];
	$all_mac_addr="";
	


	//////////////////////// Permet de mettre un débit MAX //////////////////////////////////////////////////////////

	$file="/opt/drbl/conf/drbl-ocs.conf"; // chemin du fichier de conf à modifier
	//ouverture en lecture et écriture
	$text=fopen($file,'r+');
	$contenu=file_get_contents($file);
	if ($qos == "yes")
	{
		$max_bitrate=$_POST['max_bitrate'];
		$contenu_modif=preg_replace('/udp_sender_extra_opt_default.*/',"udp_sender_extra_opt_default=\"--max-bitrate ".$max_bitrate."m\"", $contenu);
	} 
	else
	{
		$contenu_modif=preg_replace('/udp_sender_extra_opt_default.*/',"udp_sender_extra_opt_default=\"\"", $contenu);
	} 
	fwrite($text,$contenu_modif);
	fclose($text);
	
	

	// Permet d'avoir la valeur du timeout

	if ($timeout == "yes")
	{
		$max_time_to_wait= "--max-time-to-wait ".$max_time_to_wait_value."";

	} 
	else
	{
		$max_time_to_wait="";
	} 
	


	// Permet d'avoir les adresses Mac des postes
	$sql = "SELECT C_mac_addr,C_name FROM computers where C_room='$room' order by C_name";
	$result = mysql_query($sql);
	if (  $room_or_computer == "computer" )
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
		{
			$computer=$row['C_name'];
			if (isset($_POST["$computer"]))
			{
				$all_mac_addr = "".$all_mac_addr." ".$row['C_mac_addr']."";
			}
		}
	}
	else
	{
		// On cherche toutes les adresses MAC de cette salle
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
		{
			$all_mac_addr = "".$all_mac_addr." ".$row['C_mac_addr']."";
		}

	}

	

	////// Permet d'avoir le nombre de client de la salle
	if ( $room_or_computer == "room" )
	{
		$sql = "select count(*) FROM computers where C_room='$room'";
		$result = mysql_query($sql);
		$ligne = mysql_fetch_row($result);
		$nb_host=$ligne[0];
	}
	else
	{
		// On compte le nombre d'adresses mac qu'on a
		$nb_host=str_word_count($all_mac_addr,0,':0123456789');
	}

	if ($nb_host != 0)
	{
		
		if (  $disk_or_parts == "parts" )
		{
			// Permet de récupérer les partitions sélectionnées
			$sql = "SELECT P_name FROM partitions where P_id_image='$image_id' order by P_name";
			$result = mysql_query($sql);
			while (($row = mysql_fetch_array($result, MYSQL_ASSOC) )) 
			{
				$parts_name=$row['P_name'];
				if (isset($_POST["$parts_name"]))
				{
					$all_parts_selected = "".$all_parts_selected." ".$row['P_name']."";
				}
			}

			if ((isset($all_parts_selected)) && ($all_parts_selected != ""))
			{
				if ($nb_host==1) // Utilise de l'unicast
				{
					shell_exec("sudo /opt/drbl/sbin/drbl-ocs -b -e1 auto -e2 -x -j1 -j2 -k -o1 -p $postaction -l fr_FR.UTF-8 startparts restore $image_name $all_parts_selected > /dev/null 2>&1 &") ;
					//echo "sudo /opt/drbl/sbin/drbl-ocs -b -e1 auto -e2 -x -j1 -j2 -k -o1 -p $postaction -l fr_FR.UTF-8 startparts restore $image_name $all_parts_selected > /dev/null 2>&1 & <br>" ;
			
				}
				else // On utilise du multicast
				{
					shell_exec("sudo /opt/drbl/sbin/drbl-ocs -b -e1 auto -e2 -x -j1 -j2 -k -o1 -p $postaction --clients-to-wait $nb_host $max_time_to_wait -l fr_FR.UTF-8 startparts multicast_restore $image_name $all_parts_selected > /dev/null 2>&1 &") ;
					//echo "sudo /opt/drbl/sbin/drbl-ocs -b -e1 auto -e2 -x -j1 -j2 -k -o1 -p $postaction --clients-to-wait $nb_host $max_time_to_wait -l fr_FR.UTF-8 startparts multicast_restore $image_name $all_parts_selected > /dev/null 2>&1 & <br>" ;
				}		

				if  ($wakeup == "yes" )
				{
					shell_exec("sleep 10 && wakeonlan -i ".$addr_broadcast." ".$all_mac_addr."  > /dev/null 2>&1 &");				
					//echo "sleep 10 && wakeonlan -i ".$addr_broadcast." ".$all_mac_addr."  > /dev/null 2>&1 & <br>";
				}

				$succes=true;
			
			}
			else
			{	
				echo "Vous devez choisir au moins une partition !!";
			}
		}
		else
		{ //On utilisera la variable $disk_name qui contient le nom du disque
			if ($nb_host==1) // Utilise de l'unicast
			{
				shell_exec("sudo /opt/drbl/sbin/drbl-ocs -b -e1 auto -e2 -x -j1 -j2 -o1 -p $postaction -l fr_FR.UTF-8 startdisk restore $image_name $disk_name  > /dev/null 2>&1 &") ;
				//echo "sudo /opt/drbl/sbin/drbl-ocs -b -e1 auto -e2 -x -j1 -j2 -o1 -p $postaction -l fr_FR.UTF-8 startdisk restore $image_name $disk_name  > /dev/null 2>&1 & <br>" ;			
			}
			else // On utilise du multicast
			{
				shell_exec("sudo /opt/drbl/sbin/drbl-ocs -b -e1 auto -e2 -x -j1 -j2 -o1 -p $postaction --clients-to-wait $nb_host $max_time_to_wait -l fr_FR.UTF-8 startdisk multicast_restore $image_name $disk_name  > /dev/null 2>&1 &") ;
				//echo "sudo /opt/drbl/sbin/drbl-ocs -b -e1 auto -e2 -x -j1 -j2 -o1 -p $postaction --clients-to-wait $nb_host $max_time_to_wait -l fr_FR.UTF-8 startdisk multicast_restore $image_name $disk_name  > /dev/null 2>&1 & <br>" ;

			}

			if ( $wakeup == "yes" )
			{
				shell_exec("sleep 10 && wakeonlan -i ".$addr_broadcast." ".$all_mac_addr."  > /dev/null 2>&1 &");
				//echo "sleep 10 && wakeonlan -i ".$addr_broadcast." ".$all_mac_addr."  > /dev/null 2>&1 & <br>";
			}

			$succes=true;
		}
	}
	else
	{
		echo "Pas d'hôtes sélectionné";
	}


}



if ($succes==true)
{
	include_once("lightbox.php"); // Contient les cadres qui sont affichés

	start_lightbox("restore"); // On démarre la boite de dialogue de succes
}

?>
</div>
