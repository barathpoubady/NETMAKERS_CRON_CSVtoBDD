<?php

class LectureCSV {
	
	//champs
	private $tabProduit;
	private $tabProduit_dep;
	private $tabProduit_maint;
	private $tabProduit_opt;
	
	
	//constructeur par defaut
	function __construct(){

		$this->lectureCSV('http://www.netmakers.fr/store/TICNMART.csv', 'TICNMART.csv');
		$this->lectureCSV('http://www.netmakers.fr/store/TICNMDPT.csv', 'TICNMDPT.csv');
		$this->lectureCSV('http://www.netmakers.fr/store/TICNMMAINT.csv', 'TICNMMAINT.csv');
		$this->lectureCSV('http://www.netmakers.fr/store/TICNMOPTION.csv', 'TICNMOPTION.csv');
		
	}
	
	
	//function parcourir repertoire imports 
	/*function parcourirCSV(){
		
		$dirname = 'http://www.netmakers.fr/store/';
		$dir = opendir($dirname);
		
		while($file = readdir($dir)) {
			
			if($file != '.' && $file != '..' && !is_dir($dirname.$file)){
				
				echo '<br /><br />[[ <a href="'.$dirname.$file.'">'.$file.'</a> ]]'.'<br /><br />';
				
				/* appel de la methode lectureCSV */ // Qui permet de la lecture des fichier CSV
	/*			$this->lectureCSV($dirname.$file,$file);
			}
		}
		
		closedir($dir);
		
	}*/
	
	

	//function lecture CSV // elle de parcourir le fichier CSV et ensuite d'enregistrer les données dans le bon tableau 
	function lectureCSV($cheminCSV, $nomFichier){
		
		//compteur
		$row = 1;
		
		/* condition qui ouvre en lecture seul et test si le fichier existe */
		if (($handle = fopen($cheminCSV, "r")) !== FALSE) {
			
			while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
				$num = count($data);
				//echo "<table> $num champs à la ligne $row: <br />";
				$row++;
				for ($c=0; $c < $num; $c++) {
					//echo $data[$c] . "<br />";
				//Enregistre la table dans une variable de class tableTempo /* permet de stoker les donnée lu dans le csv
			 		 //$this->tableTempo[$row][$c] = $data[$c];
					 $tableTempo[$row][$c] = $data[$c]; //traiterChaine()
			  
				}

			}
			fclose($handle);
			
			$this->creationTable($nomFichier,$tableTempo);
		}
		
	}//fin function
	
	
	//Function qui nettoie les textes
	function traiterChaine($chaine) {
		$chaine = str_replace('\r\n', '', $chaine);
		$chaine = str_replace('\r', '', $chaine);
		$chaine = str_replace('\n', '', $chaine);
		return $chaine;
	}
	
	
	/* FUNCTION CREATIONTALBE */ // Permet de cree une table pour chaque CSV en récupérant la table temproraire
	function creationTable($nomFichier, $tableTempo){
		
		//Switch qui permet d'attribuer chaque CSV en rapport avec son nom dans un tableau 
		switch($nomFichier){
			
			/* Si c'est Option */
			case 'TICNMOPTION.csv';
			
				$tabProduit_opt = $tableTempo;//récupere la table tempo qui contient les donnée du .csv
				$tabLenght = count($tabProduit_opt);
				echo 'TABLE : produit_options OK <br />';
				echo "<table border='1'> <tr> <td>";
				echo " TAILLE DE LA TABLE :  ". $tabLenght. "  <br />";
				echo "</td></tr>";
				
				foreach($tabProduit_opt as $k => $v){
					
					//$tabProduit = explode(';',$v);
						echo "<tr> <td>";
						
						echo $v[5];
						
						echo "</td></tr>";
				
					}
					
				echo "</table><br/><br/>";
				
				/* enregistre la table dans l'attribut tabProduit_opt */ //Permet de recupérer la table avec la methode getCSV
				$this->tabProduit_opt = $tabProduit_opt;
				
				break;
			
			/* Si c'est produit */
			case 'TICNMART.csv';
				
				$tabProduit = $tableTempo;
				$tabLenght = count($tabProduit);
				echo 'TABLE : Produits OK <br />';
				echo "<table border='1'> <tr> <td>";
				echo " TAILLE DE LA TABLE :  ". $tabLenght. "  <br />";
				echo "</td></tr>";
				
				foreach($tabProduit as $k => $v){
					
						echo "<tr> <td>";
						
						echo $v[0];
						
						echo "</td></tr>";
				
					}
					
			    echo "</table><br/><br/>";
				
				/* enregistre la table dans l'attribut tabProduit */ //Permet de recupérer la table avec la methode getCSV
				$this->tabProduit = $tabProduit;
				
				break;
				
			/* Si c'est maintenance */
			case 'TICNMMAINT.csv';
				
				$tabProduit_maint = $tableTempo;
				$tabLenght = count($tabProduit_maint);
				echo 'TABLE : produit_maintenance OK <br />';
				echo "<table border='1'> <tr> <td>";
				echo " TAILLE DE LA TABLE :  ". $tabLenght. "  <br />";
				echo "</td></tr>";
				
				foreach($tabProduit_maint as $k => $v){
					
						echo "<tr> <td>";
						
						echo $v[0];
						
						echo "</td></tr>";
				
					}
					
			    echo "</table><br/><br/>";
				
				/* enregistre la table dans l'attribut tabProduit_maint */ //Permet de recupérer la table avec la methode getCSV
				$this->tabProduit_maint = $tabProduit_maint;
				
				break;
			
			/* si c'est les departement promo */
			case 'TICNMDPT.csv';
				
				$tabProduit_dep = $tableTempo;
				$tabLenght = count($tabProduit_dep);
				echo 'TABLE : produit_departement OK <br />';
				echo "<table border='1'> <tr> <td>";
				echo " TAILLE DE LA TABLE :  ". $tabLenght. "  <br />";
				echo "</td></tr>";
				
				foreach($tabProduit_dep as $k => $v){
					
						echo "<tr> <td>";
						
						echo $v[0];
						
						echo "</td></tr>";
				
					}
				
					
			    echo "</table><br/><br/>";
				
				/* enregistre la table dans l'attribut tabProduit_dep */ //Permet de recupérer la table avec la methode getCSV
				$this->tabProduit_dep = $tabProduit_dep;
				
				break;
				
			default;
				echo 'Aucun CSV';
			break;
			
		}
	
	}//fin function
	
	
	/* METHODE GET et SET*/
	/*function get qui prend en paramettre une chaine de charactére */ // Elle permet de retourner le tableau correspondant à la chaine entré en paramettre
	function getCSV($nomTableCSV){
		
		switch($nomTableCSV){
			
			case 'tabProduit';

				return $this->tabProduit;
			break;
			
			case 'tabProduitDepartement';
			
				return $this->tabProduit_dep;
			break;
			
			case 'tabProduitMaintenance';
			
				return $this->tabProduit_maint;
			break;
			
			case 'tabProduitOption';
			
				return $this->tabProduit_opt;
			break;
				
			default;
				echo 'Aucune Table ne correspond';
			break;
		}

	}//fin function
	
}


?>




















