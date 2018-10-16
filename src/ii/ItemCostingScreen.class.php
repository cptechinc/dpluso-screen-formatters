<?php
	namespace Dplus\Dpluso\ScreenFormatters\II;
	
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Content\HTMLWriter;
	use Dplus\Content\Table;
	use Dplus\Dpluso\ScreenFormatters\TableScreenMaker;
	
	/**
	 * II Item Costing Screen
	 * Not Formattable
	 */
	 class ItemCostingScreen extends TableScreenMaker {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ii-cost'; 
		protected $title = 'Item Costing';
		protected $datafilename = 'iicost'; 
		protected $testprefix = 'iicost';
		protected $datasections = array();
		
		/* =============================================================
			PUBLIC FUNCTIONS
		============================================================= */
		public function generate_screen() {
			$bootstrap = new HTMLWriter();
			$content = $this->generate_itemtable();
			
			if ($this->forprint) {
				$content .= $bootstrap->h2('class=page-header', 'Warehouse');
				$content .= $bootstrap->div('class=form-group', $this->generate_whsesection());
				$content .= $bootstrap->h2('class=page-header', 'Vendor');
				$content .= $bootstrap->div('class=form-group', $this->generate_vendorsection());
				$content .= $bootstrap->h2('class=page-header', 'Last Purchase');
				$content .= $bootstrap->div('class=form-group', $this->generate_lastpurchasedtable());
			} else {
				$listitems = $bootstrap->li('role=presentation|class=active', $bootstrap->a('href=#whse|aria-controls=warehouse|role=tab|data-toggle=tab', 'Warehouse'));
				$listitems .= $bootstrap->li('role=presentation', $bootstrap->a('href=#vendor|aria-controls=vendor|role=tab|data-toggle=tab', 'Vendor'));
				$listitems .= $bootstrap->li('role=presentation', $bootstrap->a('href=#lastpurchase|aria-controls=lastpurchase|role=tab|data-toggle=tab', 'Last Purchase'));
				$content .= $bootstrap->ul('class=nav nav-tabs|role=tablist', $listitems);
				
				$tabs = $bootstrap->div('role=tabpanel|class=tab-pane active|id=whse', $this->generate_whsesection());
				$tabs .= $bootstrap->div('role=tabpanel|class=tab-pane|id=vendor', $this->generate_vendorsection());
				$tabs .= $bootstrap->div('role=tabpanel|class=tab-pane|id=lastpurchase', $this->generate_lastpurchasedtable());
				
				$content .= $bootstrap->div('', $bootstrap->div('class=tab-content', $tabs));
			}
			return $content;
		}
		
		/**
		 * Returns HTML Item Description Table
		 * @return string HTML Item Description Table
		 */
		public function generate_itemtable() {
			$tb = new Table('class=table table-striped table-condensed table-excel');
			$tb->tr();
			$tb->td('', '<b>Item ID</b>')->td('', $this->json['itemid'])->td('colspan=2', $this->json['desc1']);
			
			$tb->tr();
			$tb->td('', '<b>Sales UoM</b>')->td('', $this->json['sale uom'])->td('colspan=2', $this->json['desc2']);
			
			$tb->tr();
			$tb->td('', '<b>Stan Cost</b>')->td('class=text-center', $this->json['stan cost'])->td('', '<b>Avg Cost:</b> ' . $this->json['avg cost']);
			$tb->td('', '<b>Avg Cost:</b> ' . $this->json['last cost']);
			return $tb->close();
		}
		
		/**
		 * Returns HTML for the Warehouse Section with lot information
		 * @return string HTML for the Warehouse Section
		 */
		public function generate_whsesection() {
			$bootstrap = new HTMLWriter();
			$content = '';
			if (!isset($this->json['data']['warehouse'])) return $content;
			
			foreach ($this->json['data']['warehouse'] as $whse) {
				$content .= '<h3>'.$whse['whse name'].'</h3>'; 
				$tb = new Table('class=table table-striped table-bordered table-condensed table-excel no-bottom');
				$tb->tablesection('thead')->tr();
					foreach ($this->json['columns']['warehouse'] as $column) {
						$class = DplusWire::wire('config')->textjustify[$column['headingjustify']];
						$tb->th("class=$class", $column['heading']);
					}
				$tb->closetablesection('thead');
				$tb->tablesection('tbody');
					foreach ($whse['lots'] as $lot) {
						$tb->tr();
						foreach (array_keys($this->json['columns']['warehouse']) as $column) {
							$class = DplusWire::wire('config')->textjustify[$this->json['columns']['warehouse'][$column]['datajustify']];
							$tb->td("class=$class", $lot[$column]);
						}
					}
				$tb->closetablesection('tbody');
				$content.= $tb->close();
			}
			return $content;
		}
		
		/**
		 * Returns HTML for the Vendor Section
		 * @return string HTML for the Vendor Section
		 */
		public function generate_vendorsection() {
			$bootstrap = new HTMLWriter();
			$content = '';
			foreach ($this->json['data']['vendor'] as $vendor) {
				$content .= '<h3>'.$vendor['vend id'].'</h3>';
				$content .= $bootstrap->open('div', 'class=row');
					$content .= $bootstrap->open('div', 'class=col-sm-6');
						$tb = new Table('class=table table-striped table-bordered table-condensed table-excel no-bottom');
						$tb->tr()->td('', 'Vendor:')->td('', $vendor['vend name']);
						$tb->tr()->td('', 'Phone Nbr:')->td('', $vendor['vend phone']);
						$tb->tr()->td('', 'Purch UoM:')->td('', $vendor['vend uom']);
						$tb->tr()->td('', 'Case Qty:')->td('', $vendor['vend case qty']);
						$tb->tr()->td('', 'List Price:')->td('', $vendor['vend price']);
						$tb->tr()->td('', 'Change Date:')->td('', $vendor['vend chg date']);
						$tb->tr()->td('', 'PO Order Code:')->td('', $vendor['vend po code']);
						$content .= $tb->close();
					$content .= $bootstrap->close('div'); // CLOSES col-sm-6
					
					$content .= $bootstrap->open('div', 'class=col-sm-6');
						$tb = new Table('class=table table-striped table-bordered table-condensed table-excel no-bottom');
						$tb->tr();
						$tb->tablesection('thead');
							foreach ($this->json['columns']['vendor'] as $column) {
								$class = DplusWire::wire('config')->textjustify[$column['headingjustify']];
								$tb->th("class=$class", $column['heading']);
							}
						$tb->closetablesection('thead');
						$tb->tablesection('tbody');
							foreach ($vendor['vend cost breaks'] as $costbreak) {
								$tb->tr();
								foreach (array_keys($this->json['columns']['vendor']) as $column) {
									$class = DplusWire::wire('config')->textjustify[$this->json['columns']['vendor'][$column]['datajustify']];
									$tb->td("class=$class", $costbreak[$column]);
								}
							}
						$tb->closetablesection('tbody');
						$content .= $tb->close();
					$content .= $bootstrap->close('div'); // CLOSES col-sm-6
				$content .= $bootstrap->close('div');
			}
			return $content;
		}
		
		/**
		 * Returns HTML Table for the last purchased
		 * @return string HTML Table for the last purchased
		 */
		function generate_lastpurchasedtable() {
			$tb = new Table('class=table table-striped table-bordered table-condensed table-excel no-bottom');
			$tb->tr();
			$tb->tablesection('thead');
				foreach ($this->json['columns']['last purchase'] as $column) {
					$class = DplusWire::wire('config')->textjustify[$column['headingjustify']];
					$tb->th("class=$class", $column['heading']);
				}
			$tb->closetablesection('thead');
			$tb->tablesection('tbody');
				foreach ($this->json['data']['last purchase'] as $lastpurchase) {
					$tb->tr();
					foreach (array_keys($this->json['columns']['last purchase']) as $column) {
						$class = DplusWire::wire('config')->textjustify[$this->json['columns']['last purchase'][$column]['datajustify']];
						$tb->td("class=$class", $lastpurchase[$column]);
					}
				}
			$tb->closetablesection('tbody');
			return $tb->close();
		}
	}
