<?php
	namespace Dplus\Dpluso\ScreenFormatters\VI;
	
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Dpluso\ScreenFormatters\TableScreenFormatter;
	use Dplus\Content\HTMLWriter;
	use Dplus\Content\Table;
	
	/**
	 * Formatter for VI Unreleased Purchase Orders Screen
	 * Formattable
	 */
	class UnreleasedPurchaseOrdersFormatter extends TableScreenFormatter {
		protected $tabletype = 'normal';
		protected $type = 'vi-unreleased-purchase-orders'; 
		protected $title = 'Vendor Unreleased Purchase Orders';
		protected $datafilename = 'viunreleased'; 
		protected $testprefix = 'viunrv';
		protected $formatterfieldsfile = 'vipofmattbl';
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
			$content = $bootstrap->div('class=row', $bootstrap->div('class=col-sm-4 form-group', $bootstrap->label('', 'Show Notes') . $this->generate_shownotesselect()));
			
			$tb = new Table('class=table table-striped table-bordered table-condensed table-excel|id=unreleased');
			$tb->tablesection('thead');
				for ($x = 1; $x < $this->tableblueprint['header']['maxrows'] + 1; $x++) {
					$tb->tr();
					$columncount = 0;
					for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
						if (isset($this->tableblueprint['header']['rows'][$x]['columns'][$i])) {
							$column = $this->tableblueprint['header']['rows'][$x]['columns'][$i];
							$class = DplusWire::wire('config')->textjustify[$this->fields['data']['header'][$column['id']]['headingjustify']];
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
								$tb->td();
							}
						}
						$columncount += $colspan;
					}
				}
			$tb->closetablesection('thead');
			$tb->tablesection('tbody');
				foreach($this->json['data']['purchaseorders'] as $order) {
					for ($x = 1; $x < $this->tableblueprint['header']['maxrows'] + 1; $x++) {
						$tb->tr('class=first-txn-row');
						$columncount = 0;
						for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
							if (isset($this->tableblueprint['header']['rows'][$x]['columns'][$i])) {
								$column = $this->tableblueprint['header']['rows'][$x]['columns'][$i];
								$class = DplusWire::wire('config')->textjustify[$this->fields['data']['header'][$column['id']]['datajustify']];
								$colspan = $column['col-length'];
								$celldata = TableScreenMaker::generate_formattedcelldata($this->fields['data']['header'][$column['id']]['type'], $order, $column);
								$tb->td("colspan=$colspan|class=$class", $celldata);
							} else {
								if ($columncount < $this->tableblueprint['cols']) {
									$colspan = 1;
									$tb->td();
								}
							}
							$columncount += $colspan;
						}
						foreach ($order['details'] as $detail) {
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
								}
								
								foreach ($detail['detailnotes'] as $note) {
									for ($y = 1; $y < $this->tableblueprint['detail']['maxrows'] + 1; $y++) {
										$tb->tr('class=show-notes hidden');
										for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
											if ($i == 2) {
												$tb->td('', $note['Detail Notes']);
											} else {
												$tb->td();
											}
										}
									}	
								} // end of detail notes
							}
						} // end of details
						
						foreach ($order['ordernotes'] as $ordernote) {
							for ($y = 1; $y < $this->tableblueprint['detail']['maxrows'] + 1; $y++) {
								$tb->tr('class=show-notes hidden');
								for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
									if ($i == 2) {
										$tb->td('', $ordernote['Order Notes']);
									} else {
										$tb->td();
									}
								}
							}
						}
						
						$pototals = $order['pototals'];
						for ($x = 1; $x < $this->tableblueprint['detail']['maxrows'] + 1; $x++) {
							$tb->tr();
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
						} // end of pototals
					} // end of purchase order #
				}
				
				$vendortotal = $this->json['data']['vendortotals'];
				for ($x = 1; $x < $this->tableblueprint['detail']['maxrows'] + 1; $x++) {
					$tb->tr('class=totals');
					$columncount = 0;
					for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
						if (isset($this->tableblueprint['detail']['rows'][$x]['columns'][$i])) {
							$column = $this->tableblueprint['detail']['rows'][$x]['columns'][$i];
							$class = DplusWire::wire('config')->textjustify[$this->fields['data']['detail'][$column['id']]['datajustify']];
							$colspan = $column['id'] == "Line Number" ? 2 : $column['col-length'];
							$celldata = TableScreenMaker::generate_formattedcelldata($this->fields['data']['detail'][$column['id']]['type'], $vendortotal, $column);
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
			$tb->closetablesection('tbody');
			$content .= $tb->close();
			return $content;
		}
		
		public function generate_javascript() {
			$bootstrap = new HTMLWriter();
			$content = $bootstrap->open('script', '');
				$content .= "\n";
					if ($this->tableblueprint['detail']['maxrows'] < 2) {
						$content .= $bootstrap->indent() . "$(function() {";
						$content .= $bootstrap->indent() . $bootstrap->indent() . "$('#unreleased').DataTable();";
						$content .= $bootstrap->indent() ."});";
					}
				$content .= "\n";
			$content .= $bootstrap->close('script');
			return $content;
		}
	}
