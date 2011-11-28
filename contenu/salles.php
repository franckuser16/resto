<h1> Gérer les postes et les salles </h1>


<hr>
<?php



$connect=false;

$resultat="<h1 class=\"erreur\">Echec de connexion à la base !</h1>";

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

if (isset($_GET["action"]))
{
    switch ($_GET["action"])
    {	

	case "detection":

	      shell_exec("sudo touch /var/lock/restonux.lock"); // Arrete le script s'il est déjà en exécution
	      shell_exec("rm -f ".$var_www."/macadr-eth*");
	      shell_exec("sudo rm -f /var/lock/restonux.lock"); // On supprime les anciens fichiers
	      shell_exec("sudo ".$var_www."/scripts/drbl-collect-mac $interface_drbl > /dev/null 2>&1 &"); // On démarre le script qui collect les addr Mac en Pxe

	      ?>
	      <p> Allumez vos ordinateurs en boot PXE.<br/> Une fois tous les ordinateurs allumés entrez le nom de la salle et appuyez sur "stop".<br/>
		  Si tous les ordinateurs n'ont pas été détectés vous pouvez recommencer une détection en utilisant le même nom de salle.
	      </p>
	      <hr>
	      <form id="gestion" action="<?php echo ''.$_SERVER['PHP_SELF'].'?menu=salles&action=stop'; ?>" method="post" >
	      <p><label>Nom de la salle</label><input type="text" name="room_name"></p>
	      <input type="submit" name="action" value="stop">
	      </form>
	      <?php

	break;


	case "stop":

		shell_exec("sudo touch /var/lock/restonux.lock"); // On arrete le script qui collecte les adresses Mac

		$room_name=$_POST["room_name"];

		sleep(3); // On attend que le fichier qui contient les adresses Mac se créer

		if ( $connect == true )
		{
			$fichier = fopen("macadr-".$interface_drbl.".txt","r");

			echo "salle $room_name <br>";	

			if ($fichier)
			{
				while (!feof($fichier)) // Pour chaques lignes du fichier
				{
					$mac_addr = fgets($fichier); // on récupère une adresse mac

					if ( $mac_addr != "" ) // Si la ligne est vide on ne rentre pas l'adresse dans la bdd
					{
						echo "$mac_addr<br>";

						// On vérifie que l'adresse n'est pas déja dans la bdd
						$requete="SELECT C_mac_addr FROM computers where C_mac_addr='$mac_addr'";

						$result = mysql_query($requete); // On effectue la requete
						$nbligne=mysql_num_rows($result);
						// Si on obtient une ligne: le nom existe déjà dans la bdd
						if ($nbligne!=1)
						{
							// On insère l'adresse Mac dans la bdd
							$requete="insert into computers values('','','$mac_addr','$room_name')";
							$result = mysql_query($requete); // On effectue la requete
						}
					}
				}

		    		fclose($fichier);

				// Message de succes
				include_once("lightbox.php"); // Contient les cadres qui sont affichés
				start_lightbox("auto_detect_computers"); // On démarre la boite de dialogue de succes		
		
			 }
		}
	break;

	case "generate" :

		if ($connect==true)
	 	{
			//requête permettant de retrouver les données de l'ordinateur 
			$requete = 'Select C_name,C_mac_addr FROM computers';
			$result = mysql_query($requete);
			
			//ouverture du fichier mac-hostname en écriture
			$fichier = fopen("".$_SERVER['DOCUMENT_ROOT']."/mac-hostname", "w");
?>
<p>
Le fichier mac-hostname a été généré et copié à la racine du serveur web <br><br>
<?php
			
			while (($row = mysql_fetch_array($result, MYSQL_ASSOC) )) 
			{
				if ($fichier)
				{	
					echo "".$row['C_mac_addr']." = ".$row['C_name']."<br>";
					fputs($fichier,"".$row['C_mac_addr']." = ".$row['C_name']."\n");
				}
			
			}
			fclose($fichier);
?>
</p>
<?php

			// On copie le fichier "MAC <=> Nom des hotes" dans le répertoire de postrun
			copy("".$_SERVER['DOCUMENT_ROOT']."/mac-hostname",'/opt/drbl/share/ocs/postrun/mac-hostname');
			

			// On recharge la configuration des clients pour qu'ils prennent en compte le fichier mac-hostname
			shell_exec("sudo /opt/drbl/sbin/drbl-gen-ssi-files -n -x  > /dev/null 2>&1 &");
		

		}
	


	
	break;
	case "add_room_manually": //menu permettant d'ajoutter manuelement une salle
?>

<form id="gestion" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="menu" value="salles" /> 
<input type="hidden" name="action" value="visualisation" /> 
<input type="hidden" name="manage-computer" value="save_new_room" />
   <p>
       <label for="room">Entrer le nom de la nouvelle salle :</label>
       <input type="text" name="room" id="room" size="30" maxlength="30" />
   </p>

<?php
for ($count = 1; $count <= 5; $count++)
{
?>
<p>
	<label for="pc_name_<?php echo $count ?>">Entrer le nom de l'ordinateur<?php echo $count ?> :</label>
	<input type="text" name="pc_name_<?php echo $count ?>" id="pc_name_<?php echo $count ?>" size="30" maxlength="30" /><br/>

	<label for="pc_mac_<?php echo $count ?>">Entrer l'addresse mac :</label>
	<input type="text" name="pc_mac_<?php echo $count ?>" id="pc_mac_<?php echo $count ?>" size="17" maxlength="17" /><br/>
</p>
<?php
}
?>	

 <input type="submit" value="Enregistrer les modifications" />
</form>
<?php
	break;
	case "visualisation":
		// Si la variable manage_computer a été transmise on effectue une opération sur la base de donnée ou sur l'ordinateur selon sa valeur
		if (isset($_GET["manage-computer"]))
		{
		    switch ($_GET["manage-computer"])
		    {
				    case "save_new_room":
?>
<hr/>
				<h3> Ajout d'ordinateurs à la base de données </h3>
				<h4> Rapport : </h4>
<p>
<?php
					// vérification que l'utilisateur est bien remplis les champs 
					if ((isset($_GET["room"])) && ($_GET["room"] !=""))
					{
						$room_name=$_GET["room"];
						
						
						//  vérification que l'utilisateur est bien remplis les champs des ordinateurs corectement et ajout à la base de donnée
						$add_at_least_one_computer=false;
						for ($count = 1; $count <= 5; $count++)
						{
							if (isset($_GET["pc_name_$count"]))
							{
								$pc_name=$_GET["pc_name_$count"];

								// vérification de l'existence et de l'exactitude de l'adresse mac 
								if ((isset($_GET["pc_mac_$count"])) && (preg_match("#^(([a-fA-F0-9]){2}:){5}([a-fA-F0-9]){2}$#",$_GET["pc_mac_$count"])))
								{
							
									
									$pc_mac_address=$_GET["pc_mac_$count"];
									
									// enregistrement
									// On vérifie que l'adresse n'est pas déja dans la bdd
									$requete="SELECT C_mac_addr FROM computers where C_mac_addr='$pc_mac_address'";

									$result = mysql_query($requete); // On effectue la requete
									$nbligne=mysql_num_rows($result);
									// Si on obtient une ligne: le nom existe déjà dans la bdd
									if ($nbligne!=1)
									{
										// On insère l'adresse Mac dans la bdd
										$requete="insert into computers values('','$pc_name','$pc_mac_address','$room_name')";
										$result = mysql_query($requete); // On effectue la requete
										$add_at_least_one_computer = true ;
?>
			L'ordinateur " <?php echo $pc_name ?> " a été ajouté à la salle " <?php echo $room_name ?> " <br/>
<?php
									}
									else
									{
?>
			L'adresse mac de l'ordinateur " <?php echo $pc_name ?> " est déjà présente dans la base de donnée. <br/>
<?php
									}
									
								} // isset $_GET["pc_mac"]
								else
								{
?>
			L'adresse mac de l'ordinateur " <?php echo $pc_name ?> " est erronée. <br/>

<?php
								}
							} // isset $_GET["pc_name"]
						} // for 
						
					} // isset $_GET["room"]
				if ( $add_at_least_one_computer==false)
				{
?>
					Aucun ordinateur n'a été ajouté <br/>
<?php
				}
?>
</p>
<hr/>
<?php				    break;
				    case "add_computer_manually_menu":
					
				
				if (isset($_GET["room"]))
				{
					$room_name = $_GET["room"];
?>
				

	<!-- Affichage d'un menu permettant de rajouter manuelement un ordianteur à la salle  -->

<hr/>
<h3>Ajout manuel d'un ordinateur dans la salle :</h3>
<form id="gestion" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" >
<input type="hidden" name="menu" value="salles" /> 
<input type="hidden" name="action" value="visualisation" /> 
<input type="hidden" name="manage-computer" value="add_computer_manually" />
<input type="hidden" name="room" value="<?php echo $room_name; ?>" />

	<p>	
		<label for="pc_name">Entrer le nom de l'ordinateur :</label>
		<input type="text" name="pc_name" id="pc_name" size="30" maxlength="30" /><br/>
	
		<label for="pc_mac">Entrer l'addresse mac de l'ordinateur :</label>
		<input type="text" name="pc_mac" id="pc_mac" size="17" maxlength="17" /><br/>
	</p>
      <input type="submit" value="Enregistrer">
</form>
<hr/>


<?php
				} 
			
			
				    break;
				    
				    case "add_computer_manually":


					if ($connect==true)
					{
						if (isset($_GET["room"]))
						{
							$room_name = $_GET["room"];
?>
<hr/>
				<h3> Ajout de l'ordinateur à la salle "<?php echo $room_name; ?>" </h3>
				<h4> Rapport : </h4>
<p>
<?php
							if ((isset($_GET["pc_mac"])) && (preg_match("#^(([a-fA-F0-9]){2}:){5}([a-fA-F0-9]){2}$#",$_GET["pc_mac"])))
							{
								$pc_name = $_GET["pc_name"];
								$pc_mac_address = $_GET["pc_mac"];
		
								
								// On vérifie que l'adresse n'est pas déja dans la bdd
								$requete="SELECT C_mac_addr FROM computers where C_mac_addr='$pc_mac_address'";

								$result = mysql_query($requete); // On effectue la requete
								$nbligne=mysql_num_rows($result);
								// Si on obtient une ligne: le nom existe déjà dans la bdd
								if ($nbligne!=1)
								{
									// On insère l'adresse Mac dans la bdd
									$requete="insert into computers values('','$pc_name','$pc_mac_address','$room_name')";
									$result = mysql_query($requete); // On effectue la requete
									$add_at_least_one_computer = true ;
?>
		L'ordinateur " <?php echo $pc_name ?> " a été ajouté à la salle " <?php echo $room_name ?> " <br/>
<?php
								}
								else
								{
?>
		L'adresse mac de l'ordinateur " <?php echo $pc_name ?> " est déjà présente dans la base de donnée. <br/>
<?php
								}
							}
							else
							{
?>
 L'adresse mac entrée est erronnée <br/>
<?php	
							}

						}
						else
						{
						}
					}
					else
					{
?>
<p> Erreur de connexion à la base de donnée </p>
<?php
					}
?>
</p>
<hr/>
<?php
				    break;
				    case "edit_computer_setting":
					if (isset($_GET['computer-id']))
					{
					$computer_id = $_GET['computer-id'];

						 if ($connect==true)
				      		 {
							 //requête permettant de retrouver les données de l'ordinateur 
							  $requete = 'Select * FROM computers WHERE C_id="'.$computer_id.'"';
							  $result = mysql_query($requete);
							  $donnes=mysql_fetch_array($result);

							  $computer_name=$donnes["C_name"];
							  $computer_mac=$donnes["C_mac_addr"];
							  $room=$donnes["C_room"];
		?>
<hr/>
<h3>Modification des informations d'un poste:</h3>
<form id="gestion" action="<?php echo ''.$_SERVER['PHP_SELF'].'?menu=salles&action=visualisation&room='.$room.'&manage-computer=save_computer_changes&computer-id='.$computer_id.'' ?>" method="post" >

<p>
<label>Nom de l'ordinateur</label>
<input type="text" title="Nom de l'ordinateur" name="Computer-name" value="<?php echo  $computer_name; ?>"size="30" maxlength="30">
</p>
<p>
<label>Adresse Mac</label><input type="text" name="Mac-address" value="<?php echo $computer_mac; ?>"size="30" maxlength="30" >
</p>

<p>
       <label for="room">Sélectionner la salle de l'ordinateur </label><br />
       <select name="room" id="room">
      

<?php
				//requete permettant de connaître toutes les salles 
				$requete = "SELECT distinct C_room FROM computers";
					  
					  if($result = mysql_query($requete))
					  { 	
						while ($ligne = mysql_fetch_array($result))
						{
						$list_room=$ligne["C_room"];
				// affichage d'une liste contenant toute les salles informatique. La salle actuelle de l'ordinateur est sélectionné par défaut
?>
<option value="<?php echo $list_room; ?> " <?php if ( $room == $list_room){ echo 'selected="selected"';}?> > <?php echo $list_room ?> </option>
<?php
						} //ligne
					  } //result
?>
	   
       </select>
</p>

<p>
<input type="submit" name="action" value="modifier">
</p>
</form>
<hr/>
<?php
					  
							  
							 
						 } //connect 
					} //isset
				    break;
				    


				    case "delete_room":
?>
<hr/>
<?php

					if (isset($_GET["room_that_sucks"]))
					{
						$room = $_GET["room_that_sucks"];
		
						//requête de suppression 
						 $requete = "DELETE FROM computers WHERE C_room='$room'";
						 
						 if($result = mysql_query($requete))
						 {
?>
<p> La salle a été supprimé de la base de donnée</p>
<?php
						 
						 }
						 else
						 {
?>
<p> Une erreur est survenue lors de la suppression de la salle</p>
<?php
						 }
					} //isset
?>
<hr/>
<?php
				    break;


				    case "delete_computer":
?>
<hr/>
<?php
					if (isset($_GET["computer-id"]))
					{
						$computer_id = $_GET["computer-id"];
		
						//requête de suppression 
						 $requete = 'DELETE FROM computers WHERE C_id='.$computer_id.'';
						 
						 if($result = mysql_query($requete))
						 {
?>
<p> L'ordinateur a été supprimé de la base de donnée</p>
<?php
						 
						 }
						 else
						 {
?>
<p> une erreur est survenue lors de la mise à jour des infos</p>
<?php
						 }
					} //isset
?>
<hr/>
<?php
				    break;



				    case "save_computer_changes":
?>
<hr/>
<?php
					    if (isset($_GET["computer-id"]))
					    {
							$computer_id=$_GET["computer-id"];
							$computer_name=$_POST["Computer-name"];
							$mac_address=$_POST["Mac-address"];
							$room=$_POST["room"];
	
							//vérification de l'adresse mac avec une expression régulière [a-fA-F0-9] corespond à tout les chiffres + lettre de a à f minuscules majuscules

							if (preg_match("#^(([a-fA-F0-9]){2}:){5}([a-fA-F0-9]){2}$#",$mac_address))
							{
								//envoie de la requête pour mettre à jour
								
						 		$requete = "UPDATE computers SET C_name = '".$computer_name."', C_mac_addr ='".$mac_address."', C_room = '".$room."' WHERE C_id = '".$computer_id."' ";	
								 if($result = mysql_query($requete))
			 					 {
?>
<p>Les mises à jour ont été effectuée</p>
<?php
								 }
								 else
							         {
?>
<p>Un problème de connexion à la base à empêcher la modification des données</p>
<?php
								 }
								
							}
							else
							{
?>
<p>Vous avez entré une mauvaise adresse mac "<?php echo $mac_address ?>"</p>
<?php
							}
						 
					    } //isset $_GET["computer-id"]
?>
<hr/>
<?php
			
				    break;
					
				    case "rename_room_menu":
?>
<hr/>
<?php
				  // récupération des données 
				  $old_room_name=$_GET["old_room_name"];
?>
<h3>Renommer la salle "<?php echo $old_room_name ?>" :</h3>

<form id="gestion" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" >
<input type="hidden" name="menu" value="salles" /> 
<input type="hidden" name="action" value="visualisation" /> 
<input type="hidden" name="manage-computer" value="save_new_room_name" />
<input type="hidden" name="old_room_name" value="<?php echo $old_room_name; ?>" />

	<p>	
		<label for="new_room_name">Entrer le nouveau nom de la salle :</label>
		<input type="text" name="new_room_name" id="new_room_name" size="30" maxlength="30" /><br/>
	</p>
      <input type="submit" value="Modifier">
</form>

<hr/>
<?php				    
				    break;
				    case "save_new_room_name":
	
				
?>
<hr/>
<h3>Modification des informations de la salle </h3>
<h4>Rapport : </h4>
<p>
<?php
					 	$new_room_name=$_GET["new_room_name"];
						$old_room_name=$_GET["old_room_name"];

						// mise à jour du nom de la salle
						$requete = "UPDATE computers SET C_room = '".$new_room_name."' WHERE C_room = '".$old_room_name."' ";	
						 if($result = mysql_query($requete))
	 					 {
?>
							Le changement de nom a été éffectué<br/>
<?php
						 }
						 else 
						 {
?>
							Une est surevenue lors de la connexion à la base de donnée</br>	
<?php
						 } 
?>
</p>
<hr/>
<?php
				    break;
				    case "wake-up":
?>
<hr/>
<?php
					
					  $computer_id=$_GET["computer-id"];
					  $requete = 'Select C_mac_addr FROM computers WHERE C_id="'.$computer_id.'"';
					  $result = mysql_query($requete);
					  $donnes=mysql_fetch_array($result);
					  $computer_mac=$donnes["C_mac_addr"];
				
				  	  shell_exec("wakeonlan -i ".$addr_broadcast." ".$computer_mac."  > /dev/null 2>&1 &");
?>
<hr/>
<?php
				    break;
				    } // fin du switch case

		} // isset $_Get["manage_computer"]

		// Si la variable salle à été transmise on affiche tout les ordinateurs de cette salle 
		if (isset($_GET["room"]))
		{
		     	$room=$_GET["room"];

		     	if ($connect==true)
		      	{
?>
<p>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=salles&action=visualisation&manage-computer=add_computer_manually_menu&room=<?php echo $room; ?>" > Ajouter un ordinateur manuellement  </a><br/>
</p
<table id="listing" >
<caption>Salle : <?php echo $room ?> </caption>
  <tr>
       <th colspan="3">Action </th>
       <th>Nom de l'ordinateur </th>
       <th>Adresse mac</th>
  </tr>
<?php				 //récupération des information pour tout les ordi de cette salle
				 
				 $requete = 'SELECT * FROM computers where C_room="'. $room.'"';
			  
			 	 if($result = mysql_query($requete))
			 	 {
					 while ($ligne = mysql_fetch_array($result))
				   	 {
					 
					 $computer_id=$ligne["C_id"];
					 $computer_name=$ligne["C_name"];
					 $mac_address=$ligne["C_mac_addr"];
?>
<tr>
       <td> <a href="<?php echo ''.$_SERVER['PHP_SELF'].'?menu=salles&action=visualisation&room='.$room.'&manage-computer=edit_computer_setting&computer-id='.$computer_id.' '?>" > Modifier </a></td>
       <td> <a href="<?php echo ''.$_SERVER['PHP_SELF'].'?menu=salles&action=visualisation&room='.$room.'&manage-computer=wake-up&computer-id='.$computer_id.''?>" > Réveiller </a></td>
       <td> <a href="<?php echo ''.$_SERVER['PHP_SELF'].'?menu=salles&action=visualisation&room='.$room.'&manage-computer=delete_computer&computer-id='.$computer_id.' '?>" > Supprimer </a></td>
       <td> <?php echo $computer_name ?>	</td>
       <td> <?php echo $mac_address ?>		</td>
       
</tr>
<?php
					 } //fetch array
					 mysql_free_result($result);
				
				 } //requete 	
?>
</table>
<?php
		      } //connect 
		}
		// Sinon on affiche toutes les salles avec le nombre d'ordinateurs 
		else
		{

		      if ($connect==true)
		      {
			  $requete = "SELECT distinct C_room FROM computers";
			  
			  if($result = mysql_query($requete))
			  { 	
?>
<table id="listing" >
<caption>Présentation des salles</caption>
  <tr>
       <th colspan="3" >Action </th>
       <th>Nom de la salle </th>
       <th>Nombre d'ordinateurs</th>
  </tr>
<?php
	
				   //liste les différentes salles 
				   while ($ligne = mysql_fetch_array($result))
				    {
		
					
					  $room=$ligne["C_room"];

					  // compte le nombre d'ordinateur pour chaque salle
				 	  $requete = 'Select count(*) AS nb_computers from computers where C_room="'.$ligne["C_room"].'"';

					  $result2 = mysql_query($requete);
					  $donnes=mysql_fetch_array($result2);
				
					  $nb_computer=$donnes["nb_computers"];
					
?>
<tr>
       <td> <a href="<?php echo ''.$_SERVER['PHP_SELF'].'?menu=salles&action=visualisation&room='.$room.' '?>" title="Modifier les paramètres des ordinateurs de la salle "> Gérer </a></td>
<td> <a href="<?php echo ''.$_SERVER['PHP_SELF'].'?menu=salles&action=visualisation&manage-computer=rename_room_menu&old_room_name='.$room.''?>" title="Renommer la salle"> Renommer </a></td>
       <td> <a href="<?php echo ''.$_SERVER['PHP_SELF'].'?menu=salles&action=visualisation&manage-computer=delete_room&room_that_sucks='.$room.''?>" title="Supprime la salle "> Supprimer </a></td>
       <td> <?php echo $room ?>	</td>
       <td> <?php echo $nb_computer ?></td>
       
</tr>
<?php
						  //affichage de la ligne du tableau
				     }
?>
</table>
<?php

				 // mysql_free_result($result);
				  //mysql_free_result($result2);		
			  }
			  else
			  {
?>
<h1 class="erreur">erreur requete !</h1>
<?php
			  }
		      } // connect true
		} //else
	break;	
	} // fin du switch $Get_action visualisation_
}
else 
{
//Si la variable action n'est pas passé dans l'adresse alors on affiche les deux liens  détection et visualisation
?>
<p>
<a id="lien_important" href="<?php echo "".$_SERVER['PHP_SELF']."?menu=salles&action=detection"; ?>" > Détection des ordinateurs </a>
Vous pourrez détecter les adresses MAC des ordinateurs pour les mettres dans une salle. Pour cela, il faut démarrer les ordinateurs que vous voulez détecter via boot PXE. Ils seront automatiquement rajouté à la base de donnée, vous deverez ensuite <a href="<?php echo "".$_SERVER['PHP_SELF']."?menu=salles&action=visualisation"; ?>" >mettre un nom d'ordinateur pour chaque adresses MAC.</a><br><br>

<a id="lien_important" href="<?php echo "".$_SERVER['PHP_SELF']."?menu=salles&action=add_room_manually"; ?>" > Créer une salle manuellement </a>
Un nom de salle vous sera demandé pendant la détection des ordinateurs. Mais vous pouvez créer une salle manuellement et ajouter les adresses MAC vous même.<br><br>

<a id="lien_important" href="<?php echo "".$_SERVER['PHP_SELF']."?menu=salles&action=visualisation"; ?>" > Visualisation et gestion des ordinateurs </a>
Vous allez pouvoir visualiser les ordinateurs qui sont enregistré dans la base de donnée. Vous pourrez modifier les noms de ordinateurs, les salles et les adresses MAC.<br><br>

<a id="lien_important" href="<?php echo "".$_SERVER['PHP_SELF']."?menu=salles&action=generate"; ?>" > Généreration du fichier mac-hostname </a>
Ce fichier permet de faire la correspondance entre les adresses MAC des ordinateurs et le nom de ces ordinateurs. Ce fichier est indispensable pour pouvoir changer le nom des ordinateurs automatiquement après une restauration. Vous devez générer ce fichier après chaque modification de la base de donnée.<br><br>
</p>
<?php
}
?>
