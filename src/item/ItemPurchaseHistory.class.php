<?php 
	/**
	 * Item history Parses and generates the display for 
	 * item history
	 * Used on Edit Item Detail 
	 */
     class Item_ItemPurchaseHistory extends TableScreenMaker {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'item-purchasehistory'; 
		protected $title = 'Item Purchase History';
		protected $datafilename = 'prichist'; 
		protected $testprefix = 'iiprc';
		protected $datasections = array();
        
        /* =============================================================
          PUBLIC FUNCTIONS
       	============================================================ */
        public function generate_screen() {
            $bootstrap = new HTMLWriter();
            $content = '';
			$tb = new Table('class=table item-pricing table-striped table-condensed table-bordered print-hidden');
			$tb->tablesection('thead');
				$tb->tr();
				foreach($this->json['columns'] as $column => $name)  {
					$tb->th("", $name);
				}
			$tb->closetablesection('thead');
			$tb->tablesection('tbody');
				foreach($this->json['data'] as $warehouse) {
					$tb->tr();
					foreach($this->json['columns'] as $column => $name)  {
						if (is_numeric($warehouse[$column])) {
							$tb->td("class=text-right", $warehouse[$column]);
						} else {
							$tb->td("", $warehouse[$column]);
						}
					}
				}
			$tb->closetablesection('tbody');
			return $tb->close();
        }
    }
