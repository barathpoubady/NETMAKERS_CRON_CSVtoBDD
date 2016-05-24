<?php
set_time_limit(360); // temps d'activité augmenté
//ini_set("memory_limit","1024M");
$startTime = microtime(true);


require_once("../common/inc/_config.html");
$SQL = newClass("sql"); // inclusion de la class mysql

		/*$req = "SELECT id_produit,nom_produit FROM produit";
		$SQL->ExecManualRequest($req);
		if($SQL->HasResult()){
			while($data = $SQL->FR()){
					$out[] = $data[0].$data[1];
			}
		}else{
			echo "pas de resultat";
		}*/


function convert_O_Ko_Mo($size){
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}


$Recap="";
$Recap.="Récuperation du fichier de contact pour la commande N°".$_SESSION['id_commande']."<br /><br />";

/*$Produit = newClass("produit",412);	
$Produit->Init();
$Produit->SetNom($ton_tab['5']);
$Produit->SetRef($ton_tab['23']);
$Produit->Save();*/

	
	$nb_contact_add=0;
	foreach($tab_contacts as $k => $v){
		$tab_contact = explode(';',$v);
		
			// si l'email ne contien pas de @ on n'enregistre pas...
			if( !eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$",$tab_contact[5]) ){continue;}
		
			$Base = newClass("base2");
			$Base->Init();
		
			// si deja blacklist on enregistre pas...
			if( $Base->GetIsBlacklistByEmail(trim(strtolower($tab_contact[5]))) ){continue;}
			
			// si deja bounce on enregistre direct en tant que bounce...
			if( $Base->GetBounceByEmail(trim(strtolower($tab_contact[5]))) ){$Base->SetErrorbounce($Base->GetBounceByEmail(trim(strtolower($tab_contact[5]))));}
		
			$Base->SetCommande2($_SESSION['id_commande']);
			$Base->SetSociete(trim(ucfirst(strtolower($tab_contact[4]))));
	        $Base->SetPrenom(trim(ucfirst(strtolower($tab_contact[3]))));
	        $Base->SetNom(trim(ucfirst(strtolower($tab_contact[2]))));
	        $Base->SetEmail(trim(strtolower($tab_contact[5])));
			$Base->SetSource(trim(strtolower($tab_contact[0]))); // id_megabase
			
			$Base->Save();
		$nb_contact_add++;			
	}
	fclose($tab_contacts);
	$Recap.="Nombre de contacte ajouté : ".$nb_contact_add."<br />";





echo $Recap;





	//raport par mail
	$vers_mail = "s.teulie@aressy.com"; //Email du receveur
	$sujet = "[Cron] Import Netmakers Store ".$_CFG['NOM_CLIENT']; //Sujet du mail
		$headers ='From: "eCronMaster"<emailmaster@aressy.com>'."\n";
		$headers .='Reply-To: emailmaster@aressy.com'."\n";
		$headers .='Content-Type: text/html; charset="iso-8859-1"'."\n";
		$headers .='Content-Transfer-Encoding: 8bit'; 

	$message="<html><body>";
		$message.="Client : ".$_CFG['NOM_CLIENT']."<br />";
		$message.="Commande N° ".$_SESSION['id_commande']."<br />";
		$message.="<hr>";
		$message.=$Recap;
		$message.="<hr>";
		$message.="<br />Mémoire utilisé : ".convert_O_Ko_Mo(memory_get_usage())." (".convert_O_Ko_Mo(memory_get_peak_usage()).")<br />";
		
		$endTime = microtime(true);  
		$elapsed = $endTime - $startTime;
		$message.="Temps : ".$elapsed." sec."."<br />";
	$message.="</body></html>";

	mail($vers_mail, $sujet, $message, $headers, "-f emailmaster@aressy.com");//on envoi le mail de rapport

?>