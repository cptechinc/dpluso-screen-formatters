<?php
	namespace Dplus\Dpluso\ScreenFormatters\II;
	
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Content\HTMLWriter;
	use Dplus\Content\Table;
	use Dplus\Dpluso\ScreenFormatters\TableScreenMaker;
	
	/**
	 * Formatter for Item Pricing Screen
	 * Not Formattable
	 */
	 class ItemPricingScreen extends TableScreenMaker {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ii-pricing'; 
		protected $title = 'Item Pricing';
		protected $datafilename = 'iiprice'; 
		protected $testprefix = 'iiprc';
		protected $datasections = array();
		
		/* =============================================================
			PUBLIC FUNCTIONS
		============================================================ */
		public function generate_screen() {
			$bootstrap = new HTMLWriter();
			$content = '';
			$content .= $this->generate_itemtable();
			
			$content .= $bootstrap->open('div', 'class=row');
				$content .= $bootstrap->open('div', 'class=col-sm-4');
					$content .= $bootstrap->h3('', 'Standard Pricing');
					$content .= $this->generate_standardpricingtable();
				$content .= $bootstrap->close('div');
				
				$content .= $bootstrap->open('div', 'class=col-sm-4');
					$content .= $bootstrap->h3('', 'Customer Pricing');
					$content .= $this->generate_customerpricingtable();
				$content .= $bootstrap->close('div');
				
				$content .= $bootstrap->open('div', 'class=col-sm-4');
					$content .= $bootstrap->h3('', 'Pricing Derived From');
					$content .= $this->generate_derivedpricingtable();
				$content .= $bootstrap->close('div');
			$content .= $bootstrap->close('div');
			return $content;
		}
		
		/* =============================================================
			CLASS FUNCTIONS
		============================================================ */
		/**
		 * Returns the Derived Pricing Table
		 * @return string HTML Derived Pricing Table
		 */
		protected function generate_derivedpricingtable() {
			$tb = new Table('class=table table-striped table-condensed table-excel');
			$tb->tablesection('thead');
				$tb->tr();
				foreach($this->json['columns']['pricing derived from'] as $column) {
					$class = DplusWire::wire('config')->textjustify[$column['headingjustify']];
					$tb->th("class=$class", $column['heading']);
				}
			$tb->closetablesection('thead');
			$tb->tablesection('body');
				foreach ($this->json['data']['pricing derived from'] as $derivedpricing) {
					$tb->tr();
					foreach(array_keys($this->json['columns']['pricing derived from']) as $column) {
						$class = DplusWire::wire('config')->textjustify[$this->json['columns']['pricing derived from'][$column]['datajustify']];
						$tb->td("class=$class", $derivedpricing[$column]);
					}
				}
			$tb->closetablesection('tbody');
			return $tb->close();
		}
		
		/**
		 * Returns the Customer Pricing Table
		 * @return string HTML customer pricing table
		 */
		protected function generate_customerpricingtable() {
			$tb = new Table('class=table table-striped table-condensed table-excel');
			$tb->tablesection('thead');
				$tb->tr();
				foreach($this->json['columns']['customer pricing'] as $column) {
					$class = DplusWire::wire('config')->textjustify[$column['headingjustify']];
					$tb->th("class=$class", $column['heading']);
				}
			$tb->closetablesection('thead');
			$tb->tablesection('body');
				foreach ($this->json['data']['customer pricing']['cust breaks'] as $customerpricing) {
					$tb->tr();
					foreach(array_keys($this->json['columns']['customer pricing']) as $column) {
						$class = DplusWire::wire('config')->textjustify[$this->json['columns']['customer pricing'][$column]['datajustify']];
						$tb->td("class=$class", $customerpricing[$column]);
					}
				}
			$tb->closetablesection('tbody');
			return $tb->close();
		}
		
		/**
		 * Returns HTML Table for Standard Pricing
		 * @return string HTML Table for Standard Pricing
		 */
		protected function generate_standardpricingtable() {
			$bootstrap = new HTMLWriter();
			$tb = new Table('class=table table-striped table-condensed table-excel');
			$tb->tablesection('thead');
				$tb->tr();
				foreach($this->json['columns']['standard pricing'] as $column) {
					$class = DplusWire::wire('config')->textjustify[$column['headingjustify']];
					$tb->th("class=$class", $column['heading']);
				}
			$tb->closetablesection('thead');
			$tb->tablesection('body');
				$tb->tr();
					$tb->td('', $bootstrap->b('', 'Last Price Date: '));
					$tb->td('', $this->json['data']['standard pricing']['last price date']);
				foreach ($this->json['data']['standard pricing']['standard breaks'] as $standardpricing) {
					$tb->tr();
					foreach(array_keys($this->json['columns']['standard pricing']) as $column) {
						$class = DplusWire::wire('config')->textjustify[$this->json['columns']['standard pricing'][$column]['datajustify']];
						$tb->td("class=$class", $standardpricing[$column]);
					}
				}
			$tb->closetablesection('tbody');
			return $tb->close();
		}
		
		/**
		 * Returns HTML Table for Item Summary
		 * @return string HTML Table for Item Summary
		 */
		protected function generate_itemtable() {
			$bootstrap = new HTMLWriter();
			$tb = new Table('class=table table-striped table-condensed table-excel');
			$tb->tr();
				$tb->td('', '<b>Item ID</b>');
				$tb->td('', $this->json['itemid']);
				$tb->td('colspan=2', $this->json['desc1']);
			$tb->tr();
				$tb->td('', $bootstrap->b('', 'Customer ID'));
				$button = $bootstrap->button("type=button|class=btn btn-primary btn-sm|data-dismiss=modal|onclick=iicust('ii-pricing')", 'Change Customer');
				$content = $this->json['custid']." - ".$this->json['cust name'] . ' &nbsp; ';
				$tb->td('colspan=2', $content);
			$tb->tr();
				$tb->td('', $bootstrap->b('', 'Cust Price Code'));
				$tb->td('colspan=2', $this->json['cust price code']." - ".$this->json['cust price desc']);
			return $tb->close();
		}
	}
