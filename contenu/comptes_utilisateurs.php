<h1>Gestion des comptes</h1>
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


if (isset($_GET["cle"]))
{
	$cle=$_GET["cle"];
	$login="";
	$pass="";
	if ($connect==true)
	{
		$requete = "SELECT * FROM users where U_id='$cle'";

		if($result = mysql_query($requete))
		{ 
			$nbligne=mysql_num_rows($result);
			if ($nbligne==1)
			{
				$ligne = mysql_fetch_row($result);
				$id=$ligne[0];
				$login=$ligne[1];
				$pass=$ligne[2];
				$right=$ligne[3];
				$resultat="";
			}
			mysql_free_result($result);	
		}
		else
		{
			$resultat="<h1 class=\"erreur\">erreur requete !</h1>";
		}
	}
}
else
{
	if (isset($_POST["action"]))
	{
		$login=$_POST["login"];
		$pass=$_POST["pass"];
		
		if (isset($_POST["id"]))
		{
			$id=$_POST["id"];
		}
		
		$right=$_POST["right"];
		$action=$_POST["action"];
		$resultat="";
		switch($action)
		{
			case "inserer":
				$requete="insert into users values('','$login','$pass','$right')";
				break;
			case "modifier":
				$requete="update users set U_password='$pass' where U_login='$login'";
				break; 
			case "supprimer":
				$requete="delete from users where U_login='$login'";
				$login="";
				$pass="";
				break;
		
		}
		if(mysql_query($requete)==false)
		{
			$resultat="<h1 class=\"erreur\">erreur requete !</h1>";
		}
		else
		{
			if ($action=="inserer")	$id=mysql_insert_id();
		}
		
	}
	else
	{
		$nom="";
		$pass="";
		$resultat="";
	}
}
?>

<form id="gestion" action="<?php echo $_SERVER['PHP_SELF'] ?>?menu=comptes_utilisateurs" method="post" >

 <p><label>nom</label>
 <input type="text" title="nom de l'utilisateur" name="login" value=<?php echo $login; ?>></p>
 <p><label>mot de passe</label><input type="text" name="pass" value=<?php echo $pass; ?>></p>
 <p><label>droit</label><input type="text" name="right" value=<?php echo $right; ?>></p>
 <p>
  <input type="submit" name="action" value="modifier">
   <input type="submit" name="action" value="supprimer">
 <input type="submit" name="action" value="inserer">
</p>
</form>
<hr>
<?php echo $resultat; ?>
<hr>
<br>
<?php
	
	if ($connect==true)
	{
			$requete = "SELECT * FROM users";
			if($result = mysql_query($requete))
			{ 
				$nbcolonne=mysql_num_fields($result);
				$nbligne=mysql_num_rows($result);
				echo '<table id="listing">';
				echo '<tr><th>id</th><th>login</th><th>pass</th><th>droit</th></tr>';
				
				while($ligne = mysql_fetch_row($result))
				{
					echo"<tr>";
					echo "<td><a href=\"".$_SERVER["PHP_SELF"]."?menu=comptes_utilisateurs&cle=".$ligne[0]."\">".$ligne[0]."</td>";
					for($i=1;$i<$nbcolonne;$i++)
					{
						echo "<td>".$ligne[$i]."</td>";
					}
					echo"</tr>";
				}
				echo "</table>";
				mysql_free_result($result);
			}
			else
			{
				echo "<h1 class=\"erreur\">echec requete</h1>";
			}
			mysql_close($db);
	}
	else
	{
		echo "<h1 class=\"erreur\">Echec de connexion à la base !</h1>";
	}
		

?>


