<?php 
	namespace Dplus\Dpluso\ScreenFormatters\Item;

	/**
	 * Item KitComponentsParses and generates the display for 
	 * item KitComponents
	 * Used on Add Item
	 */
     class Item_ItemKitComponents extends TableScreenMaker {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'item-kitcomponents'; 
		protected $title = 'Item Kit Components';
		protected $datafilename = 'kititem'; 
		protected $testprefix = 'iiprc';
		protected $datasections = array();
        
        /* =============================================================
          PUBLIC FUNCTIONS
       	============================================================ */
        public function generate_screen() {
            $bootstrap = new Dplus\Content\HTMLWriter();
            $content = '';
			$tb = new Dplus\Content\Table('class=table item-pricing table-striped table-condensed table-bordered print-hidden');
			$tb->tablesection('thead');
				$tb->tr();
				foreach($this->json['columns'] as $column => $name)  {
					$tb->th("", $name);
				}
			$tb->closetablesection('thead');
			$tb->tablesection('tbody');
				foreach($this->json['data'] as $component) {
					$tb->tr();
					foreach($this->json['columns'] as $column => $name)  {
						$tb->td('', $component[$column]);
					}
				}
			$tb->closetablesection('tbody');
			return $tb->close();
        }
    }
