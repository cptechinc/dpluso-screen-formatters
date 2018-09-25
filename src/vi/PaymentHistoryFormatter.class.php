<?php
	/**
	 * Formatter for VI Payment History Screen
	 * Formattable
	 */
	class VI_PaymentHistoryFormatter extends TableScreenFormatter {
        protected $tabletype = 'normal';
		protected $type = 'vi-payment-history'; 
		protected $title = 'Vendor Payment History';
		protected $datafilename = 'vipayment'; 
		protected $testprefix = 'vipy';
		protected $formatterfieldsfile = 'vipyfmattbl';
		protected $datasections = array(
			"detail" => "Detail"
		);
		
		/* =============================================================
            PUBLIC FUNCTIONS
       	============================================================= */
        public function generate_screen() {
            $bootstrap = new HTMLWriter();
            $content = '';
			$this->generate_tableblueprint();
			
            $tb = new Table('class=table table-striped table-bordered table-condensed table-excel|id=payments');
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
        		foreach($this->json['data']['payments'] as $invoice) {
        			for ($x = 1; $x < $this->tableblueprint['detail']['maxrows'] + 1; $x++) {
        				$tb->tr();
						$columncount = 0;
        				for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
        					if (isset($this->tableblueprint['detail']['rows'][$x]['columns'][$i])) {
        						$column = $this->tableblueprint['detail']['rows'][$x]['columns'][$i];
        						$class = DplusWire::wire('config')->textjustify[$this->fields['data']['detail'][$column['id']]['datajustify']];
        						$colspan = $column['col-length'];
        						$celldata = TableScreenMaker::generate_formattedcelldata($this->fields['data']['detail'][$column['id']]['type'], $invoice, $column);
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
        	$tb->closetablesection('tbody');
        	$tb->close();
			return $tb->close();
        }
		
		public function generate_javascript() {
			$bootstrap = new HTMLWriter();
			$content = $bootstrap->open('script', '');
			if (!$this->forprint) {
				$content .= "\n";
					if ($this->tableblueprint['detail']['maxrows'] < 2) {
						$content .= $bootstrap->indent() . "$(function() {";
						$content .= $bootstrap->indent() . $bootstrap->indent() . "$('#payments').DataTable();";
						$content .= $bootstrap->indent() ."});";
					}
				$content .= "\n";
			}	
			$content .= $bootstrap->close('script');
			return $content;
		}
    }
