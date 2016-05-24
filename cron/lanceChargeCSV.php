<?php

set_time_limit(360); // temps d'activité augmenté
//ini_set("memory_limit","1024M");
$startTime = microtime(true); //retourne le timestamp actuel

//inclusion de la configuration
require_once("../common/inc/_config.html");

//instance de la classe sql - appel de class
$SQL = newClass("sql"); // inclusion de la class mysql

/* import de la classe */
require_once("classes/LectureCSV.class.php");
require_once("classes/EnregistreBDD.class.php");

//appel de la classe LectureCSV.class.php /* Permet de charger les functions de la classe, un contructeur par defaut est lancé au chargement
$classCSV = new EnregistreBDD($SQL);


/*
$tab = $classCSV->getCSV("tabProduit");

echo $tab. "ok";
print_r($tab);*/

/*$req = "SELECT id_produit,nom_produit FROM produit";
		$SQL->ExecManualRequest($req);
		if($SQL->HasResult()){
			while($data = $SQL->FR()){
					$out[] = $data[0].$data[1];
			}
		}else{
			echo "pas de resultat";
		}*/

?>