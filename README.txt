/*******************************************************************************************************************************************
 **********************************************************doActions************************************************************************
 *******************************************************************************************************************************************/

	Hook doActions et fetch du $object à rajouter sur htdocs/livraison/fiche.php : (Avant les actions en début de page)
	
	$object = new Livraison($db);
	$object->fetch($_REQUEST['id']);
	$object->fetch_thirdparty();
	
	$hookmanager->initHooks(array('receptioncard'));
	$parameters=array();
	$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks


/*******************************************************************************************************************************************
 *******************************************************formObjectOption********************************************************************
 *******************************************************************************************************************************************/

	Hook formObjectOption à rajouter sur htdocs/livraison/fiche.php : (ligne 634 environ avant l'affichage des lignes produit)
	            
	$parameters=array('colspan' => ' colspan="3"');
	$reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$delivery,$action);    // Note that $action and $object may have been modified by hook