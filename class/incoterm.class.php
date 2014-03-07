<?php
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