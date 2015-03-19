<?php
/** 
 * Script de contrôle et d'affichage du cas d'utilisation "Modifier forfaits"
 * @package default
 * @todo  RAS
 */
  $repInclude = './include/';
  require($repInclude . "_init.inc.php");

  // page inaccessible si visiteur non connecté
  if (!estVisiteurConnecte()) {
      header("Location: cSeConnecter.php");  
  }
  require($repInclude . "_entete.inc.html");
  require($repInclude . "_sommaire.inc.php");
  
  $txtEltsForfait =  lireDonneePost("txtEltsForfait", "");
  $etape=lireDonnee("etape","demanderSaisie");
  $idForfait=lireDonnee("idForfait", "");

if ($etape == "validerSaisie") {
    // l'utilisateur valide les éléments forfaitisés         
    // vérification des quantités des éléments forfaitisés

         modifierPrixForfait($idConnexion, $txtEltsForfait);
}
  ?>

  <!-- Division principale -->
  <div id="contenu">
      <h2>Modification du prix des forfaits</h2>
      <form action="" method="post">
      <div class="corpsForm">
          <input type="hidden" name="etape" value="validerSaisie" />
          <fieldset>
            <legend>Eléments forfaitisés</legend>
      <?php          
            //requête pour obtenir la liste des forfaits et leurs prix
            $req = obtenirReqPrixForfaits();
            $idJeuEltsFraisForfait = mysql_query($req, $idConnexion);
            echo mysql_error($idConnexion);
            $lgEltForfait = mysql_fetch_assoc($idJeuEltsFraisForfait);
            while ( is_array($lgEltForfait) ) {
                $idForfait = $lgEltForfait["id"];
                $libelle = $lgEltForfait["libelle"];
                $montant = $lgEltForfait["montant"];
            ?>
            <p>
              <label for="<?php echo $idForfait ?>">* <?php echo $libelle; ?> : </label>
              <input type="text" id="<?php echo $idForfait ?>" 
                    name="txtEltsForfait[<?php echo $idForfait ?>]" 
                    size="10" maxlength="5"
                    title="Entrez le prix des forfaits" 
                    value="<?php echo $montant; ?>" />
            </p>
            <?php        
                $lgEltForfait = mysql_fetch_assoc($idJeuEltsFraisForfait);   
            }
            mysql_free_result($idJeuEltsFraisForfait);
            ?>
            
            
          </fieldset>
      </div>
      <div class="piedForm">
      <p>
<!--        <input id="ok" type="submit" value="Valider" size="20" 
               title="Enregistrer les nouvelles modification des forfaits" />-->
          
            <input name="validerSaisie" type="submit" value="Valider" size="20" 
               title="Enregistrer les nouvelles modification des forfaits" />
            
        <input id="annuler" type="reset" value="Effacer" size="20" />
      </p>
      </div>
      </form>
      <?php
//      if(isset($_POST["validerSaisie"])) {
//          echo "coucou";
//          modifierPrixForfait($idConnexion, $libelle, $montantFinal);
//      }
  	?>
<?php        
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?> 