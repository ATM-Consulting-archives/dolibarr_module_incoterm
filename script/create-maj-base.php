<?php
/*
 * Script créant et vérifiant que les champs requis s'ajoutent bien
 * 
 */
 	define('INC_FROM_CRON_SCRIPT', true);
	
	require('../config.php');
	require('../class/incoterm.class.php');

	$ATMdb=new TPDOdb;
	$ATMdb->debug=true;

	$o=new TIncotermsFacture;
	$o->init_db_by_vars($ATMdb);
	
	$o=new TIncotermsCommande;
	$o->init_db_by_vars($ATMdb);
	
	$o=new TIncotermsPropal;
	$o->init_db_by_vars($ATMdb);
	
	$o=new TIncotermsExpedition;
	$o->init_db_by_vars($ATMdb);
	
	$o=new TIncotermsLivraison;
	$o->init_db_by_vars($ATMdb);
	
	/*$o=new TProductUnit;
	$o->init_db_by_vars($ATMdb);*/
	
	$o=new TIncotermsSociete;
	$o->init_db_by_vars($ATMdb);
	
	$o=new TIncotermsCommandeFournisseur;
	$o->init_db_by_vars($ATMdb);