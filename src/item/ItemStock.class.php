<?php
	namespace Dplus\Dpluso\ScreenFormatters\Item;
	
	use Dplus\Dpluso\ScreenFormatters\TableScreenMaker;
	use Dplus\Content\HTMLWriter;
	use Dplus\Content\Table;
	
	/**
	 * Item Stock Parses and generates the display for 
	 * item stock
	 * Used on Edit Item Detail 
	 */
	 class ItemStock extends TableScreenMaker {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'item-stock'; 
		protected $title = 'Item Stock';
		protected $datafilename = 'stock'; 
		protected $testprefix = 'iistk';
		protected $datasections = array();
		
		/* =============================================================
			PUBLIC FUNCTIONS
		============================================================ */
		public function generate_screen() {
			$bootstrap = new HTMLWriter();
			$content = '';
			$tb = new Table('class=table table-striped table-condensed table-bordered');
			$tb->tablesection('thead');
				$tb->tr();
				foreach($this->json['columns'] as $column => $name)  {
					$tb->th("", $name);
				}
			$tb->closetablesection('thead');
			$tb->tablesection('tbody');
				foreach($this->json['data'] as $warehouse) {
					$warehouseid = $warehouse['Warehouse ID'];
					$tb->tr("class=warehouse-tr $warehouseid-row");
					foreach($this->json['columns'] as $column => $name)  {
						if ($column == 'Warehouse ID') {
							$itemid = $this->json['itemid'];
							$whse = $warehouse[$column];
							$onclick = "choose_itemwhse('$itemid ', '$whse')";
							$button = $bootstrap->button("type=button|class=btn btn-primary btn-xs|onclick=$onclick", $warehouse[$column]);
							$tb->td("", $button);
						} else {
							if (is_numeric($warehouse[$column])) {
								$tb->td("class=text-right", $warehouse[$column]);
							} else {
								$tb->td("", $warehouse[$column]);
							}
						}
					}
				}
			$tb->closetablesection('tbody');
			return $bootstrap->div('class=table-responsive', $tb->close());
		}
	}
