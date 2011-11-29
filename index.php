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


<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" media="screen" href="test.css"/> 
<!--[if IE ]>
        <link rel="stylesheet" href="style_ie.css" />
<![endif]-->

<!--<link rel="stylesheet" type="text/css" href="style.css">-->
<!--<link rel="stylesheet" type="text/css" href="contenu/style.css">-->

<!--
<link rel="stylesheet" type="text/css" media="screen" href="http://sfa.univ-poitiers.fr/jsp/styles/composantes-defaut/bandeauup.css" />
<link rel="stylesheet" type="text/css" media="print" href="http://sfa.univ-poitiers.fr/jsp/styles/composantes-defaut/bandeauup-print.css" /> 
-->
<!-- Favicon pour Firefox -->    
<link rel="icon" type="image/gif" href="http://sfa.univ-poitiers.fr/images/favicon.gif" />
<!-- Favicon pour IE -->
<link rel="shortcut icon" href="http://sfa.univ-poitiers.fr/images/favicon.ico" />    
<!-- JavaScript -->
<!--<script type="text/javascript" src="scripts/prototype.js"></script>-->
<script type="text/javascript" src="scripts/lightbox.js"></script>

<!--<script type="text/javascript" src="http://sfa.univ-poitiers.fr/adminsite/toolbox/toolbox.js"></script>-->
<!--<script type="text/javascript" src="http://sfa.univ-poitiers.fr/jsp/scripts/defaut.js"></script>-->
<script type="text/javascript" src="http://sfa.univ-poitiers.fr/jsp/scripts/jquery-1.4.2.js"></script>
<!--<script type="text/javascript" src="http://sfa.univ-poitiers.fr/jsp/scripts/browserdetect.js"></script>-->

<script type="text/javascript">
<!--
$(document).ready( function () {
    // On cache les sous-menus :
    //$(".navigation ul.subMenu").hide();
    
    $(".navigation ul.subMenu:not('.open_at_load')").hide();
    $("#null").addClass("open");
    
    // On sï¿½lectionne tous les items de liste portant la classe "toggleSubMenu"
    // et on remplace l'ï¿½lï¿½ment span qu'ils contiennent par un lien :
    $(".navigation li.toggleSubMenu span").each( function () {
        // On stocke le contenu du span :
        var TexteSpan = $(this).text();
        $(this).replaceWith('<a href="" title="Afficher le sous-menu" class="uppercase">' + TexteSpan + '<\/a>') ;
    } ) ;
 
    // On modifie l'ï¿½vï¿½nement "click" sur les liens dans les items de liste
    // qui portent la classe "toggleSubMenu" :
    $(".navigation li.toggleSubMenu > a").click( function () {
        // Si le sous-menu ï¿½tait dï¿½jï¿½ ouvert, on le referme :
        if ($(this).next("ul.subMenu:visible").length != 0) {
            $(this).next("ul.subMenu").slideUp("normal", function () { $(this).parent().removeClass("open") });
        }
        // Si le sous-menu est cachï¿½, on ferme les autres et on l'affiche :
        else {
            $(".navigation ul.subMenu").slideUp("normal", function () { $(this).parent().removeClass("open") });
            $(this).next("ul.subMenu").slideDown("normal", function () { $(this).parent().addClass("open") });
        }
        // On empï¿½che le navigateur de suivre le lien :
        return false;
    });     
} ) ;
// -->
 
</script>  
<!--[if lte IE 8]>
<script type="text/javascript" src="/jsp/scripts/roundies.js">
</script><![endif]-->  

</head>
<body>
        
<a name="top" id="top"></a>

<div id="global"><!-- Balise fermee dans le fichier footer.jsp -->        
       
        <hr />

        <div id="bandeau">        
                
                <h1 class="hidden">UFR Science Fondamentales et Appliquées - Université de Poitiers - Universit&eacute; de Poitiers</h1>
                
                <div id="bandeauLogos">
                
                        <a href="http://www.univ-poitiers.fr" title="Aller sur le portail de l'Universit&eacute; de Poitiers"><img src="http://sfa.univ-poitiers.fr/images/logo-up.gif" alt="Logo de l'Universit&eacute; de Poitiers" /></a>
                        
                        <a href="http://sfa.univ-poitiers.fr/" title="Retour &agrave; la page d'accueil du site" accesskey="1">
                                                
                        <img src="http://sfa.univ-poitiers.fr/images/logo-composantes-sfa.gif" alt="Logo de UFR Science Fondamentales et Appliquées - Université de Poitiers" />
                                                                        
                        </a>                        
                </div> <!-- fin #bandeauLogos -->
        </div> <!-- fin #bandeau -->       
        <hr />
<div id="page">

<div id="entete">
<h1><a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=logout" ><img src="images/deco.png"></a><?php echo "$titre"; ?></h1>
</div>

<div id="menu">
<div id="pageMenu" class="navigation">
    
<?php
	if ($right != 0)
	{
?>
<!-- Affichage du menu-->

<p class="hidden">Menu principal :</p>
<ul>
    <li id="menu1">
        <span class="uppercase">
            
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=accueil">Accueil</a>  
               
        </span>   
    </li>    
    <li id="menu2" class="toggleSubMenu">
        <a class="uppercase" title="Afficher le sous-menu" href=""> Restauration </a>
        <ul id="smenu2" class="subMenu" style="display: none;">
            <li>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=restore">Effectuer une restauration</a>
            </li>
        </ul>
    </li>
    <li id="menu3" class="toggleSubMenu">
        <a class="uppercase" title="Afficher le sous-menu" href=""> Sauvegarde </a>
        <ul id="smenu3" class="subMenu" style="display: none;">
            <li>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=save">Créer une nouvelle sauvegarde</a>
            </li>
        </ul>
    </li>
    <li id="menu4" class="toggleSubMenu">
        <a class="uppercase" title="Afficher le sous-menu" href=""> Administration des postes </a>    
        <ul id="smenu4" class="subMenu" style="display: none;">
            <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=adhesion_domain">Adhésion au domaine</a></li>
            <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=actions_distance">Actions à distance</a></li>
        </ul>
    </li>    
    <li id="menu5" class="toggleSubMenu">
        <a class="uppercase" title="Afficher le sous-menu" href=""> Gestion de la base de donnée </a>
        <ul id="smenu5" class="subMenu" style="display: none;">
            <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=salles">Gérer les salles</a></li>
            <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=images">Gérer les images</a></li>
            <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=comptes_utilisateurs">Gérer les comptes utilisateurs</a></li>
            <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=domaines">Gestion des domaines</a></li>  
        </ul>
    </li>   
</ul>


<?php  } ?>
</div>
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
        Universit&eacute; de Poitiers - 15, rue de l'H&ocirc;tel Dieu - 86034 POITIERS Cedex - France - T&eacute;l : (33) (0)5 49 45 30 00 - Fax : (33) (0)5 49 45 30 50 - webmaster@univ-poitiers.fr
        - <a href="/83681377/0/fiche___pagelibre/&amp;RH=1268304565657" accesskey="8">Cr&eacute;dits et mentions l&eacute;gales</a>
    </p>
</div><!-- Fin div#pied -->



</div>
</body>
</html>
