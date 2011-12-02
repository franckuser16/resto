<div id="auth">
    <br><br><br>
    <h2>Veuillez vous identifier</h2>
    
    <br>
    <form id="authentification" action="<?php echo $_SERVER['PHP_SELF']; ?>?menu=access" method="post" >
        <div>
            <h3><label for="nom">Nom</label></h3>
            <input type="text" title="nom de l'utilisateur" name="login" maxlength="25" value="" id="nom">
        </div>
        <div>
            <h3><label for="Mdp">Mot de passe</label></h3>
            <input type="password" name="pass"  maxlength="20" value="" id="Mdp">
        </div>
        <p>          
            <h3><label>&nbsp</label></h3>
            <input classe="gauche" type="submit" name="action" value="Authentification" id="bouton_valid">
        </p>
    </form>

</div>
