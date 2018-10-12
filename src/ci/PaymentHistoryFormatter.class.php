<?php
	use Dplus\ProcessWire\DplusWire as DplusWire;
	
	/**
	 * Formatter CI PAYMENTS HISTORY
	 * Formattable
	 */
	class CI_PaymentHistoryFormatter extends TableScreenFormatter {
        protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ci-payment-history';
		protected $title = 'Customer Payment History';
		protected $datafilename = 'cipayment';
		protected $testprefix = 'cioi';
		protected $formatterfieldsfile = 'cipyfmattbl';
		protected $datasections = array(
			"detail" => "Detail",
		);

		/* =============================================================
            PUBLIC FUNCTIONS
        ============================================================ */
        public function generate_screen() {
            $bootstrap = new Dplus\Content\HTMLWriter();
			$this->generate_tableblueprint();

			$tb = new Dplus\Content\Table('class=table table-striped table-bordered table-condensed table-excel|id=payments');
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
			return $tb->close();
        }

        public function generate_javascript() {
			if (!$this->forprint) {
				$bootstrap = new Dplus\Content\HTMLWriter();
				$content = $bootstrap->open('script', '');
					$content .= "\n";
					if ($this->tableblueprint['detail']['maxrows'] == 1) {
						$content .= $bootstrap->indent().'$(function() {';
							$content .= "$('#payments').DataTable();";
						$content .= $bootstrap->indent().'});';
					}
					$content .= "\n";
				$content .= $bootstrap->close('script');
				return $content;
			} else {
				return '';
			}
		}
    }
