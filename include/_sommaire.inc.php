<?php
/** 
 * Contient la division pour le sommaire, sujet à des variations suivant la 
 * connexion ou non d'un utilisateur, et dans l'avenir, suivant le type de cet utilisateur 
 * @todo  RAS
 */

?>
    <!-- Division pour le sommaire -->
    <div id="menuGauche">
     <div id="infosUtil">
    <?php      
      if (estVisiteurConnecte() ) {
          $idUser = obtenirIdUserConnecte() ;
          $lgUser = obtenirDetailVisiteur($idConnexion, $idUser);
          $nom = $lgUser['nom'];
          $prenom = $lgUser['prenom'];            
    ?>
        <h2>
    <?php  
            echo $nom . " " . $prenom ;
    ?>
        </h2>
         
         
    <?php
    $idUser = obtenirIdUserConnecte() ;
    $lgUser = obtenirDetailVisiteur($idConnexion, $idUser);
    $type = $lgUser['typeVisiteur'];
    if($type == 1){
    ?>
        <h3>Comptable</h3>
    <?php
        }
    ?>
         
    <?php
       }
    ?>  
      </div>  
<?php      
  if (estVisiteurConnecte() ) {
?>
        <ul id="menuList">
           <li class="smenu">
              <a href="cAccueil.php" title="Page d'accueil">Accueil</a>
           </li>
           <li class="smenu">
              <a href="cSeDeconnecter.php" title="Se déconnecter">Se déconnecter</a>
           </li>
           <li class="smenu">
              <a href="cSaisieFicheFrais.php" title="Saisie fiche de frais du mois courant">Saisie fiche de frais</a>
           </li>
           <li class="smenu">
              <a href="cConsultFichesFrais.php" title="Consultation de mes fiches de frais">Mes fiches de frais</a>
           </li>
           <li class="smenu">
               <a href="cModifForfaits.php" title="Modification des forfaits">Modification forfaits</a>
           </li>
         </ul>

    <?php
    $idUser = obtenirIdUserConnecte() ;
    $lgUser = obtenirDetailVisiteur($idConnexion, $idUser);
    $type = $lgUser['typeVisiteur'];
    if($type == 1){
    ?>
        <ul id="menuList">
            <li class="smenu">
                <a href="cValidFichesFrais.php" title="Page d'accueil">Valider fiche de frais</a>
            </li>
            <li>
                <a href="cModifierVisiteur.php" title="Modifier visiteurs">Modifier informations visiteurs</a>
            </li>
        </ul>
    <?php
        }
     ?>
        <?php
          // affichage des éventuelles erreurs déjà détectées
          if ( nbErreurs($tabErreurs) > 0 ) {
              echo toStringErreurs($tabErreurs) ;
          }
  }
        ?>
    </div>
    