<?php
include_once("conf/config.php");

/* les variables menu ici */
$list_menu = array (
	"accueil" 	=> "Accueil",
	"restore" 	=> "Effectuer une restauration",
	"save"	=> "Créer une nouvelle sauvegarde",
	"actions_distance" 	=> "Actions à distance",
	"adhesion_domain"	=> "Adhésion au domaine",
	"salles" 	=> "Gérer les salles",
	"comptes_utilisateurs"	=> "Gérer les comptes utilisateurs",
	"images"	=> "Gérer les images",
	"domaines"	=> "Gestion des domaines");



session_start();

//franck: mardi 29 novembre 2011, 11:38:46 (UTC+0100)
//
//cet import ne sert à rien
//functions.php définit:
//-start_lightbox qui n'est pas utilisé dans cette page
//-redirection qui n'est utilisé nulle part
include_once("functions.php");

if(isset($_SESSION['login']))
{
	$right=$_SESSION['right'];
	$login=$_SESSION['login'];
}
else
{
	$right=0;
	$_SESSION['right']=0;
	$login="";
	$_SESSION['login']="";
	$menu="accueil";
}


if (isset($_GET["menu"]))
{
	$menu=$_GET["menu"];
}
else
{
	$menu="accueil";
}


if ($menu == "logout")
{
	session_destroy();
	$right=0;
	$_SESSION['right']=0;
	$login="";
	$_SESSION['login']="";
	$menu="accueil";
}


if ($menu == "access")
{
	include_once("acces_bdd.php");
	//changement du menu la page access n'existe pas 
	$menu="accueil";
}


if ($right == 0)
{
	$titre="Authentification";
}
else
{
	$titre=$list_menu["$menu"];
}


?>


<html>
<head><title>Restonux</title>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="contenu/style.css">
<link rel="icon" type="image/png" href="/images/icone.png" />

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<!-- JavaScript -->
<script type="text/javascript" src="scripts/prototype.js"></script>
<script type="text/javascript" src="scripts/lightbox.js"></script>


</head>
<body>
<div id="ensemble">

<div id="baniere">
<a href="<?php echo $_SERVER['PHP_SELF']; ?>" ><img src="images/phototete.jpg"></a>
</div>

<div id="entete">
<h1><?php echo "$titre"; ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=logout" ><img src="images/deco.png"></a></h1>
</div>

<div id="menu">

<?php
if ($right != 0)
{
?>
<!-- Affichage du menu-->
<br>
<menu_top><a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=accueil">Accueil</a></menu_top>
<ul></ul>
<menu_top><lien_ok_jaune>Restauration</lien_ok_jaune></menu_top>
<ul>
	<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=restore">Effectuer une restauration</a></li>
</ul>
<menu_top><lien_ok_jaune>Sauvegarde</lien_ok_jaune></menu_top>
<ul>
	<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=save">Créer une nouvelle sauvegarde</a></li>
</ul><menu_top><lien_ok_jaune>Administration des postes</lien_ok_jaune></menu_top>
<ul>
	<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=adhesion_domain">Adhésion au domaine</a></li>
	<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=actions_distance">Actions à distance</a></li>
</ul>
<menu_top><lien_ok_jaune>Gestion de la base de donnée</lien_ok_jaune></menu_top>
<ul>
	<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=salles">Gérer les salles</a></li>
	<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=images">Gérer les images</a></li>
	<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=comptes_utilisateurs">Gérer les comptes utilisateurs</a></li>
	<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=domaines">Gestion des domaines</a></li>
</ul>

<?php
}
?>
</div>

<div id="contenu">
<?php

if ($right == 0)
{
	include_once("authentification.php");
}
else
{
	if (file_exists("contenu/$menu.php"))
	{
		include_once("contenu/$menu.php");
	}
	else
	{
		include_once("contenu/notfound.php");
	}
}

?>

</div>

<div id="pied">
<p>

			Design by: <a href="http://www.styleshout.com/">styleshout | </a> <a href="http://www.iutlaroche.univ-nantes.fr">IUT La Roche/Yon | Réseau et Télécommunication </a>

</p>

<a href="<?php echo $_SERVER['PHP_SELF']; ?>" ><img src="images/footer.jpg"></a>
</div>


</div>
</body>
</html>
