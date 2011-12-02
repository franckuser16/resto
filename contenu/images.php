<h1> Gérer les images </h1>
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
	// Détection automatique des images
	if ($connect==true) 
	{
		$mydir = '/home/partimag/'; 

	 	if ($dir = @opendir($mydir)) 
	 	{
		
		//récupération de la liste des sauvegardes et enregistrement dans un array
		 while (($file = readdir($dir)) !== false) 
	   	 { 
	 	    if($file != ".." && $file != ".") 
	   	    { 
	   	      $filelist[] = $file; 
	   	    } 
	   	 } 

		// On obtient le nombre d'images
		if (isset($filelist))
		{
			foreach( $filelist as $image_name )
			{

				// On vérifie que le nom de l'image n'est pas déja dans la bdd
				$requete="SELECT I_name FROM images where I_name='$image_name'";
			
				if($result = mysql_query($requete)) // On effectue la requete
				{ 
					$nbligne=mysql_num_rows($result);
					// Si on obtient une ligne: le nom existe déjà dans la bdd
					if (($nbligne != 1 ) && ( $image_name != "" ) ) // On ne fait pas la détection de cette image
					{			
						// on récupère le nom du disque ( sda ou hda )
					
						$filename = '/home/partimag/'.$image_name.'/disk';

						if (file_exists($filename)) 
						{
						    $disk=shell_exec("cat /home/partimag/$image_name/disk");
						    $disk=trim($disk); // On supprime les espaces indésirable
						}
						else
						{
						    $disk="";
						}
						    // On insère dans la bdd l'image n dans la table image
						    $requete="insert into images values(' ','$image_name','$disk',' ')";

						    if( ! $result = mysql_query($requete)) // Si la requete a échouée, il y a une erreur
						    { 
							    echo "Erreur lors de l'ajout de l'image dans la base de donnée";
						    }
	?>
		<p> Nom du répertoire de l'image détectée : <?php echo $image_name;?><br>
	<?php

						// On obtient le nombre de partition que contient cette image
						$nb_parts=shell_exec("cat /home/partimag/$image_name/parts | wc -w");
					
						for($j=1;$j<=$nb_parts;$j++)
						{
							// On chope la partition n de cette image
							$part_name=shell_exec("cat /home/partimag/$image_name/parts | tr -s ' ' '=' | cut -d= -f$j");
							$part_name=trim($part_name); // On supprime les espaces indésirable


							// On chope l'id de l'image 
							$requete="SELECT I_id FROM images where I_name='$image_name'";

							if( $result = mysql_query($requete))
							{ 
								$nbligne=mysql_num_rows($result);
							}

							if ($nbligne==1)
							{
								$ligne = mysql_fetch_row($result);
								$id_image=$ligne[0];
							}
							else
							{
								echo "L'id de l'image n'a pas été retrouvé";
							}



							// On rempli la table partitions
							$requete="insert into partitions values('','$part_name',' ','$id_image')";

							if( ! $result = mysql_query($requete))
							{ 
								echo "Les partitions n'ont pas été ajoutées à la base de donnée";
							}


							echo "images des partitions : $part_name";
							echo "<br>";
						}
	?>
					      </p><hr>
	<?php	
					}
					else
					{
						echo "L'image $image_name est déjà dans la base de donnée !! <br>";
					}
				}
				else
				{
				  echo "erreur";
				}
			}
		}
		else
		{
			echo "Aucune image détectée";
		}
	
	closedir($dir); 
	}

	}
	break;
	

	case "visualisation":
	
	if (isset($_GET["manage-database"]))
	{
		switch ($_GET["manage-database"])
		{	

			case "edit":
?>			<hr/>
<?php
			if (isset($_GET["image_id"]))
			{
				$image_id=$_GET["image_id"];
					
				
				$requete = "SELECT I_name,I_description FROM images WHERE I_id=$image_id"; 	 	
						  
				if($result = mysql_query($requete))
				{
					$ligne = mysql_fetch_array($result);
					$image_name=$ligne["I_name"];
					$image_description=$ligne["I_description"];
			
?>				<h3>Modification de l'image : <?php echo $image_name ?> </h3>

				<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">

				<!-- méthode permettant de rajouter des paramètres dans l'adresse (on utilise le formulaire avec la méthode get et non post-->		
				<input type="hidden" name="menu" value="images" /> 
				<input type="hidden" name="action" value="visualisation" /> 
				<input type="hidden" name="manage-database" value="modify" /> 
				<input type="hidden" name="image_id" value="<?php echo $image_id ?>" /> 
	
  				<p>
     					<label for="image_description"> description de l'image :</label>
    					<input type="text" name="image_description" id="image_description" value="<?php echo $image_description ?> " size="30" maxlength="30" />
  				</p>
				

<?php
					$requete2 = 'SELECT P_id,P_name,P_description FROM partitions WHERE P_id_image='.$image_id.'';
					
					if($result2 = mysql_query($requete2))
					{
						// variable servant à incrémenter
						$count = 1 ;
						while($ligne2 =mysql_fetch_array($result2))
						{
						$partition_id = $ligne2["P_id"];
						$partition_name = $ligne2["P_name"];
						$partition_description = $ligne2["P_description"];
					
?>	
						<p>
     					<label for="partition<?php echo $count ?>">Description de la partition : <?php echo $partition_name ?> </label>
    					<input type="text" name="partition_description_<?php echo $count ?>" id="partition<?php echo $count ?>" value="<?php echo $partition_description ?> " size="30" maxlength="30" />
					<input type="hidden" name="partition_id_<?php echo $count ?>" value="<?php echo $partition_id ?>" /> 
  						</p>
									
<?php		
						$count++;						
						} //fin du while
					
?>					<input type="hidden" name="number_partition" value="<?php echo $count-1 ?>" /> 		
					<p>
					<input type="submit" value="Enregistrer les modifications" />
					</p>
					</form>		
<?php
					 
					} //fin du if result2
				 mysql_free_result($result2);
				 mysql_free_result($result);
				} //fin du if result 
			}
?>			<hr/>
<?php
			break;
			case "modify":
			
			if (isset($_GET["image_id"]))
			{
				$image_id=$_GET["image_id"];
				$image_description=$_GET["image_description"];
				
				//mise à jour de la description de l'image
				$requete = "UPDATE images SET I_description = '$image_description' WHERE I_id =$image_id";
				
				if($result = mysql_query($requete))
				{
					//mise à jour de la description des partitions
					$number_partition=$_GET["number_partition"];
					
					for ($count = 1; $count <= $number_partition; $count++)
					{
						if (isset($_GET["partition_id_$count"]))
						{
							$partition_description = $_GET["partition_description_$count"];
							$partition_id = $_GET["partition_id_$count"];

							//modification des infos
							$requete2 = "UPDATE partitions SET P_description = '$partition_description' WHERE P_id = '$partition_id' ";
							if(!$result2 = mysql_query($requete2))
							{
?>
							<p> Une erreur est survenue lors de la mise à jour des informations sur la partition</p>
<?php 
							}
						} 
						else
						{
							echo "erreur de l'interface chaise-clavier codeuse<br>";
						}
					} //fin du for 
?>					<p> Mise à jour des informations effectuée </p>
<?php 				
				}
				else
				{
?>					<p> Une erreur est survenue lors de la mise à jour des informations sur l'image </p>
<?php 
				}
			} //fin du isset $_GET["image_id"]
			
			
?>
<?php
			break;
			case  "delete":
			
			if (isset($_GET["image_id"]))
			{
				 $image_id=$_GET["image_id"];
				 $directory_name=$_GET["directory_name"];
				
				 if ($directory_name != "")
				 {
				 	// suppression du dossier sur le serveur drbl 
				 	shell_exec("sudo rm -rf /home/partimag/$directory_name");

				 	//suppression de l'entrée dans la table image
				 	$requete = 'DELETE FROM images WHERE I_id='.$image_id.'';
				 }


				
				if(!$result = mysql_query($requete))
				{
?>					<p>Une erreur est survenue pendant la suppression de l'image</p>
<?php					
				}
			
				$requete2 = 'DELETE FROM partitions WHERE P_id_image='.$image_id.'';
				
				if(!$result2 = mysql_query($requete2))
				{
?>					<p>Une erreur est survenue pendant la suppression de l'image</p>
<?php					
				}
?>					<p>L'image a été supprimée de la base de donnée et de l'ordinateur</p>
<?php		
			}
			break;
		}
	}
	
	
	
	//menu d'affichage des images et des partitions sauvegardés
	if ($connect=="true")
	{
		$requete = "SELECT I_id,I_name,I_description FROM images"; 	 	
					  
		if($result = mysql_query($requete))
		{
			while ($ligne = mysql_fetch_array($result))
			{
				$image_id=$ligne["I_id"];
				$image_name=$ligne["I_name"];
				$image_description=$ligne["I_description"];

?>
			<table id="listing" >
			  <caption><?php echo '-Nom : '.$image_name.' -Description : '.$image_description.'';?></caption>

			<tr>
			       <th>Nom de la partition</th>
			       <th>Description de la partition</th>
			</tr>
			   
<?php
				$requete = 'SELECT P_name,P_description FROM partitions WHERE P_id_image='.$image_id.'';
						  
				if($result2 = mysql_query($requete))
				{

					while ($ligne2 = mysql_fetch_array($result2))
					{
						$partition_name=$ligne2["P_name"];
						$partition_description=$ligne2["P_description"];
?>
					   <tr>
					       <td><?php echo $partition_name;		?></td>
					       <td><?php echo $partition_description;	?></td>
					   </tr>
<?php
					}
				

				} //fin de la deuxième requète
?>			</table>
			<p>
			<a id="modif_supp" href="<?php echo $_SERVER['PHP_SELF'];?>?menu=images&action=visualisation&manage-database=edit&image_id=<?php echo $image_id;?>" title="modifier les descriptions"> Modification </a>
			<a id="modif_supp" href="<?php echo $_SERVER['PHP_SELF'];?>?menu=images&action=visualisation&manage-database=delete&image_id=<?php echo $image_id;?>&directory_name=<?php echo $image_name;?>" title="modifier les descriptions"> Suppression </a>
			</p>
<?php
			} //fin du premier while
		} //fin de la première requete
	} // conect = true
	break;
	} //fin du switch case $_Get['action']
} // fin du isset $_Get['action']
else
{
?>
<p>

 	<a id="lien_important" href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=images&action=visualisation" title="Visualisation"> Visualisation </a>
Vous pourrez visualiser et supprimer les images qui sont stockée sur le serveur.<br><br>



	<a id="lien_important" href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=images&action=detection" title="Détection"> Détection </a>
La détection permet de mettre à jour la base de donnée à partir des images qui sont stockée sur le serveur. Après chaque sauvegarde, vous devez détecter les nouvelles images pour qu'elles soient prises en compte.
</p>
<?php
}



?>
