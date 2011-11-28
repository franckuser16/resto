<h1> Bienvenue sur Restonux ! </h1>

<hr>
<p> Ceci est une interface web du serveur DRBL. <a href="http://clonezilla.org/">http://clonezilla.org/</a> <br></p>

<form id="formulaire" action="<?php echo "".$_SERVER['PHP_SELF']."?menu=".$menu.""; ?>" method="post" >

<p>Si vous souhaitez couper le mode de restauration cliquez ici : 

<input type="submit" name="stop_pxe" value="Arreter"></p> 
</form>

<p>Vous ne pourrez plus démarrer en PXE à partir des clients.</p>
<p>Pour redémarrer le mode de restauration, il suffit de lancer une restauration ou une sauvegarde. </p>
<hr>

<?php

if (isset($_POST['stop_pxe']))
{
	shell_exec("sudo /opt/drbl/sbin/dcs -nl clonezilla-stop > /dev/null 2>&1 &");

	include_once("lightbox.php"); // Contient les cadres qui sont affichés

	start_lightbox("stop"); // On démarre la boite de dialogue de succes 

}


?>
