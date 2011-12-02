<?php

if (isset($_POST["action"]))
{
$pass=$_POST["pass"];
$resultat="<h1 class=\"erreur\">authentification echec</h1>";


	if($db = mysql_connect($mysql_hote, $mysql_login, $mysql_password))
	{
		$login = mysql_real_escape_string($_POST['login']); //contre les injections sql
		
		if ($id_db = mysql_select_db($mysql_database))
		{
			$requete = "SELECT U_password,U_right  FROM users WHERE U_login='$login'";
			if($result = mysql_query($requete))
			{ 
				$nbligne=mysql_num_rows($result);
				if ($nbligne==1)
				{
					 $ligne = mysql_fetch_row($result);
					 $passebdd=$ligne[0];
					if ($pass==$passebdd)
					{
						$right=$ligne[1];
						$_SESSION['right']=$right;
						$_SESSION['login']=$login;
						$menu=0;
						
					}
					else
					{
					$_SESSION['right']=0;
					$right=0;
					$resultat= "<h1 class=\"erreur\">mot de passe éroné</h1>";
					}
				}
				else
				{
					$_SESSION['right']=0;
					$right=0;
					$resultat= "<h1 class=\"erreur\">utilisateur inconnu</h1>";
				}
								
				mysql_free_result($result);
			}
			else
			{
				
				$resultat= "<h1 class=\"erreur\">echec de la requete  </h1>";
				$_SESSION['right']=0;
				$right=0;
			}
			
		}
		else 
		{
			$resultat= "<h1 class=\"erreur\">Echec de connexion à la base !</h1>";
			$_SESSION['right']=0;
			$right=0;
		}
		mysql_close($db);
	}
	else 
	{
		$resultat= "<h1 class=\"erreur\">Echec de connexion à mysql !</h1>";
		$_SESSION['right']=0;
		$right=0;
	}

}
else
{

	$resultat="";

}
?>