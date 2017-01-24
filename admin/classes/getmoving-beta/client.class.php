<?php
/*
 * Fil som konsulteres av de fleste filene i /admin/classes/ 
 * for uregelrette handlinger.
 * 
 * Alle funksjonene i denne filen er nødvendige, med unntak 
 * av save_column(), som bare kalles om den finnes.
 * 
 * For eksempel på hvordan en standardfil skal se ut, kontakt jonatan@norbye.com
*/

if(!class_exists("Client")){
class Client{
    function __construct($data){
        $this->data = $data;
        
        $this->error = array();
    }
	
    /*
     * Viser kolonner (database-kolonne) som er uregelrette.
     * Returnerer false om den aktuelle kolonnen ikke er uregelrett.
     * Returnerer true om den aktuelle kolonnen er uregelrett.
    */
	function display_column(array $data){
		$required_cols = array('tbl', 'column_name');
		foreach($required_cols as $rq){if(!isset($data[$rq])){ return false; }}
        
        /*
         * Eksempel på hvordan $data arrayen som sendes av info.class.php ser ut
         *   array(
         *       'ID' => $ID,
         *       'tbl' => $tbl,
         *       'tbl_index' => $tbl_index,
         *       'column_name' => $column_name,
         *       'column_value' => $info[$column_name],
         *       'info' => $info
         *   );
         */
        
        //Avoid header printing responsibilities
        if(isset($data['header']) && $data['header']){
            //Den aktuelle klienten har ingen kolonner hvor tittelen behandles uregelmessig, så return false
            return false;
        }
        
        //Lagre verdien av name attributten for å gjøre senere kode kortere
        $name = $data['tbl'].'-'.$data['tbl_index'].'-'.$data['column_name'];
		
		
		return false;
	}
    
    /*
     * Endre database-spørringen om nødvendig.
     * Returner false om spørringen ikke er uregelrett.
     * Returner spørringen om den er uregelrett.
    */
    function custom_query(array $data){
        
        return false;
    }
    
    /*
     * Vis sider som bare finnes i den aktuelle klienten.
     * Returnerer aldri noe.
     *
     * Sjekker om de nødvendige delene av variabelen $i_a er satt, 
     * og viderefører handlingen til en class om alt stemmer
    */
    function display_page(array $i_a){
        
    }
    
    /*
     * Utfør spesielle handlinger når noe lagres.
     * F.eks.: fjern ; fra starten og slutten til en RFID.
     * Returnerer verdien av feltet.
    */
    function save_column(array $data){
        
        return $data['column_value'];
    }
}
}