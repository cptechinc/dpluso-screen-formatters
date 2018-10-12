<?php
	use Dplus\ProcessWire\DplusWire as DplusWire;
	
	/**
	 * Formatter for VI Purchase History Screen
	 * Formattable
	 */
	class VI_PurchaseHistoryFormatter extends TableScreenFormatter {
        protected $tabletype = 'normal'; // grid or normal
		protected $type = 'vi-purchase-history'; // ii-sales-history
		protected $title = 'Vendor Purchase History';
		protected $datafilename = 'vipurchhist'; // iisaleshist.json
		protected $testprefix = 'viph'; // iish
		protected $formatterfieldsfile = 'viphfmattbl'; // iishfmtbl.json
		protected $datasections = array(
			"detail" => "Detail"
		);
		
		/* =============================================================
            PUBLIC FUNCTIONS
       	============================================================= */
        public function generate_screen() {
            $bootstrap = new Dplus\Content\HTMLWriter();
            $content = '';
			$this->generate_tableblueprint();
		    
			$tb = new Dplus\Content\Table('class=table table-striped table-bordered table-condensed table-excel|id=purchase-history');
			$tb->tablesection('thead');
				for ($x = 1; $x < $this->tableblueprint['detail']['maxrows'] + 1; $x++) {
					$tb->tr();
					$columncount = 0;
					for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
						if (isset($this->tableblueprint['detail']['rows'][$x]['columns'][$i])) {
							$column = $this->tableblueprint['detail']['rows'][$x]['columns'][$i];
							$class = DplusWire::wire('config')->textjustify[$this->fields['data']['detail'][$column['id']]['headingjustify']];
							$colspan = $column['col-length'];
							$tb->th("colspan=$colspan|class=$class", $column['label']);
						} else {
							if ($columncount < $this->tableblueprint['cols']) {
								$colspan = 1;
								$tb->th();
							}
						}
						$columncount += $colspan;
					}
				}
			$tb->closetablesection('thead');
			$tb->tablesection('tbody');
				foreach($this->json['data']['purchaseorders'] as $order) {
					foreach($order['details'] as $detail) {
						for ($x = 1; $x < $this->tableblueprint['detail']['maxrows'] + 1; $x++) {
							$tb->tr();
							$columncount = 0;
							for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
								if (isset($this->tableblueprint['detail']['rows'][$x]['columns'][$i])) {
									$column = $this->tableblueprint['detail']['rows'][$x]['columns'][$i];
									$class = DplusWire::wire('config')->textjustify[$this->fields['data']['detail'][$column['id']]['datajustify']];
									$colspan = $column['col-length'];
									$celldata = TableScreenMaker::generate_formattedcelldata($this->fields['data']['detail'][$column['id']]['type'], $detail, $column);
									$tb->td("colspan=$colspan|class=$class", $celldata);
								} else {
									if ($columncount < $this->tableblueprint['cols']) {
										$colspan = 1;
										$tb->td();
									}
								}
								$columncount += $colspan;
							}
						}
					}
					
					$pototals = $order['pototals'];
					for ($x = 1; $x < $this->tableblueprint['detail']['maxrows'] + 1; $x++) {
						$tb->tr('class=totals');
						$columncount = 0;
						for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
							if (isset($this->tableblueprint['detail']['rows'][$x]['columns'][$i])) {
								$column = $this->tableblueprint['detail']['rows'][$x]['columns'][$i];
								$class = DplusWire::wire('config')->textjustify[$this->fields['data']['detail'][$column['id']]['datajustify']];
								$colspan = $column['id'] == "Purchase Order Number" ? 2 : $column['col-length'];
								$celldata = TableScreenMaker::generate_formattedcelldata($this->fields['data']['detail'][$column['id']]['type'], $pototals, $column);
								$tb->td("colspan=$colspan|class=$class", $celldata);
							} else {
								if ($columncount < $this->tableblueprint['cols']) {
									$colspan = 1;
									$tb->td();
								}
							}
							$columncount += $colspan;
						}
					}
				}
				
				$vendortotal = $this->json['data']['vendortotals'];
				for ($x = 1; $x < $this->tableblueprint['detail']['maxrows'] + 1; $x++) {
					$tb->tr('class=totals');
					$columncount = 0;
					for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
						if (isset($this->tableblueprint['detail']['rows'][$x]['columns'][$i])) {
							$column = $this->tableblueprint['detail']['rows'][$x]['columns'][$i];
							$class = DplusWire::wire('config')->textjustify[$this->fields['data']['detail'][$column['id']]['datajustify']];
							$colspan = $column['id'] == "Purchase Order Number" ? 2 : $column['col-length'];
							$celldata = TableScreenMaker::generate_formattedcelldata($this->fields['data']['detail'][$column['id']]['type'], $vendortotal, $column);
							$tb->td("colspan=$colspan|class=$class", $celldata);
						} else {
							if ($columncount < $this->tableblueprint['cols']) {
								$colspan = 1;
								$tb->th();
							}
						}
						$columncount += $colspan;
					}
				}
				
			$tb->closetablesection('tbody');
			return $tb->close();
        }
		
		public function generate_javascript() {
			$bootstrap = new Dplus\Content\HTMLWriter();
			$content = $bootstrap->open('script', '');
				$content .= "\n";

				$content .= "\n";
			$content .= $bootstrap->close('script');
			return $content;
		}
    }
