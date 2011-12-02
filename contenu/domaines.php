
<h1> Enregistrer un domaine dans la base de donnée </h1>

<hr>

<div id="style">
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
	$domain_password="";
	if ($connect==true)
	{
		$requete = "SELECT * FROM domain where D_id='$cle'";

		if($result = mysql_query($requete))
		{ 
			$nbligne=mysql_num_rows($result);
			if ($nbligne==1)
			{
				$ligne = mysql_fetch_row($result);
				$domain_id=$ligne[0];
				$domain_name=$ligne[1];
				$host_admin=$ligne[2];
				$domain_admin=$ligne[3];
				$domain_password=$ligne[4];
				
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
		$domain_name=$_POST["domain_name"];
		$host_admin=$_POST["host_admin"];
		$domain_admin=$_POST["domain_admin"];
		$domain_password=$_POST["domain_password"];
		$action=$_POST["action"];
		$resultat="";
		switch($action)
		{
			case "inserer":
				$requete="insert into domain values(' ','$domain_name','$host_admin','$domain_admin','$domain_password')";
				break;
			case "supprimer":
				$requete="delete from domain where D_name='$domain_name'";
				break;
		
		}
		if(mysql_query($requete)==false)
		{
			$resultat="<h1 class=\"erreur\">erreur de la requete !</h1>";
		}
		else
		{
			if ($action=="inserer")	$id=mysql_insert_id();
		}
		
	}
	else
	{
		$domain_password="";
		$domain_name="";
		$host_admin="";
		$domain_admin="";
		$resultat="";
	}
	
}
?>


<form id="gestion" action="<?php echo $_SERVER['PHP_SELF']; ?>?menu=domaines" method="post" >

 <p><label>Nom du domaine</label> <input type="text"  name="domain_name" value=<?php echo $domain_name; ?>></p>
 <p><label>Nom du compte admin en local</label><input type="text" name="host_admin"  value=<?php echo $host_admin; ?>></p>
 <p><label>Login du controleur de domaine</label><input type="text" name="domain_admin" value=<?php echo $domain_admin; ?>></p>
 <p><label>Mot de passe du controleur de domaine</label><input type="password" name="domain_password" value=<?php echo $domain_password; ?>></p>
 <p>
<br>
   <input type="submit" name="action" value="supprimer">
 <input type="submit" name="action" value="inserer">
</p>
</form>

<hr>
<?php 
echo $resultat;
 ?>
<hr>
<br>
<?php
	
	if ($connect==true)
	{
			$requete = "SELECT D_id,D_name,D_host_admin,D_domain_admin FROM domain";
			if($result = mysql_query($requete))
			{ 
				$nbcolonne=mysql_num_fields($result);
				$nbligne=mysql_num_rows($result);
				echo '<table id="listing">';
				echo '<tr><th>id</th><th>Nom domaine</th><th>Compte en local</th><th>Login domaine</th></tr>';
				
				while($ligne = mysql_fetch_row($result))
				{
					echo"<tr>";
					echo "<td><a href=\"".$_SERVER["PHP_SELF"]."?menu=domaines&cle=".$ligne[0]."\">".$ligne[0]."</td>";
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
</div>

