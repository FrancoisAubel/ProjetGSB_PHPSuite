  <?php
  $repInclude = './include/';
  require($repInclude . "_init.inc.php");

  // page inaccessible si visiteur non connecté
  if (!estVisiteurConnecte()) {
      header("Location: cSeConnecter.php");  
  }
  else {
      $idUser = obtenirIdUserConnecte();
      $lgUser = obtenirDetailVisiteur($idConnexion, $idUser);
      $type = $lgUser['typeVisiteur'];
      if ($type != 1) {
          header("Location: cAccueil.php");
      }
  }
  require($repInclude . "_entete.inc.html");
  require($repInclude . "_sommaire.inc.php");
?>

<?php
	if(isset($_POST['listeVisiteurs'])){
		$listeVisiteurs=$_POST['listeVisiteurs'];
	}else{
	$listeVisiteurs=-1;
	}
?>
	<div id="contenu">
            <h2>Choix et modification d'un utilisateur</h2>
            
            <form name ="formVisiteurs" method="post" action="">
				<select name = "listeVisiteurs" onchange=" formVisiteurs.submit();">
						<option value = -1>-- Choisissez un utilisateur -- </option>
        <?php
                $connection = mysql_connect('localhost', 'root', 'root');
                $base = mysql_select_db('gsb_frais');

                $requete = "SELECT nom FROM visiteur";
                $execution_requete = mysql_query($requete);
                while($total = mysql_fetch_array($execution_requete))

                //Liste déroulante
                {
        ?>
                            <option value='<?php echo $total["nom"];
                                    if($listeVisiteurs == $total['nom']) { echo "selected"; }
                                    ?>'>
                                    <?php echo $total['nom']; ?> 
                            </option>
        <?php	
                }
    ?>
                    </select>
            </form>
    <?php
            if($listeVisiteurs != -1){ 
                    $requete = "SELECT id, nom, prenom, cp, adresse, ville, mdp FROM visiteur WHERE nom='".$listeVisiteurs."'";
                    $execution_requete = mysql_query($requete);
                    $total = mysql_fetch_array($execution_requete);	
    ?>
            <form method="post" action="">
                    <h2>Informations</h2>
                    Nom :
                    <input type="text" name="nom" value="<?php echo $total['nom'] ?>" size="20" ><br/><br/>
                    Prénom :
                    <input type="text" name="prenom" value="<?php echo $total['prenom'] ?>" size="20" ><br/><br/>
                    Adresse :
                    <input type="text" name="adresse" value="<?php echo $total['adresse'] ?>" size="35" ><br/><br/>
                    Code Postal : 
                    <input type="text" name="cp" value="<?php echo $total['cp'] ?>" size="20" ><br/><br/>
                    Ville : 
                    <input type="text" name="ville" value="<?php echo $total['ville'] ?>" size="20" ><br/><br/>
                    Mot de passe : 
                    <input type="passowrd" name="mdp" value="<?php echo $total['mdp'] ?>" size="20" ><br/><br/>

                    <input type="submit" name="maj" value="Mettre à jour">
                    <input type="hidden" name="id" value="<?php echo $total['id'] ?>">
            </form>


    <?php
    } 

            if(isset($_POST['maj'])){
                    $id = $_POST["id"];
                    $result = mysql_query("UPDATE visiteur SET nom = '" . $_POST['nom'] . "' , prenom = '" . $_POST['prenom'] . "', adresse = '" . $_POST['adresse'] . "', 
                    cp = '" . $_POST['cp'] . "', ville = '" . $_POST['ville'] .  "', mdp = '" . $_POST['mdp'] . "' WHERE id = '" . $id . "' LIMIT 1");

                    if (!$result) {
                            echo "Les modifications ont échouées !<br>";
                    } else {
                            echo "Modifications prises en compte !<br>";
                            
                    }
            }
    ?>
			<h2>Ajout d'un utilisateur</h2>
	 
			<form method="post" action="">
				Id :
				<input type="text" name="id" size="10" required="required" value="<?php if(isset($_POST['id'])) { echo $_POST['id']; }?>" ><br/><br/>
				Nom :
				<input type="text" name="nom" size="20" required="required" value="<?php if(isset($_POST['nom'])) { echo $_POST['nom']; }?>" ><br/><br/>
				Prénom :
				<input type="text" name="prenom" size="20"  required="required" value="<?php if(isset($_POST['prenom'])) { echo $_POST['prenom']; }?>" ><br/><br/>
				Login : 
				<input type="text" name="login" size="15"  required="required" value="<?php if(isset($_POST['login'])) { echo $_POST['login']; }?>" ><br/><br/>
				Mot de passe : 
				<input type="text" name="mdp" size="15"  required="required" value="<?php if(isset($_POST['mdp'])) { echo $_POST['mdp']; }?>" ><br/><br/>
				Adresse :
				<input type="text" name="adresse" size="35"  required="required" value="<?php if(isset($_POST['adresse'])) { echo $_POST['adresse']; }?>" ><br/><br/>
				Code Postal : 
				<input type="text" name="cp"  size="20" maxlength="5"  required="required" value="<?php if(isset($_POST['cp'])) { echo $_POST['cp']; }?>" ><br/><br/>
				Ville : 
				<input type="text" name="ville" size="20"  required="required" value="<?php if(isset($_POST['ville'])) { echo $_POST['ville']; }?>" ><br/><br/>
				Date embauche (YYYY-MM-DD) : 
				<input type="text" name="dateemb" size="10"  required="required" value="<?php if(isset($_POST['dateemb'])) { echo $_POST['dateemb']; }?>" ><br/><br/>
				Comptable : 
				<input type="checkbox" name="typevisiteur" value="1" value="<?php if(isset($_POST['typevisiteur'])) { echo $_POST['typevisiteur']; }?>" ><br/><br/>

				<input type="submit" name="add" value="Ajouter">
			</form>
	<?php 
			$type = 0;
			if(isset($_POST['add'])){
					
					$query = "INSERT INTO visiteur VALUES (";
					$query .= "'" .$_POST['id'].			"',";
					$query .= "'" .$_POST['nom'].			"',";
					$query .= "'" .$_POST['prenom'].		"',";
					$query .= "'" .$_POST['login'].			"',";
					$query .= "'" .$_POST['mdp'].			"',";
					$query .= "'" .$_POST['adresse'].		"',";
					$query .= "'" .$_POST['cp'].			"',";
					$query .= "'" .$_POST['ville'].			"',";
					$query .= "'" .$_POST['dateemb'].		"',";
					if(isset($_POST['typevisiteur'])) {
						$query .= $_POST['typevisiteur'];					
					}
					else {
						$query  .= $type; 
					}
					$query .= ")";
					
					$execute_query = mysql_query($query);
					
					
					
                    if (!$execute_query) {
                            echo "<font color='red'>  Le client " .$_POST['nom']. " n'a pas été ajouté à la base de données !</font><br>";
							echo "PierreSQL =  " . $query;
                    } else {
                            echo "<font color='green'>  Le client " .$_POST['nom']. " a été ajouté à la base de données !</font><br>";    
                    }
            }
			
	?>
	</div>
	
<?php        
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?> 