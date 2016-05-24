<?php

class EnregistreBDD extends LectureCSV{
	
	//champs
	private $oLectureCSV;
	var $nb_produit_AJOUT = 0; //Le nombre de produit ajouté
	var $nb_produit_MAJ = 0; //Le nombre de produit Mise a jour
	var $nb_produit_departement = 0; //Le nombre de departement ajouté
	var $nb_produit_marque = 0; //Le nombre de marque ajouté
	var $nb_produit_gamme = 0; //Le nombre de gamme ajouté 
	var $nb_produit_maintenance_AJOUT = 0; //Le nombre de maintenance ajouté
	var $nb_produit_maintenance_MAJ = 0; //Le nombre de maintenance Mise à jour
	var $nb_produit_option_INSERT = 0; //Le nombre de produit option ajouté
	var $nb_produit_option_MAJ = 0; //Le nombre de produit option Mise à jour 
	var $nb_produit_options_type_INSERT = 0; //Le nombre de type de options ajoutés
	var $idTypeOption = "-1";

	//var $tabNomtabCSV = array("tabProduit", "tabProduitDepartement", "tabProduitMaintenance", "tabProduitOption");
	
	//constructeur par defaut /* 1 pramettre l'objet SQL */
	function __construct($SQL){
		
		//recupere l'objet SQL
		$this->oSQL = $SQL;
		
		//Création de l'instance et appel de la methode pére (hérité par LectureCSV)
		$oLectureCSV = new LectureCSV();
		
		//on rend tous les produits indisponible
		$this->razProduit();
		
		//on rend toutes les options indisponible
		$this->razOption();
		
		//on appel la methode qui parcours les tables
		$this->recupeTabCSV($oLectureCSV);

		//on appel la methode qui Envoie l'mail
		$this->transfertEmail();
	}
	
	/* METHODE RECUPETABCSV */ //Permet de faire appel des methodes de la classe LectureCSV afin de récuperer les tables de données CSV
	function recupeTabCSV($oLectureCSV){
	
		/* GESTION DE produit.csv */
		$tabCSVproduit = ($oLectureCSV->getCSV("tabProduit"));
		
		$compteur = 0; //permet de ne pas lire la premiere ligne des CSV
		
		foreach($tabCSVproduit as $k => $v){
			
			if($compteur > 0){
				
				$this->verifProduit($v[0], $k, $tabCSVproduit); 
				//echo $v[0]."<br/>";
				//echo $k."<br />"; 
				
			}
			
			$compteur++;
			
		}//fin foreach
		
		/* GESTION DE produit_departement.csv */
		$tabCSVproduitDepartement = ($oLectureCSV->getCSV("tabProduitDepartement"));
		
		$compteur = 0; //permet de ne pas lire la premiere ligne des .CSV
		
		foreach($tabCSVproduitDepartement as $k => $v){
			
			if($compteur > 0){
				
				$this->verifDepartement($v[1], $k, $tabCSVproduitDepartement);  

			}
			
			$compteur++;
			
		}//fin foreach
		
		/* GESTION DE produit_maintenance.csv */
		$tabCSVproduitMaintenance = ($oLectureCSV->getCSV("tabProduitMaintenance")); 
		
		$compteur = 0; //permet de ne pas lire la premiere ligne des .CSV
		
		foreach($tabCSVproduitMaintenance as $k => $v){
			
			if($compteur > 0){
				
				$this->verifMaintenance($v[0], $k, $tabCSVproduitMaintenance); 

			}
			
			$compteur++;
			
		}//fin foreach
		
		
		/* GESTION DE produit_options.csv */
		$tabCSVproduitOption = ($oLectureCSV->getCSV("tabProduitOption"));
		
		$compteur = 0; //permet de ne pas lire la premiere ligne des .CSV
		
		foreach($tabCSVproduitOption as $k => $v){
			
			if($compteur > 0){
				
				$this->verifOption($v[0], $v[1], $v[5], $k, $tabCSVproduitOption); 

			}
			
			$compteur++;
			
		}//fin foreach
		
		
	}
	
/*************************************************************************************************\
|********************************* LES METHODES DE RAZ DE LA BDD *********************************|
\*************************************************************************************************/
	/* Les methodes qui suit permettent de mettre le champ dispo_produit a 0 pour qu'elle ne saffiche plus sur le site */
	function razProduit(){
		
		$req = "SELECT id_produit FROM produit ";
		$this->oSQL->ExecManualRequest($req);
		if($this->oSQL->HasResult()){
			while($data = $this->oSQL->FR()){

				echo $data[0];
				
				//UPDATE DES DONNEES EN BASE
				$Produit = newClass("produit",$data[0]);	
				$Produit->Init();
				$Produit->SetDispo("0");
				
				//sauvegarde
			    $Produit->Save();
			}
			
		}
		
		
	}//fin methode
	
	
	/* La methode qui suit permettent de mettre le champ dispo_option a 0 pour qu'elle ne saffiche plus sur le site */
	function razOption(){
		
		$req = "SELECT id_produit_options FROM produit_options "; 
		$this->oSQL->ExecManualRequest($req);
		if($this->oSQL->HasResult()){
			while($data = $this->oSQL->FR()){

				echo $data[0];
				
				//UPDATE DES DONNEES EN BASE
				$ProduitOption = newClass("produit_options",$data[0]);	
				$ProduitOption->Init();
				$ProduitOption->SetDispo("0");
				
				//sauvegarde
			    $ProduitOption->Save();
			}
			
		}
		
		
	}//fin methode
	
	
/*************************************************************************************************\
|********************************* LES METHODES DE VERIFICATIONS *********************************|
\*************************************************************************************************/

	/* FUNCTION VERIFIE LES PRODUITS SI ILS EXISTENT OU PAS
	*@params : $valeurRef //L'identifiant unique du produit
	*@params : $k //La clé de la ligne du tableau qui est lu
	*@params : $tabProduit //Le tableau qui contient tous les element du csv
	*/
	function verifProduit($valeurRef, $k, $tabProduit){
		
		$bOuiPourInsert = 0;//varible type boolean local à la function

		$req = "SELECT ref_produit, id_produit FROM produit";
		$this->oSQL->ExecManualRequest($req);
		if($this->oSQL->HasResult()){
			while($data = $this->oSQL->FR()){
					
				echo $valeurRef." | ";
				
				if($valeurRef === $data[0]){
					
					//Appel de la methode majPrduit 
					$this->majProduit($tabProduit, $data[1], $k);
					//boolean qui passe à 1 si le produit existe deja
					$bOuiPourInsert = 1;
					echo "<br />";
					return;//quitte le Tantque
					
				}else{
					echo "rien <br />";
					// si bOuiPourInsert est égale a 0 alors on insert une nouvel ligne
					$bOuiPourInsert = 0; 
				}

			}
			
			echo "<br />";
			
		}else{
			echo "pas de resultat"; 
		}
		
		/* // APPEL DE LA METHODE insertProduit \\ */
		if($bOuiPourInsert == 0){
			
			//appel de la methode qui ajoute un nouveau produit 
			$this->insertProduit($tabProduit, $k);
			
		}

	}//fin function


	/* FUNCTION VERIFIE LES DEPARTEMENT 
	*@params : $valeurDep //Le code postale du departement
	*@params : $k //La clé de la ligne du tableau qui est lu
	*@params : $tabProduit //Le tableau qui contient tous les element du csv
	*/
	function verifDepartement($valeurDep, $k, $tabDepartement){

		$req = "SELECT numero_produit_departement, id_produit_departement FROM produit_departement";
		$this->oSQL->ExecManualRequest($req);
		if($this->oSQL->HasResult()){
			while($data = $this->oSQL->FR()){
					
				echo $valeurDep." | ";
				
				if($valeurDep === $data[0]){
					
					//Appel de la methode majPrduit 
					$this->majDepartement($tabDepartement, $data[1], $k);
					echo "<br />";
					return;//quitte le Tantque
					
				}else{
					echo "rien <br />";
					
				}

			}
			
			echo "<br />";

		}else{
			echo "pas de resultat"; 
		}

	}//fin function


	/* FUNCTION VERIFIE LES PRODUITS_MAINTENANCE SI ILS EXISTENT OU PAS ET RECUPERE L'ID PRODUIT
	*@params : $valeurRef //L'identifiant unique du produit
	*@params : $k //La clé de la ligne du tableau qui est lu
	*@params : $tabProduit //Le tableau qui contient tous les element du csv
	*/
	function verifMaintenance($valeurRef, $k, $tabProduitMaintenance){
		
		$bOuiPourInsert = 0;//varible type boolean local à la function

		$req = "SELECT ref_produit, id_produit FROM produit";
		$this->oSQL->ExecManualRequest($req);
		if($this->oSQL->HasResult()){
			while($data = $this->oSQL->FR()){
					
				echo $valeurRef." | ";
			
				if($valeurRef === $data[0]){
					
					$req2 = "SELECT id_produit_maintenance, maintenance_netmakers_produit_maintenance, id_produit FROM produit_maintenance";
					$this->oSQL->ExecManualRequest($req2);
					if($this->oSQL->HasResult()){
						while($data2 = $this->oSQL->FR()){
							
							echo("-->".$data2[1]."<-- <br/>");
							
							$testMaintenance = -1;
							
							//MAJ MAINTENANCE_NM
							if($tabProduitMaintenance[$k][1] === "N"){
								$testMaintenance = 0;
							}elseif($tabProduitMaintenance[$k][1] === "O"){
								$testMaintenance = 1;
							}
							
							if($data[1] == $data2[2] && $data2[1] == $testMaintenance){ 
								
								//Appel de la methode majProduitMaintenance
								$this->majProduitMaintenance($tabProduitMaintenance, $data2[0], $data2[1], $k);
								//boolean qui passe à 1 si le produit existe deja
								$bOuiPourInsert = 1;
								echo "<br />";
								return;//quitte le Tantque 
								
							}
													
						}//fin while n°2
						
					}

				}//fin if

			}//fin while n°1
			
			echo "<br />";

			
		}else{
			echo "pas de resultat"; 
		}
		
		/* // APPEL DE LA METHODE insertProduit \\ */
		if($bOuiPourInsert == 0){
			
			//appel de la methode qui ajoute une nouvelle maintenance
			$this->insertMaintenance($tabProduitMaintenance, $k); 
			
		}

	}//fin function
	
	/* FUNCTION VERIFIE LES OPTIONS SI ILS EXISTENT OU PAS
	*@params : $valeurProduitRef //Le numero de reference du produit
	*@params : $valeurType //Le texte de l'option type
	*@params : $valeurOptionRef //Le texte de l'option type
	*@params : $k //La clé de la ligne du tableau qui est lu
	*@params : $tabOption //Le tableau qui contient tous les element du csv
	*/
	function verifOption($valeurProduitRef, $valeurType, $valeurOptionRef, $k, $tabOption){
		
		$bOuiPourInsert = 0;//varible type boolean local à la function 
		
		//appel de la methode qui ajoute ou pas une categorie dans la table produit_option_type
		$this->verfAjouteTypeOption($valeurType);
		//recupere l'id de la categorie type_options
		$this->recupeIdProduitOption($valeurType);

		$req = "SELECT ref_produit_produit_options, id_produit_options_type, ref_option_produit_options, id_produit_options FROM produit_options";
		$this->oSQL->ExecManualRequest($req);
		if($this->oSQL->HasResult()){
			while($data = $this->oSQL->FR()){
				
				//on recupere l'id option 
				$idTypeOption = $this->idTypeOption;
				
				echo "-->".$data[0]." | ID_OPTION_PRODUIT_REF -- ".$idTypeOption." ";
				
				if($valeurProduitRef === $data[0] && $idTypeOption === $data[1] && $valeurOptionRef === $data[2]){
					echo "<br/>OKOKOK<br />";
					//Appel de la methode majOption 
					$this->majOption($tabOption, $this->idTypeOption, $data[3], $k);
					//boolean qui passe à 1 si le produit existe deja
					$bOuiPourInsert = 1;
					
					return;//quitte le Tantque
					
				}else{
					echo "rien <br />";
					// si bOuiPourInsert est égale a 0 alors on insert une nouvel ligne
					$bOuiPourInsert = 0;
				}

			}
			
		}else{
			echo "pas de resultat"; 
		}
		
		/* // APPEL DE LA METHODE insertProduit \\ */
		if($bOuiPourInsert == 0){
			
			//appel de la methode qui ajoute une nouvelle options 
			$this->insertOption($tabOption, $this->idTypeOption, $k);
			echo "<br />";  
			
		}

	}//fin function


/*************************************************************************************************\
|******************************** LES METHODES DE MISE A JOUR ************************************|
\*************************************************************************************************/
	
	/* METHODES MAJ DES PRODUITS  //Permet de MAJ les produits de la table produit, elle prend en paramettre la tableCSVproduit et l'id du produit a update et la clé $k,
	*
	*	@params: $tabProduit //la tableCSVproduit
	*	@params: $tabProduit //l'id du produit a update 
	*	@params: $k //la clé $k
	*/
	
	function majProduit($tabProduit, $id, $k){
		
		echo $tabProduit[$k][0]." | ".$tabProduit[$k][1]." | ".$tabProduit[$k][2]." | ".$tabProduit[$k][3]."<br/>";

		//UPDATE DES DONNEES EN BASE
		$Produit = newClass("produit",$id);	
		$Produit->Init();
		
		//AJOUTE l'URL
		$Produit->SetUrl_visuel($tabProduit[$k][1]);
		//AJOUT PROMO OU PAS
		if($tabProduit[$k][2] === "N"){
			$Produit->SetPromo("0");
		}elseif($tabProduit[$k][2] === "O"){
			$Produit->SetPromo("1");
		}
		//APPEL DE LA METHODE verfAjouteMarque
		$this->verfAjouteMarque($tabProduit[$k][3]);
		$this->verfAjouteGamme($tabProduit[$k][4]);
		//AJOUTE L'ID DE LA MARQUE ET LA GAMME
		$Produit->SetProduit_marque($this->recupeIdMarque($tabProduit[$k][3]));
		$Produit->SetProduit_gamme($this->recupeIdGamme($tabProduit[$k][4]));
		//AJOUTE NB OU PAS
		if($tabProduit[$k][5] === "N"){
			$Produit->SetNb("0");
		}elseif($tabProduit[$k][5] === "O"){ 
			$Produit->SetNb("1");
		}
		//AJOUTE COULEURS OU PAS
		if($tabProduit[$k][6] === "N"){
			$Produit->SetCouleur("0");
		}elseif($tabProduit[$k][6] === "O"){
			$Produit->SetCouleur("1");
		}
		//AJOUTE FORMAT A4
		if($tabProduit[$k][7] === "N"){
			$Produit->SetA4("0");
		}elseif($tabProduit[$k][7] === "O"){
			$Produit->SetA4("1");
		}
		//AJOUTE FORMAT A3
		if($tabProduit[$k][8] === "N"){
			$Produit->SetA3("0");
		}elseif($tabProduit[$k][8] === "O"){
			$Produit->SetA3("1");
		}
		//AJOUTE VITESSE NB
		$Produit->SetVitesse_nb($tabProduit[$k][9]);
		//AJOUTE MAC
		if($tabProduit[$k][10] === "N"){
			$Produit->SetMac("0");
		}elseif($tabProduit[$k][10] === "O"){
			$Produit->SetMac("1");
		}
		//AJOUTE PC
		if($tabProduit[$k][11] === "N"){
			$Produit->SetPc("0");
		}elseif($tabProduit[$k][11] === "O"){
			$Produit->SetPc("1");
		}
		//AJOUTE PC
		$Produit->SetNom($tabProduit[$k][12]);
		//AJOUTE DESCRIPTION
		$Produit->SetDescription($tabProduit[$k][13]);
		//AJOUTE LA DIMENSION
		$Produit->SetDimensions($tabProduit[$k][14]);
		//AJOUTE LE POIDS DU PRODUIT
		$Produit->SetPoids($tabProduit[$k][15]);
		//AJOUTE LA RESOLUTION
		$Produit->SetResolution($tabProduit[$k][16]);
		//AJOUTE VITESSE COULEUR
		$Produit->SetVitesse_couleur($tabProduit[$k][17]); 
		//RECTO VERSO OU PAS
		if($tabProduit[$k][18] === "N"){
			$Produit->SetRectoverso("0");
		}elseif($tabProduit[$k][18] === "O"){
			$Produit->SetRectoverso("1");
		}
		//INTERFACE
		$Produit->SetInterface($tabProduit[$k][19]);
		//CPU / PROCESSEUR
		$Produit->SetCpu($tabProduit[$k][20]);
		//RAM
		$Produit->SetRam($tabProduit[$k][21]);
		//LANGUE
		$Produit->SetLang($tabProduit[$k][22]);
		//CAPACITE FEUILLE
		$Produit->SetCapacite($tabProduit[$k][23]);
		//PRIX PRODUIT
		$Produit->SetPrix(str_replace(" ", "", $tabProduit[$k][24]));
		//PRIX PROMO PRODUIT
		$Produit->SetPromo_prix($tabProduit[$k][25]);
		
		//ON REND DISPO LE PRODUIT
		$Produit->SetDispo("1");
		$Produit->Save();
		
		//Incremente le nb de produit MAJ
		$this->nb_produit_MAJ++;
		
	}//fin function



	/* METHODES MAJ DES DEPARTEMENTS  //Permet de MAJ les departements de la table produit_departement, 
	*
	*	@params: $tabProduit //la tableCSVdepartement
	*	@params: $tabProduit //l'id du produit a update 
	*	@params: $k //la clé $k
	*/
	function majDepartement($tabDepartement, $id, $k){
		
		echo $tabDepartement[$k][0]." | ".$tabDepartement[$k][1]." | ".$tabDepartement[$k][2]."<br/>";

		//UPDATE DES DONNEES EN BASE // prend en paramettre la table et l'id du produit en BDD
		$produitDepartement = newClass("produit_departement", $id);	
		$produitDepartement->Init();
		
		//AJOUT PROMO OU PAS
		if($tabDepartement[$k][2] === "N"){
			$produitDepartement->SetMaintenance("0");
		}elseif($tabDepartement[$k][2] === "O"){
			$produitDepartement->SetMaintenance("1");
		}
		
		// SAUVEGARDE
		$produitDepartement->Save();
		
		//Incremente le nb de departement MAJ
		$this->nb_produit_departement++;

	}//fin function
	
	
	/* METHODES MAJ DES MAINTENANCE  //Permet de MAJ les maintenance produit de la table produit_maintenance, 
	*
	*	@params: $tabProduitMaintenance //la tableCSVdepartement
	*	@params: $id //l'id du produit 
	*	@params: $k //la clé $k
	*/
	function majProduitMaintenance($tabProduitMaintenance, $id_maintenance, $maintenance_NO_YES, $k){
		
		echo $tabProduitMaintenance[$k][0]." | ".$tabProduitMaintenance[$k][1]." | ".$tabProduitMaintenance[$k][2]."<br/>";
		
				//on verifie si l'id_CSV du produit est egale à l'id_produit_bdd_maintenance et si maintenance_N_O egale à 1
				if($maintenance_NO_YES == 1){
					
					//UPDATE DES DONNEES EN BASE // prend en paramettre la table et l'id du produit en BDD
					$produitMaintenance = newClass("produit_maintenance", $id_maintenance);	
					$produitMaintenance->Init();
					
					//MAJ MAINTENANCE_NM
					if($tabProduitMaintenance[$k][1] === "N"){
						$produitMaintenance->SetMaintenance_netmakers("0");
					}elseif($tabProduitMaintenance[$k][1] === "O"){
						$produitMaintenance->SetMaintenance_netmakers("1"); 
					}
					//MAJ LE PRIX COULEUR
					$produitMaintenance->SetPrix_couleur($tabProduitMaintenance[$k][2]);
					//MAJ LE PRIX NB
					$produitMaintenance->SetPrix_nb($tabProduitMaintenance[$k][3]);
					//MAJ DE L'ASSURANCE
					$produitMaintenance->SetAssurance_service($tabProduitMaintenance[$k][6]);
					//MAJ DE LA LIVRAISON
					$produitMaintenance->SetLivraison($tabProduitMaintenance[$k][8]);
					//MAJ DU DEPLOIEMENT
					$produitMaintenance->SetDeploiement($tabProduitMaintenance[$k][9]);
					//MAJ DU SOUS_TRAITANT
					$produitMaintenance->SetSous_traitant($tabProduitMaintenance[$k][10]);
					
					// SAUVEGARDE
					$produitMaintenance->Save();
					
					//Incremente le nb de maintenance MAJ
					$this->nb_produit_maintenance_MAJ++;
					
				}elseif($maintenance_NO_YES == 0){
					
					//UPDATE DES DONNEES EN BASE // prend en paramettre la table et l'id du produit en BDD
					$produitMaintenance = newClass("produit_maintenance", $id_maintenance);	
					$produitMaintenance->Init();
					
					//MAJ MAINTENANCE_NM
					if($tabProduitMaintenance[$k][1] === "N"){
						$produitMaintenance->SetMaintenance_netmakers("0");
					}elseif($tabProduitMaintenance[$k][1] === "O"){
						$produitMaintenance->SetMaintenance_netmakers("1");  
					}
					//MAJ LE PRIX COULEUR
					$produitMaintenance->SetPrix_couleur($tabProduitMaintenance[$k][2]);
					//MAJ LE PRIX NB
					$produitMaintenance->SetPrix_nb($tabProduitMaintenance[$k][3]);
					//MAJ DE L'ASSURANCE
					$produitMaintenance->SetAssurance_service($tabProduitMaintenance[$k][6]);
					//MAJ DE LA LIVRAISON
					$produitMaintenance->SetLivraison($tabProduitMaintenance[$k][8]);
					//MAJ DU DEPLOIEMENT
					$produitMaintenance->SetDeploiement($tabProduitMaintenance[$k][9]);
					//MAJ DU SOUS_TRAITANT
					$produitMaintenance->SetSous_traitant($tabProduitMaintenance[$k][10]);
					
					// SAUVEGARDE
					$produitMaintenance->Save();
					
					//Incremente le nb de maintenance MAJ
					$this->nb_produit_maintenance_MAJ++;
					
				}else{

					//on fait rien

				}
		
			
		
	}//fin function
	
	
	/* METHODES MAJ DES OPTIONS  //Permet de MAJ les options de la table produit_options, elle prend en paramettre la tableCSVoptions et l'id du option a update et la clé $k,
	*
	*	@params: $tabOption //la tableCSVoption
	*	@params: $tabProduit //l'id du option a update 
	*	@params: $k //la clé $k
	*/
	function majOption($tabOption, $idProduitOptionType, $id, $k){
		
		echo $tabOption[$k][0]." | ".$tabOption[$k][1]." | ".$tabOption[$k][2]." | ".$tabOption[$k][3]."<br/>";

		//UPDATE DES DONNEES EN BASE
		$Option = newClass("produit_options",$id);	
		$Option->Init();
		
		//AJOUTE DE LA REF PRODUIT
		$Option->SetRef_produit($tabOption[$k][0]);
		
		//Appel de la methode qui recupere l'id du produit
		$idProduit = $this->recupeIdProduit($tabOption[$k][0]);
		if($idProduit == "erreurRefProduit"){
			$this->transfertEmailErreur("La reférence du produit de la table produit_option n'existe pas. Voir ligne ".$k."du CSV");
			exit();
			
		}
		//AJOUT DE L'ID PRODUIT
		$Option->SetProduit($idProduit);
		
		//AJOUT DE LA CATEGORIE
		$Option->SetProduit_options_type($idProduitOptionType);
		
		//AJOUT DU PRIX
		$Option->SetPrix($tabOption[$k][2]);
		
		//AJOUT DU NOM
		$Option->SetNom(filter_var($tabOption[$k][3], FILTER_SANITIZE_STRING));
		
		//AJOUT DE LA DESCRIPTION
		$Option->SetDescription(filter_var($tabOption[$k][4], FILTER_SANITIZE_STRING));
		
		//ON REND L'OPTION DISPONIBLE
		$Option->SetDispo("1");
		$Option->Save();
		
		//Incremente le nb de produit option MAJ
		$this->nb_produit_option_MAJ++;
		
	}//fin function

	

/*************************************************************************************************\
|********************************* LES METHODES D'INSERTION **************************************|
\*************************************************************************************************/	
	
	/* METHODES INSERT DES PRODUITS */ //Permet d'ajouter les produits de la table produit, elle prend en paramettre la tableCSVproduit et l'id du produit a update et la clé $k
	function insertProduit($tabProduit, $k){
		
		echo $tabProduit[$k][0]." | ".$tabProduit[$k][1]." | ".$tabProduit[$k][2]." | ".$tabProduit[$k][3]."<br/>";

		$Produit = newClass("produit");	
		$Produit->Init();
		//AJOUTE REFERENCE
		$Produit->SetRef($tabProduit[$k][0]);
		//AJOUTE l'URL
		$Produit->SetUrl_visuel($tabProduit[$k][1]);
		//AJOUT PROMO OU PAS
		if($tabProduit[$k][2] === "N"){
			$Produit->SetPromo("0");
		}elseif($tabProduit[$k][2] === "O"){
			$Produit->SetPromo("1");
		}
		//APPEL DE LA METHODE verfAjouteMarque
		$this->verfAjouteMarque($tabProduit[$k][3]);
		$this->verfAjouteGamme($tabProduit[$k][4]);
		//AJOUTE L'ID DE LA MARQUE ET LA GAMME
		$Produit->SetProduit_marque($this->recupeIdMarque($tabProduit[$k][3]));
		$Produit->SetProduit_gamme($this->recupeIdGamme($tabProduit[$k][4]));
		//AJOUTE NB OU PAS
		if($tabProduit[$k][5] === "N"){
			$Produit->SetNb("0");
		}elseif($tabProduit[$k][5] === "O"){
			$Produit->SetNb("1");
		}
		//AJOUTE COULEURS OU PAS
		if($tabProduit[$k][6] === "N"){
			$Produit->SetCouleur("0");
		}elseif($tabProduit[$k][6] === "O"){
			$Produit->SetCouleur("1");
		}
		//AJOUTE FORMAT A4
		if($tabProduit[$k][7] === "N"){
			$Produit->SetA4("0");
		}elseif($tabProduit[$k][7] === "O"){
			$Produit->SetA4("1");
		}
		//AJOUTE FORMAT A3
		if($tabProduit[$k][8] === "N"){
			$Produit->SetA3("0");
		}elseif($tabProduit[$k][8] === "O"){
			$Produit->SetA3("1");
		}
		//AJOUTE VITESSE NB
		$Produit->SetVitesse_nb($tabProduit[$k][9]);
		//AJOUTE MAC
		if($tabProduit[$k][10] === "N"){
			$Produit->SetMac("0");
		}elseif($tabProduit[$k][10] === "O"){
			$Produit->SetMac("1");
		}
		//AJOUTE PC
		if($tabProduit[$k][11] === "N"){
			$Produit->SetPc("0");
		}elseif($tabProduit[$k][11] === "O"){
			$Produit->SetPc("1");
		}
		//AJOUTE PC
		$Produit->SetNom($tabProduit[$k][12]);
		//AJOUTE DESCRIPTION
		$Produit->SetDescription($tabProduit[$k][13]);
		//AJOUTE LA DIMENSION
		$Produit->SetDimensions($tabProduit[$k][14]);
		//AJOUTE LE POIDS DU PRODUIT
		$Produit->SetPoids($tabProduit[$k][15]);
		//AJOUTE LA RESOLUTION
		$Produit->SetResolution($tabProduit[$k][16]);
		//AJOUTE VITESSE COULEUR
		$Produit->SetVitesse_couleur($tabProduit[$k][17]); 
		//RECTO VERSO OU PAS
		if($tabProduit[$k][18] === "N"){
			$Produit->SetRectoverso("0");
		}elseif($tabProduit[$k][18] === "O"){
			$Produit->SetRectoverso("1");
		}
		//INTERFACE
		$Produit->SetInterface($tabProduit[$k][19]);
		//CPU / PROCESSEUR
		$Produit->SetCpu($tabProduit[$k][20]);
		//RAM
		$Produit->SetRam($tabProduit[$k][21]);
		//LANGUE
		$Produit->SetLang($tabProduit[$k][22]);
		//CAPACITE FEUILLE
		$Produit->SetCapacite($tabProduit[$k][23]);
		//PRIX PRODUIT
		$Produit->SetPrix(str_replace(" ", "", $tabProduit[$k][24]));
		//PRIX PROMO PRODUIT
		$Produit->SetPromo_prix($tabProduit[$k][25]);
		
		//ON REND DISPO LE PRODUIT
		$Produit->SetDispo("1");
		$Produit->Save();
		
		//incremente le nb de produit ajouté
		$this->nb_produit_AJOUT++;	
		
	}//fin function

	//METHODE D'INSERTION MAINTENANCE
	/*
	*@Param: $tabProduitMaintenance //les données du CSV
	*@Param: $k //la clé de la ligne à lire
	*
	*/
	function insertMaintenance($tabProduitMaintenance, $k){
		
		$req = "SELECT ref_produit, id_produit FROM produit";
		$this->oSQL->ExecManualRequest($req);
		if($this->oSQL->HasResult()){
			while($data = $this->oSQL->FR()){
				//on verifie si le produit existe et on reupere l'id du produit grace la reference
				if($tabProduitMaintenance[$k][0] == $data[0]){
				
					//AJOUT DES DONNEES EN BASE // prend en paramettre le nom de la table_BDD
					$produitMaintenance = newClass("produit_maintenance");	
					$produitMaintenance->Init();
					
					//AJOUT L'ID_PRODUIT
					$produitMaintenance->SetProduit($data[1]); 
					
					//MAJ MAINTENANCE_NM
					if($tabProduitMaintenance[$k][1] === "N"){
						$produitMaintenance->SetMaintenance_netmakers("0");
					}elseif($tabProduitMaintenance[$k][1] === "O"){
						$produitMaintenance->SetMaintenance_netmakers("1"); 
					}
					//MAJ LE PRIX COULEUR
					$produitMaintenance->SetPrix_couleur($tabProduitMaintenance[$k][2]);
					//MAJ LE PRIX NB
					$produitMaintenance->SetPrix_nb($tabProduitMaintenance[$k][3]);
					//MAJ DE L'ASSURANCE
					$produitMaintenance->SetAssurance_service($tabProduitMaintenance[$k][6]);
					//MAJ DE LA LIVRAISON
					$produitMaintenance->SetLivraison($tabProduitMaintenance[$k][8]);
					//MAJ DU DEPLOIEMENT
					$produitMaintenance->SetDeploiement($tabProduitMaintenance[$k][9]);
					//MAJ DU SOUS_TRAITANT
					$produitMaintenance->SetSous_traitant($tabProduitMaintenance[$k][10]);
					
					// SAUVEGARDE
					$produitMaintenance->Save();
					
					//Incremente le nb de maintenance MAJ
					$this->nb_produit_maintenance_AJOUT++;
					
				}
							
			}
		}
		
	}//fin function
	
	
	/* METHODES INSERT DES OPTIONS  //Permet de INSERER les options de la table produit_options, elle prend en paramettre la tableCSVoptions et l'id du option a update et la clé $k,
	*
	*	@params: $tabOption //la tableCSVoption
	*	@params: $k //la clé $k
	*/
	function insertOption($tabOption, $idProduitOptionType, $k){
		
		echo $tabOption[$k][0]." | ".$tabOption[$k][1]." | ".$tabOption[$k][2]." | ".$tabOption[$k][3]."<br/>";

		//UPDATE DES DONNEES EN BASE
		$Option = newClass("produit_options");	
		$Option->Init();
		
		//AJOUTE DE LA REF PRODUIT
		$Option->SetRef_produit($tabOption[$k][0]);
		
		//Appel de la methode qui recupere l'id du produit
		$idProduit = $this->recupeIdProduit($tabOption[$k][0]);
		if($idProduit == "erreurRefProduit"){
			$this->transfertEmailErreur("La reférence du produit de la table produit_option n'existe pas. Ligne ".$k."du CSV");
			exit();
			
		}
		
		//AJOUT DE L'ID PRODUIT
		$Option->SetProduit($idProduit);
		
		//AJOUT DE LA CATEGORIE
		$Option->SetProduit_options_type($idProduitOptionType);
		
		//AJOUT DU PRIX
		$Option->SetPrix($tabOption[$k][2]);
		
		//AJOUT DU NOM
		$Option->SetNom(filter_var($tabOption[$k][3], FILTER_SANITIZE_STRING));
		
		//AJOUT DE LA DESCRIPTION
		$Option->SetDescription(filter_var($tabOption[$k][4], FILTER_SANITIZE_STRING));
		
		//AJOUT DE LA REFERENCE OPTION
		$Option->SetRef_option($tabOption[$k][5]);
		
		//ON REND L'OPTION DISPONIBLE
		$Option->SetDispo("1");
		$Option->Save();
		
		//Incremente le nb de produit option INSERT
		$this->nb_produit_option_INSERT++;
		
	}//fin function
	
	
	


/*********************** LES METHODES POUR LES TABLES MARQUE ET GAMME ***********************************/

	/* METHODE VERIFE et AJOUTE MARQUE PRODUIT */
	function verfAjouteMarque($valeurMarque){
		
		$bOuiPourInsert = 0;//varible type boolean local à la function
		
		$req = "SELECT nom_produit_marque FROM produit_marque";
		$this->oSQL->ExecManualRequest($req);
		if($this->oSQL->HasResult()){
			while($data = $this->oSQL->FR()){
					
				echo $valeurMarque." | ";
				
				/* si la valeur existe */
				if(strtolower($valeurMarque) === strtolower($data[0])){
					
					/* ON QUITTE LA BOUCLE */
					$bOuiPourInsert = 1;
					return;
					
				}else{
					
					$bOuiPourInsert = 0;//varible type boolean est egale à 0 donc on ajoute une marque

				}

			}
			
			/* ajoute la nouvelle marque */
			if($bOuiPourInsert == 0){
			
				$ProduitMarque = newClass("produit_marque");	
				$ProduitMarque->Init();
				$ProduitMarque->SetNom($valeurMarque);
				$ProduitMarque->Save();
				
				//Incremente le nb de marque ajoutée
				$this->nb_produit_marque++;
			}
			
		}else{
			echo "pas de resultat de la BDD ";  
		}
		
	}//fin function

	/* METHODE RECUPE ID MARQUE */
	function recupeIdMarque($valeurMarque){

		
		$req = "SELECT id_produit_marque FROM produit_marque WHERE nom_produit_marque ='".$valeurMarque."'";
		$this->oSQL->ExecManualRequest($req);
		if($this->oSQL->HasResult()){
			while($data = $this->oSQL->FR()){
					
				return $data[0];

			}
			
		}else{
			echo "pas de resultat de la BDD  MARQUE"; 
		}
		
		
	}//fin function
	
	
	/* METHODE VERIFE et AJOUTE GAMME PRODUIT */
	function verfAjouteGamme($valeurGamme){
		
		$bOuiPourInsert = 0;//varible type boolean local à la function
		
		$req = "SELECT nom_produit_gamme FROM produit_gamme";
		$this->oSQL->ExecManualRequest($req);
		if($this->oSQL->HasResult()){
			while($data = $this->oSQL->FR()){
					
				echo $valeurGamme." | ";
				
				if(strtolower($valeurGamme) === strtolower($data[0])){
					
					/* ON QUITTE LA BOUCLE */
					$bOuiPourInsert = 1;
					return;
					
				}else{
					
					$bOuiPourInsert = 0;//varible type boolean local à la function

				}

			}
			
			/* ajoute la nouvelle gamme */
			if($bOuiPourInsert == 0){
			
				$ProduitGamme = newClass("produit_gamme");	
				$ProduitGamme->Init();
				$ProduitGamme->SetNom($valeurGamme);
				$ProduitGamme->Save();
				
				//increment le nb de gamme ajoutée
				$this->nb_produit_gamme++;
			}
					

		}else{
			echo "pas de resultat de la BDD "; 
		}
		
	}//fin function
	
	/* METHODE RECUPE ID GAMME */
	function recupeIdGamme($valeurGamme){
		
		$req = "SELECT id_produit_gamme FROM produit_gamme WHERE nom_produit_gamme ='".$valeurGamme."'";
		$this->oSQL->ExecManualRequest($req);
		if($this->oSQL->HasResult()){
			while($data = $this->oSQL->FR()){
					
				return $data[0];

			}
			
		}else{
			echo "pas de resultat de la BDD"; 
		}
		
		
	}//fin function
	
	
/*************** FUNCTION QUI RECUPERE l'ID PRODUIT ********************/

/* METHODE RECUPE ID PRODUIT */
	function recupeIdProduit($refProduit){
		
		$req_id_produit = "SELECT id_produit FROM produit WHERE ref_produit ='".$refProduit."'";
		$this->oSQL->ExecManualRequest($req_id_produit);
		if($this->oSQL->HasResult()){
			while($data_id_produit = $this->oSQL->FR()){
				
				$valeurRetour = $data_id_produit[0];
				return $valeurRetour;

			}
			
		}else{
			echo "pas de resultat de la BDD";
			return "erreurRefProduit"; 
		}
		
		
	}//fin function
	
	
/*************** FUNCTION QUI RECUPERE l'ID PRODUIT_OPTION ET VERIFIE SI L'OPTION TYPE EXISTE OU PAS ********************/

/* METHODE VERIFE et AJOUTE TYPE PRODUIT */
	function verfAjouteTypeOption($valeurTypeOption){
		
		$bOuiPourInsertOption = 0;//varible type boolean local à la function
		
		$req_type_option = "SELECT nom_produit_options_type FROM produit_options_type";
		$this->oSQL->ExecManualRequest($req_type_option);
		if($this->oSQL->HasResult()){
			while($data_verife_type = $this->oSQL->FR()){	
				
				if(strtolower($valeurTypeOption) === strtolower($data_verife_type[0])){

					/* ON QUITTE LA BOUCLE */
					$bOuiPourInsertOption = 1;
					return;
					
				}else{
					
					$bOuiPourInsertOption = 0;//variable type boolean local à la function

				}

			}//fin while
			
			/* ajoute la nouvelle gamme */
			if($bOuiPourInsertOption == 0){
			
				$ProduitOptionType = newClass("produit_options_type");	
				$ProduitOptionType->Init();
				$ProduitOptionType->SetNom($valeurTypeOption);
				$ProduitOptionType->Save();
				
				//increment le nb de type option ajoutée
				$this->nb_produit_options_type_INSERT++;
			}
					

		}else{
			echo "pas de resultat de la BDD "; 
		}
		
	}//fin function

/* METHODE RECUPE ID PRODUIT_OPTION */
	function recupeIdProduitOption($nomProduitOption){
		
		$req_id_option = "SELECT id_produit_options_type FROM produit_options_type WHERE nom_produit_options_type ='".$nomProduitOption."'";
		$this->oSQL->ExecManualRequest($req_id_option);
		if($this->oSQL->HasResult()){
			while($data_id_option = $this->oSQL->FR()){
					
				$this->idTypeOption = $data_id_option[0];

			}
			
		}else{
			echo "pas de resultat de la BDD"; 
		}
		
		
	}//fin function
	
	


/*************** FUNCTION QUI ENVOIE L'EMAIL AU CLIENT ********************/
 function transfertEmail(){
   
    //ENVOIE DE L'EMAIL 
	//String qui contient les infos pour le mail CRON
	$Recap = "Voici les informations des importations - Netmakers STORE <br /><br />"; 
	
	$Recap .= "Date : ".date("Y-m-d")."<br />";
	$Recap .=  "Heure :".date("H:i:s")."<br /><br />";
	
	$Recap .="Nombre de produit ajouté : ".$this->nb_produit_AJOUT."<br />";
	$Recap .="Nombre de produit Mise à jour : ".$this->nb_produit_MAJ."<br />";
	$Recap .="Nombre de département Mise à jour : ".$this->nb_produit_departement."<br />";
	$Recap .="Nombre de marque ajoutée : ".$this->nb_produit_marque."<br />";
	$Recap .="Nombre de marque gamme : ".$this->nb_produit_gamme."<br />";
	$Recap .="Nombre de maintenance Mise à jour : ".$this->nb_produit_maintenance_MAJ."<br />";
	$Recap .="Nombre de maintenance ajoutée : ".$this->nb_produit_maintenance_AJOUT."<br />";
	$Recap .="Nombre de option Mise à jour : ".$this->nb_produit_option_MAJ."<br />";
	$Recap .="Nombre de option ajoutée : ".$this->nb_produit_option_INSERT."<br />";  
	$Recap .="Nombre de Type d'option ajoutée : ".$this->nb_produit_options_type_INSERT."<br />";

	echo $Recap;

	// Email du receveur
	$vers_mail = "b.poubady@aressy.com". ', '; 
	$vers_mail .= "l.darrigade@aressy.com". ', '; 
    $vers_mail .= "s.teulie@aressy.com". ',';
	$vers_mail .= "y.couder@aressy.com";
	//$vers_mail .= "b.poubady@aressy.com";

	
	$sujet = "[Cron] Import Netmakers Store "; //Sujet du mail
	
		$headers ='From: "eCronMaster"<emailmaster@aressy.com>'."\n";
		$headers .='Reply-To: emailmaster@aressy.com'."\n";
		$headers .='Content-Type: text/html; charset="iso-8859-1"'."\n";
		$headers .='Content-Transfer-Encoding: 8bit'; 
 
	    $message="<html><body>";
		
		$message.="<hr>";
		$message.=$Recap;
		$message.="<hr>";
		
	    $message.="</body></html>";
	
	
	
	//APPEL DE LA METHODE MAIL 
	mail($vers_mail, $sujet, $message, $headers, "-f emailmaster@aressy.com");//on envoi le mail de rapport
	
	
 }
	
/******************** MAIL ERREUR ********************/
function transfertEmailErreur($messageErreur){
   
    //ENVOIE DE L'EMAIL 
	//String qui contient les infos pour le mail CRON
	$Recap = "Erreur Script - Netmakers STORE <br /><br />"; 
	
	$Recap .= "Date : ".date("Y-m-d")."<br />";
	$Recap .=  "Heure :".date("H:i:s")."<br /><br />";
	
	$Recap .="MESSAGE : ".$messageErreur;

	echo $Recap;

	// Email du receveur
	$vers_mail = "b.poubady@aressy.com". ', '; 
	$vers_mail .= "l.darrigade@aressy.com". ', '; 
  $vers_mail .= "s.teulie@aressy.com". ',';
	$vers_mail .= "y.couder@aressy.com";
	//$vers_mail .= "b.poubady@aressy.com";

	
	$sujet = "[ErreurCron] Import Netmakers Store "; //Sujet du mail
	
		$headers ='From: "eCronMaster"<emailmaster@aressy.com>'."\n";
		$headers .='Reply-To: emailmaster@aressy.com'."\n";
		$headers .='Content-Type: text/html; charset="iso-8859-1"'."\n";
		$headers .='Content-Transfer-Encoding: 8bit'; 
 
	    $message="<html><body>";
		
		$message.="<hr>";
		$message.=$Recap;
		$message.="<hr>";
		
	    $message.="</body></html>";
	
	
	
	//APPEL DE LA METHODE MAIL 
	mail($vers_mail, $sujet, $message, $headers, "-f emailmaster@aressy.com");//on envoi le mail de rapport
	
	
 }


}//fin classe


?>




















