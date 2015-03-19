<?php
/** 
 * Script de contrôle et d'affichage du cas d'utilisation "Valider fiche de frais"
 * @package default
 * @todo  RAS
 */
  $repInclude = './include/';
  require($repInclude . "_init.inc.php");

  // page inaccessible si visiteur non connecté et non comptable
  if (!estVisiteurConnecte()) {
      header("Location: cSeConnecter.php");  
  }
  else {
      $idUser = obtenirIdUserConnecte() ;
      $lgUser = obtenirDetailVisiteur($idConnexion, $idUser);
      $type = $lgUser['typeVisiteur'];
      if ($type != 1) {
          header("Location: cAccueil.php");
      }
  }
  require($repInclude . "_entete.inc.html");
  require($repInclude . "_sommaire.inc.php");
  
  // affectation du mois passé pour la saisie des fiches de frais
  $mois = sprintf("%04d%02d", date("Y"), date("m"));
  
  // acquisition des données entrées
  $moisSaisi=lireDonneePost("lstMois", "");
  $moisReq=lireDonnee("moisReq", "");
  $idVisiteurAVoir=lireDonneePost("lstVisiteurs", "");
  $idVisiteurValid=lireDonnee("idVisiteurValid", "");
  $montantValide=lireDonnee("montantValide", "0");
  // acquisition de l'étape du traitement
  $etape=lireDonnee("etape","demanderSaisie");
  // acquisition des quantités des éléments forfaitisés
  $tabQteEltsForfait=lireDonneePost("txtEltsForfait", "");
  // acquisition des données d'une nouvelle ligne hors forfait
  $idLigneHF = lireDonnee("idLigneHF", "");
  $dateHF = lireDonnee("txtDateHF", "");
  $libelleHF = lireDonnee("txtLibelleHF", "");
  $montantHF = lireDonnee("txtMontantHF", "");
  // initialisation d'existe pour eviter un bug
  $existe = 0;
  
  // structure de décision sur les différentes étapes du cas d'utilisation
  if ($etape == "validerSaisie") {
      // l'utilisateur valide les éléments forfaitisés         
      // vérification des quantités des éléments forfaitisés
      $ok = verifierEntiersPositifs($tabQteEltsForfait);      
      if (!$ok) {
          ajouterErreur($tabErreurs, "Chaque quantité doit être renseignée et numérique positive.");
      }
      else { // mise à jour des quantités des éléments forfaitisés
          modifierEltsForfait($idConnexion, $moisReq, $idVisiteurValid,$tabQteEltsForfait);
      }
  }
  elseif ($etape == "validerRefuserLigneHF") { // refuser une ligne hors forfait
      refuserHorsForfaitFicheFrais($idConnexion, $idLigneHF);
  }
  elseif ($etape == "validerReporterLigneHF") { // reporter une ligne hors forfait
      reporterHorsForfaitFicheFrais($idConnexion, $idLigneHF);
  }
  elseif ($etape == "validerValid") { // Validation de la fiche de frais
      validerFicheFrais($idConnexion, $moisReq, $idVisiteurValid, $montantValide);
  }
  else { // on ne fait rien, étape non prévue 
  }
?>
  <!-- Division principale -->
  <div id="contenu">
      <h2>Valider les fiches de frais</h2>
      
      <h3>Fiche à visualiser : </h3>
      <form action="" method="post">
      <div class="corpsForm">
          <input type="hidden" name="etape" value="validerConsult" />
      <p>
        <label for="lstVisiteurs">Fiches des visiteurs à valider : </label>
        <select id="lstVisiteurs" name="lstVisiteurs" title="Sélectionnez le visiteur souhaité pour la fiche de frais">
            <?php
                // on propose tous les visiteurs qui ont une fiche de frais
                $req = obtenirReqVisiteursFicheFrais();
                $idJeuVisiteurs = mysql_query($req, $idConnexion);
                $lgVisiteurs = mysql_fetch_assoc($idJeuVisiteurs);
                while ( is_array($lgVisiteurs ) ) {
                    $nomVisiteur = $lgVisiteurs["nomVisiteur"];
                    $prenomVisiteur = $lgVisiteurs["prenomVisiteur"];
                    $idVisiteur = $lgVisiteurs["idVisiteur"];
            ?>    
            <option value="<?php echo $idVisiteur; ?>"><?php echo $nomVisiteur, " ", $prenomVisiteur; ?></option>
            <?php
                    $lgVisiteurs = mysql_fetch_assoc($idJeuVisiteurs);
                } 
                mysql_free_result($idJeuVisiteurs);
            ?>
        </select>
        
        <label for="lstMois">Mois : </label>
        <select id="lstMois" name="lstMois" title="Sélectionnez le mois souhaité pour la fiche de frais">
            <?php
                // on propose tous les mois pour lesquels les visiteur ont une fiche de frais à valider
                $req = obtenirReqAllMoisFicheFrais();
                $idJeuMois = mysql_query($req, $idConnexion);
                $lgMois = mysql_fetch_assoc($idJeuMois);
                while ( is_array($lgMois) ) {
                    $mois = $lgMois["mois"];
                    $noMois = intval(substr($mois, 4, 2));
                    $annee = intval(substr($mois, 0, 4));
                    ?>
                    <option value="<?php echo $mois; ?>"<?php if ($moisSaisi == $mois) { ?> selected="selected"<?php } ?>><?php echo obtenirLibelleMois($noMois) . " " . $annee; ?></option>
                    <?php
                    $lgMois = mysql_fetch_assoc($idJeuMois);        
                }
                mysql_free_result($idJeuMois);
            ?>
        </select>
      </p>
      </div>
      <div class="piedForm">
      <p>
        <input id="ok" type="submit" value="Consulter" size="20"
               title="Demandez à consulter cette fiche de frais" />
      </p>
      </div>
        
      </form>
      
      <?php
      // test des erreurs des données entrées
  if ($etape == "validerSaisie" || $etape == "validerAjoutLigneHF" 
          || $etape == "validerRefuserLigneHF" || $etape == "validerValid") {
      if (nbErreurs($tabErreurs) > 0) {
          echo toStringErreurs($tabErreurs);
      }
      else {
?>  
      <p class="info">Les modifications de la fiche de frais ont bien été enregistrées</p>        
<?php
      }
  }
   if ($etape == "validerConsult") {
       // demande de la requête pour obtenir la liste des éléments
       // forfaitisés du visiteur pour le mois demandé
       $req = obtenirReqEltsForfaitFicheFrais($moisSaisi, $idVisiteurAVoir);
       $idJeuEltsFraisForfait = mysql_query($req, $idConnexion);
       $existe = mysql_num_rows($idJeuEltsFraisForfait);
       // test si la requete trouve une fiche pour ce visiteur
       if ( $existe > 0) {
            ?>
            <form action="" method="post">
            <div class="corpsForm">
                <input type="hidden" name="etape" value="validerSaisie" />
                <input type="hidden" name="idVisiteurValid" value="<?php echo $idVisiteurAVoir;?>" />
                <input type="hidden" name="moisReq" value="<?php echo $moisSaisi;?>" />
                <fieldset>
                  <legend>Eléments forfaitisés
                  </legend>
            <?php
            
            echo mysql_error($idConnexion);
            $lgEltForfait = mysql_fetch_assoc($idJeuEltsFraisForfait);
            while ( is_array($lgEltForfait) ) {
                $idFraisForfait = $lgEltForfait["idFraisForfait"];
                $libelle = $lgEltForfait["libelle"];
                $quantite = $lgEltForfait["quantite"];
            ?>
            <p>
              <label for="<?php echo $idFraisForfait ?>">* <?php echo $libelle; ?> : </label>
              <input type="text" id="<?php echo $idFraisForfait ?>"
                    name="txtEltsForfait[<?php echo $idFraisForfait ?>]"
                    size="10" maxlength="5"
                    title="Entrez la quantité de l'élément forfaitisé"
                    value="<?php echo $quantite; ?>" />
            </p>
            <?php
                // attribution du montant à chaque passage
                $montantValide = $montantValide + $quantite;
            
                $lgEltForfait = mysql_fetch_assoc($idJeuEltsFraisForfait);
            }
            mysql_free_result($idJeuEltsFraisForfait);
            ?>
          </fieldset>
          <!-- On passe la montant validé en hidden -->
          <input type="hidden" name="montantValide" value="<?php echo $montantValide ?>" />
      </div>
      <div class="piedForm">
      <p>
        <input id="ok" type="submit" value="Modifier" size="20" 
               title="Enregistrer les nouvelles valeurs des éléments forfaitisés" />
      </p>
      </div>
        
      </form>
  	<table class="listeLegere">
  	   <caption>Descriptif des éléments hors forfait
       </caption>
             <tr>
                <th class="date">Date</th>
                <th class="libelle">Libellé</th>
                <th class="montant">Montant</th>
                <th class="action">&nbsp;</th>
             </tr>
<?php
          // demande de la requête pour obtenir la liste des éléments hors
          // forfait du visiteur sélectionné
          $req = obtenirReqEltsHorsForfaitFicheFrais($moisSaisi, $idVisiteurAVoir);
          $idJeuEltsHorsForfait = mysql_query($req, $idConnexion);
          $lgEltHorsForfait = mysql_fetch_assoc($idJeuEltsHorsForfait);
          
          // parcours des frais hors forfait du visiteur sélectionné
          while ( is_array($lgEltHorsForfait) ) {
          ?>
              <tr>
                <td><?php echo $lgEltHorsForfait["date"] ; ?></td>
                <td><?php echo filtrerChainePourNavig($lgEltHorsForfait["libelle"]) ; ?></td>
                <td><?php echo $lgEltHorsForfait["montant"] ; ?></td>
                <?php 
                // si la fiche n'a pas encore été refusée ou reportée, la proposer au refus et au report
                if ($lgEltHorsForfait["refuse"] == "0") {
                    // attribution du montant à chaque passage pour les fiches acceptées
                    $montantValide = $montantValide + $lgEltHorsForfait["montant"];
                    ?>
                    <td><a href="?etape=validerRefuserLigneHF&amp;idLigneHF=<?php echo $lgEltHorsForfait["id"]; ?>"
                       onclick="return confirm('Voulez-vous vraiment refuser cette ligne de frais hors forfait ?');"
                       title="refuser la ligne de frais hors forfait">refuser</a>
                       <a href="?etape=validerReporterLigneHF&amp;idLigneHF=<?php echo $lgEltHorsForfait["id"]; ?>"
                       onclick="return confirm('Voulez-vous vraiment reporter cette ligne de frais hors forfait ?');"
                       title="reporter la ligne de frais hors forfait"> reporter</a>
                    </td>
                    <input type="hidden" name="montantValide" value="<?php echo $montantValide ?>" />
                    <?php 
                }
              elseif ($lgEltHorsForfait["refuse"] == "1"){ // si la fiche a été refusée
                  ?>
                  <td class="tdAModifCouleur">REFUSE</td>
              <?php
              }
              else { // si la fiche a été reportée
                  ?>
                  <td class="tdAModifCouleur">REPORTE</td>
              <?php
              }
?>
              </tr>
          <?php
          
                
                
              $lgEltHorsForfait = mysql_fetch_assoc($idJeuEltsHorsForfait);
          }
          mysql_free_result($idJeuEltsHorsForfait);
?>
    </table><?php 
   
   }
   else { // Si aucune fiche n'a été trouvée pour le visiteur, affiche message
       echo "Pas de fiche de frais pour ce visiteur ce mois";
   }
   } // deuxième formulaire qui gère la validation
   if ($etape == "validerSaisie" || $etape == "validerConsult") { 
       if ($existe > 0) {?>
   <form action="" method="post">
      <div class="corpsForm">
          <!-- On passe des variales en hidden pour obtenir le mois et l'id saisis -->
          <input type="hidden" name="etape" value="validerValid" />
          <input type="hidden" name="idVisiteurValid" value="<?php echo $idVisiteurAVoir;?>" />
          <input type="hidden" name="moisReq" value="<?php echo $moisSaisi;?>" />
          <input type="hidden" name="montantValide" value="<?php echo $montantValide ?>" />
   
        </div>
        <div class="piedForm">
            <p> <!-- Affichage d'un message de validation -->
                <input id="ok" type="submit" value="valider la fiche de frais" size="20" 
                    title="Valider la fiche de frais" 
                    onclick="return confirm('Voulez-vous vraiment valider cette fiche de frais ?');"/>
            </p>
      </div>
    </form>
<?php
   }
   }
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?>