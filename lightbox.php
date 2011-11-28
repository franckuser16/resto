<div id="stop" class="leightbox_top">

	<h1>Commande effectuée avec succes !</h1>

	<p> Le boot PXE a été désactivé !</p>

	<p class="footer">
	
		<a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=accueil">Ok.</a>
	</p>
</div>



<div id="restore"  class="leightbox_bottom">

	<h1>Commande effectuée avec succes !</h1>

	<p> Votre action a bien été prise en compte, les ordinateurs devraient s'allumer dans quelques secondes.</p>

	<p class="footer">
	
		<a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=accueil">Ok</a>
	</p>
</div>



<div id="save" class="leightbox_bottom">

	<h1>Commande effectuée avec succes !</h1>

	<p> A la fin de la sauvegarde, vous devez redétecter les images qui sont sur le serveur. <br>La nouvelle 
	sauvegarde créé sera ainsi ajoutée à la base de donnée.</p>

	<p class="footer">
	
		<a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=images">Ok, je le fais de suite.</a>
	</p>
</div>

<div id="auto_detect_computers" class="leightbox_top">

	<h1>Commande effectuée avec succes !</h1>

	<p> Les adresses MAC des ordinateurs ont été rajouté à la base de donnée.<br>
	Vous devez maintenant nomer chaque ordinateur.</p>

	<p class="footer">
	
		<a href="<?php echo $_SERVER['PHP_SELF']; ?>?menu=salles&action=visualisation">Ok, je le fais de suite.</a>
	</p>
</div>
