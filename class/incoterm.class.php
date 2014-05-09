<?php
class TIncoterm{
	
	static function doActionsIncoterm(&$parameters, &$object, &$action, &$hookmanager) {
		
		global $langs, $db, $conf, $user;
		
		if (in_array('ordercard',explode(':',$parameters['context'])) || in_array('propalcard',explode(':',$parameters['context']))
			|| in_array('expeditioncard',explode(':',$parameters['context'])) || in_array('invoicecard',explode(':',$parameters['context']))
			|| in_array('receptioncard',explode(':',$parameters['context'])) || in_array('ordersuppliercard',explode(':',$parameters['context']))){
			
        	if ($action == 'builddoc')
			{
				
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
				
				//Si le module est actif sans module spécifique client alors on reproduit la génération standard dolibarr sinon on retourne l'objet modifié
				if(!$conf->global->USE_SPECIFIC_CLIENT){
						
					// ***********************************************
					// On reproduis le traitement standard de dolibarr
					// ***********************************************
					
					if (GETPOST('model'))
					{
						$object->setDocModel($user, GETPOST('model'));
					}
					
					// Define output language
					$outputlangs = $langs;
					if (! empty($conf->global->MAIN_MULTILANGS))
					{
						$outputlangs = new Translate("",$conf);
						$newlang=(GETPOST('lang_id') ? GETPOST('lang_id') : $object->client->default_lang);
						$outputlangs->setDefaultLang($newlang);
					}
					
					switch ($object->element) {
						case 'propal':
							$result= propale_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
							break;
						case 'facture':
							$result= facture_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
							break;
						case 'commande':
							$result= commande_pdf_create($db, $object, $object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
							break;
						case 'shipping':
							$result= expedition_pdf_create($db, $object, $object->modelpdf, $outputlangs);
							break;
						case 'delivery':
							$result=delivery_order_pdf_create($db, $object, $object->modelpdf, $outputlangs);
							break;
						case 'order_supplier':
							$result=supplier_order_pdf_create($db, $object, $object->modelpdf, $outputlangs);
							break;
						
						default:
							
							break;
					}
					
					
					if ($result <= 0)
					{
						dol_print_error($db,$result);
						exit;
					}
					elseif(!in_array('ordercard',explode(':',$parameters['context'])))
					{
						header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id.(empty($conf->global->MAIN_JUMP_TAG)?'':'#builddoc'));
						exit;
					}
				}
				
			}
		}
	}
}

class TIncotermsFacture extends TObjetStd {
	function __construct() { /* declaration */
		global $langs;
		
		parent::set_table(MAIN_DB_PREFIX.'facture');
		parent::add_champs('fk_incoterms','type=entier;');
		parent::add_champs('location_incoterms','type=chaine;');
		
		parent::_init_vars();
		parent::start();
	}
}

class TIncotermsCommande extends TObjetStd {
	function __construct() { /* declaration */
		global $langs;
		
		parent::set_table(MAIN_DB_PREFIX.'commande');
		parent::add_champs('fk_incoterms','type=entier;');
		parent::add_champs('location_incoterms','type=chaine;');
		
		parent::_init_vars();
		parent::start();
	}
}

class TIncotermsPropal extends TObjetStd {
	function __construct() { /* declaration */
		global $langs;
		
		parent::set_table(MAIN_DB_PREFIX.'propal');
		parent::add_champs('fk_incoterms','type=entier;');
		parent::add_champs('location_incoterms','type=chaine;');
		
		parent::_init_vars();
		parent::start();
	}
}

class TIncotermsExpedition extends TObjetStd {
	function __construct() { /* declaration */
		global $langs;
		
		parent::set_table(MAIN_DB_PREFIX.'expedition');
		parent::add_champs('fk_incoterms','type=entier;');
		parent::add_champs('location_incoterms','type=chaine;');
		
		parent::_init_vars();
		parent::start();
	}
}

class TIncotermsLivraison extends TObjetStd {
	function __construct() { /* declaration */
		global $langs;
		
		parent::set_table(MAIN_DB_PREFIX.'livraison');
		parent::add_champs('fk_incoterms','type=entier;');
		parent::add_champs('location_incoterms','type=chaine;');
		
		parent::_init_vars();
		parent::start();
	}
}

class TIncotermsSociete extends TObjetStd {
	function __construct() { /* declaration */
		global $langs;
		
		parent::set_table(MAIN_DB_PREFIX.'societe');
		parent::add_champs('fk_incoterms','type=entier;');
		parent::add_champs('location_incoterms','type=chaine;');
		
		parent::_init_vars();
		parent::start();
	}
}

class TIncotermsCommandeFournisseur extends TObjetStd {
	function __construct() { /* declaration */
		global $langs;
		
		parent::set_table(MAIN_DB_PREFIX.'commande_fournisseur');
		parent::add_champs('fk_incoterms','type=entier;');
		parent::add_champs('location_incoterms','type=chaine;');
		
		parent::_init_vars();
		parent::start();
	}
}
