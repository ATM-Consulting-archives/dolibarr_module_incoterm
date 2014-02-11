<?php
class ActionsIncoterm
{ 
     /** Overloading the doActions function : replacing the parent's function with the one below 
      *  @param      parameters  meta datas of the hook (context, etc...) 
      *  @param      object             the object you want to process (an invoice if you are in invoice module, a propale in propale's module, etc...) 
      *  @param      action             current action (if set). Generally create or edit or null 
      *  @return       void 
      */ 
     
    function doActions($parameters, &$object, &$action, $hookmanager) 
    {
    	global $langs, $db, $conf, $user;
		/*echo '<pre>';
		print_r($object);
		echo '</pre>';*/
		
        if($action == "validmodincoterm"){
			if(isset($_REQUEST['incoterms']) && !empty($_REQUEST['incoterms'])){
				$db->query('UPDATE '.MAIN_DB_PREFIX.$object->table_element.' SET fk_incoterms = '.$_REQUEST['incoterms'].' WHERE rowid = '.$object->id);
			}
		}
		
		//Ajout au document pdf
		if (in_array('ordercard',explode(':',$parameters['context'])) || in_array('propalcard',explode(':',$parameters['context']))
			|| in_array('expeditioncard',explode(':',$parameters['context'])) || in_array('invoicecard',explode(':',$parameters['context']))){
			
			/*echo '<pre>';
			print_r($action);
			echo '</pre>';exit;*/
			
        	if ($action == 'builddoc')
			{
				dol_include_once('/core/lib/product.lib.php');
				
				// *************************************
				// On modifie les infos qu'on souhaite
				// *************************************
				
				// 1 - Dans le document
				
				//Ajout des Incoterms dans la note public
				$resl = $db->query('SELECT ci.code
						FROM '.MAIN_DB_PREFIX.'c_incoterms as ci
							LEFT JOIN '.MAIN_DB_PREFIX.$object->table_element.' as te ON (te.fk_incoterms = ci.rowid)
						WHERE te.rowid = '.$object->id);
				$res = $db->fetch_object($resl);
				
				if($res){
					$object->note_public .= "\nIncoterm : ".$res->code;
				}
				
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
				$ret=$object->fetch($id);    // Reload to get new records
				
				/*echo '<pre>';
				print_r($object);
				echo '</pre>'; exit;*/
				
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
		
 
        return 0;
    }
    
    function formObjectOptions ($parameters, &$object, &$action, $hookmanager) 
    {
    	global $db, $user, $conf;
		dol_include_once('/incoterm/config.php');
		/*echo '<pre>';
		print_r($object);
		echo '</pre>';exit;*/
		
    	
		/*
		 * INCOTERMS 
		 */	
		if(in_array('propalcard',explode(':',$parameters['context'])) 
				|| in_array('ordercard',explode(':',$parameters['context'])) 
				|| in_array('invoicecard',explode(':',$parameters['context'])) 
				|| in_array('expeditioncard',explode(':',$parameters['context']))
				|| in_array('thirdpartycard',explode(':',$parameters['context']))){
				
			/*
			 * INCOTERMS
			 */	
				if($action == "create"){
					
					$sql = "SELECT fk_incoterms FROM ".MAIN_DB_PREFIX."societe WHERE rowid = ".$_REQUEST['socid'];
					
					if(in_array('expeditioncard',explode(':',$parameters['context']))){
						$sql = "SELECT s.fk_incoterms 
								FROM ".MAIN_DB_PREFIX."societe as s
									LEFT JOIN ".MAIN_DB_PREFIX."commande as c ON (c.fk_soc = s.rowid)
								WHERE c.rowid = ".$_REQUEST['origin_id'];
					}
					
					$resql = $db->query($sql);
										
					if($resql){
						$res = $db->fetch_object($resql);
						$id_incoterm = $res->fk_incoterms;
					}
					else 
						$id_incoterm = "";
					
					$sql = "SELECT rowid, code FROM ".MAIN_DB_PREFIX."c_incoterms ORDER BY rowid ASC";
					$resql = $db->query($sql);
					
					print '<tr><td>Incoterms</td>';
					print '<td colspan="2">';
					print '<select name="incoterms" class="flat" id="incoterms_id">';
					print '<option value="">&nbsp;</option>';
					
					while ($res = $db->fetch_object($resql)) {
						if($res->rowid == $id_incoterm){
							print '<option selected="selected" value="'.$res->rowid.'">'.$res->code.'</option>';
						}	
						else{
							print '<option value="'.$res->rowid.'">'.$res->code.'</option>';
						}
					}
					
					print '</select></td></tr>';
				}
				elseif($action == "modincoterm"){
					
					$sql = "SELECT fk_incoterms FROM ".MAIN_DB_PREFIX.$object->table_element." WHERE rowid = ".$object->id;
					$resql = $db->query($sql);
					
					if($resql){
						$res = $db->fetch_object($resql);
						$id_incoterm = $res->fk_incoterms;
					}
					else 
						$id_incoterm = "";
					
					$sql = "SELECT rowid, code FROM ".MAIN_DB_PREFIX."c_incoterms ORDER BY rowid ASC";
					$resql = $db->query($sql);
					$id_field = (in_array('thirdpartycard',explode(':',$parameters['context'])))? "socid" : "id";
					print '<tr><td>Incoterms</td>';
					print '<td colspan="2">';
					print '<form action="'.$_SERVER["PHP_SELF"].'?'.$id_field.'='.$object->id.'" method="post">';
					print '<input type="hidden" name="action" value="validmodincoterm" />';
					print '<select name="incoterms" class="flat" id="incoterms_id">';
					print '<option value="">&nbsp;</option>';
					
					while ($res = $db->fetch_object($resql)) {
						if($res->rowid == $id_incoterm)
							print '<option selected="selected" value="'.$res->rowid.'">'.$res->code.'</option>';
						else
							print '<option value="'.$res->rowid.'">'.$res->code.'</option>';
					}
					
					print '</select><input class="button" type="submit" value="Modifier"></form></td></tr>';
				}
				else{
					$sql = "SELECT fk_incoterms FROM ".MAIN_DB_PREFIX.$object->table_element." WHERE rowid = ".$object->id;
					$resql = $db->query($sql);
					if($resql){
						$res = $db->fetch_object($resql);
						
						$sql = "SELECT code FROM ".MAIN_DB_PREFIX."c_incoterms WHERE rowid = ".$res->fk_incoterms;
						$resql = $db->query($sql);
					}
					$id_field = (in_array('thirdpartycard',explode(':',$parameters['context'])))? "socid" : "id";
					print '<tr><td height="10"><table width="100%" class="nobordernopadding"><tbody><tr>';
					print '<td>Incoterms</td>';
					print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=modincoterm&'.$id_field.'='.$object->id.'">'
							.img_picto('Définir Incoterm', 'edit')
							.'</a></td>';
					PRINT '</tr></tbody></table></td>';
					print '<td colspan="3">';
					
					if($resql){
						$res = $db->fetch_object($resql);
						print $res->code;
					}
					
					print '</select></td></tr>';
					
				
				}
			
			
		}
			
        return 0;
    }

	function formAddObjectLine($parameters, &$object, &$action, $hookmanager){
		global $db,$user,$conf;
		
		
		return 0;
	}

 	function formEditProductOptions($parameters, &$object, &$action, $hookmanager) 
    {
    	global $db, $user,$conf;
		
		
        return 0;
    }
}