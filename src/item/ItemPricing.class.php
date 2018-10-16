<?php
	namespace Dplus\Dpluso\ScreenFormatters\Item;
	
	use Dplus\Dpluso\ScreenFormatters\TableScreenMaker;
	use Dplus\Content\HTMLWriter;
	use Dplus\Content\Table;
	
	/**
	 * Item pricing Parses and generates the display for 
	 * item pricing
	 * Used on Edit Item Detail 
	 */
	 class ItemPricing extends TableScreenMaker {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'item-pricing'; 
		protected $title = 'Item Pricing';
		protected $datafilename = 'price'; 
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
