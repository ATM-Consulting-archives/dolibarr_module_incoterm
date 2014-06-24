<?php
/* Copyright (C) 2005-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2011 Regis Houssin        <regis.houssin@capnetworks.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *  \file       htdocs/core/triggers/interface_90_all_Demo.class.php
 *  \ingroup    core
 *  \brief      Fichier de demo de personalisation des actions du workflow
 *  \remarks    Son propre fichier d'actions peut etre cree par recopie de celui-ci:
 *              - Le nom du fichier doit etre: interface_99_modMymodule_Mytrigger.class.php
 *				                           ou: interface_99_all_Mytrigger.class.php
 *              - Le fichier doit rester stocke dans core/triggers
 *              - Le nom de la classe doit etre InterfaceMytrigger
 *              - Le nom de la methode constructeur doit etre InterfaceMytrigger
 *              - Le nom de la propriete name doit etre Mytrigger
 */


/**
 *  Class of triggers for Mantis module
 */
 
class InterfaceIncotermWorkflow
{
    var $db;
    
    /**
     *   Constructor
     *
     *   @param		DoliDB		$db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
    
        $this->name = preg_replace('/^Interface/i','',get_class($this));
        $this->family = "ATM";
        $this->description = "Trigger du module incoterm";
        $this->version = 'dolibarr';            // 'development', 'experimental', 'dolibarr' or version
        $this->picto = 'technic';
    }
    
    
    /**
     *   Return name of trigger file
     *
     *   @return     string      Name of trigger file
     */
    function getName()
    {
        return $this->name;
    }
    
    /**
     *   Return description of trigger file
     *
     *   @return     string      Description of trigger file
     */
    function getDesc()
    {
        return $this->description;
    }

    /**
     *   Return version of trigger file
     *
     *   @return     string      Version of trigger file
     */
    function getVersion()
    {
        global $langs;
        $langs->load("admin");

        if ($this->version == 'development') return $langs->trans("Development");
        elseif ($this->version == 'experimental') return $langs->trans("Experimental");
        elseif ($this->version == 'dolibarr') return DOL_VERSION;
        elseif ($this->version) return $this->version;
        else return $langs->trans("Unknown");
    }

	
    /**
     *      Function called when a Dolibarrr business event is done.
     *      All functions "run_trigger" are triggered if file is inside directory htdocs/core/triggers
     *
     *      @param	string		$action		Event action code
     *      @param  Object		$object     Object
     *      @param  User		$user       Object user
     *      @param  Translate	$langs      Object langs
     *      @param  conf		$conf       Object conf
     *      @return int         			<0 if KO, 0 if no triggered ran, >0 if OK
     */
	function run_trigger($action,&$object,&$user,$langs,&$conf)
	{
		global $db, $user, $conf;
		
		/*
		 * TRAITEMENT DE CREATION PROPAL, COMMANDE, FACTURE, EXPEDITION
		 */
		if($action == "PROPAL_CREATE" || $action =="ORDER_CREATE" || $action =="BILL_CREATE" || $action =="SHIPPING_CREATE" || $action=="COMPANY_CREATE" || $action=="ORDER_SUPPLIER_CREATE"){
			if(isset($_REQUEST['incoterms']) && !empty($_REQUEST['incoterms'])){
				
				$db->query('UPDATE '.MAIN_DB_PREFIX.$object->table_element.' SET fk_incoterms = '.$_REQUEST['incoterms'].', location_incoterms = \''.$_REQUEST['location_incoterms'].'\' WHERE rowid = '.$object->id);
			}	
		}
		
		if($action == "BEFORE_PROPAL_BUILDDOC" || $action == "BEFORE_ORDER_BUILDDOC"  || $action == "BEFORE_BILL_BUILDDOC" || $action == "BEFORE_ORDER_SUPPLIER_BUILDDOC" || $action == "BEFORE_BILL_SUPPLIER_BUILDDOC"){
				
			
			//Ajout des Incoterms dans la note public
			$resl = $db->query('SELECT ci.code, te.location_incoterms
					FROM '.MAIN_DB_PREFIX.'c_incoterms as ci
						LEFT JOIN '.MAIN_DB_PREFIX.$object->table_element.' as te ON (te.fk_incoterms = ci.rowid)
					WHERE te.rowid = '.$object->id);
			if($resl) 
				$res = $db->fetch_object($resl);
			
			$txt = '';
			if($res && strpos($object->note_public, 'Incoterm') === FALSE){
				$txt .= "\nIncoterm : ".$res->code;
				if(!empty($res->location_incoterms)) $txt .= ' - '.$res->location_incoterms;
			}
			
			// Gestion des sauts de lignes si la note était en HTML de base
			if(dol_textishtml($object->note_public)) $object->note_public .= dol_nl2br($txt);
			else $object->note_public .= $txt;
			
		}	
		
		if($action == "PROPAL_BUILDDOC" || $action == "ORDER_BUILDDOC"  || $action == "BILL_BUILDDOC" || $action == "ORDER_SUPPLIER_BUILDDOC" || $action == "BILL_SUPPLIER_BUILDDOC") {
			
			$object->fetch($object->id);

		}
				
		return 1;
	}
}
