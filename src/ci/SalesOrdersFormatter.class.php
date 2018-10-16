<?php
	namespace Dplus\Dpluso\ScreenFormatters\CI;
	
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Dpluso\ScreenFormatters\TableScreenFormatter;
	use Dplus\Content\HTMLWriter;
	use Dplus\Content\Table;
	
	/**
	 * Formatter CI Sales Orders Screen
	 * Formattable
	 */
	class SalesOrdersFormatter extends TableScreenFormatter {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ci-sales-orders'; // ii-sales-history
		protected $title = 'Customer Sales Orders';
		protected $datafilename = 'cisalesordr'; // iisaleshist.json
		protected $testprefix = 'ciso'; // iish
		protected $formatterfieldsfile = 'cisofmattbl'; // iishfmtbl.json
		protected $datasections = array(
			"header" => "Header",
			"detail" => "Detail",
			"itemstatus" => "Item Status",
			"purchaseorder" => "Purchase Order",
			"total" => "Total",
			"shipments" => "Shipments"
		);
		
		/* =============================================================
			PUBLIC FUNCTIONS
		============================================================ */
		public function generate_screen() {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->ajaxload."ci/ci-documents/order/");
			$bootstrap = new HTMLWriter();
			$content = '';
			$this->generate_tableblueprint();
			
			foreach ($this->json['data'] as $whseid => $whse) {
				$content .= $bootstrap->h3('', $whse['Whse Name']);
				
				$tb = new Table("class=table table-striped table-bordered table-condensed table-excel|id=$whseid");
				$tb->tablesection('thead');
					
				$tb->closetablesection('thead');
				$tb->tablesection('tbody');
					foreach($whse['orders'] as $order) {
						for ($x = 1; $x < $this->tableblueprint['header']['maxrows'] + 1; $x++) {
							$attr = ($x == 1) ? 'class=first-txn-row' : '';
							$tb->tr($attr);
							$columncount = 0;
							for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
								if (isset($this->tableblueprint['header']['rows'][$x]['columns'][$i])) {
									$column = $this->tableblueprint['header']['rows'][$x]['columns'][$i];
									$class = DplusWire::wire('config')->textjustify[$this->fields['data']['header'][$column['id']]['datajustify']];
									$colspan = $column['col-length'];					
									$celldata = strlen(trim($column['label'])) ? $bootstrap->b('',$column['label'].': ') : '';
									$celldata .= TableScreenMaker::generate_formattedcelldata($this->fields['data']['header'][$column['id']]['type'], $order, $column);

									if ($i == 1 && !empty($order['Order Number'])) {
										$ordn = $order['Ordn'];
										$custID = $this->json['custid'];
										$url->query->setData(array('custID' => $custID, 'ordn' => $ordn, 'returnpage' => urlencode(DplusWire::wire('page')->fullURL->getUrl())));
										$href = $url->getUrl();
										$celldata .= "&nbsp; " . $bootstrap->create_element('a', "href=$href|class=load-order-documents|title=Load Order Documents|aria-label=Load Order Documents|data-ordn=$ordn|data-custid=$custID|data-type=$this->type", $bootstrap->icon('fa fa-file-text'));
									}
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
						
						if (sizeof($order['details'])) {
							for ($x = 1; $x < $this->tableblueprint['detail']['maxrows'] + 1; $x++) {
								$tb->tr();
								$columncount = 0;
								for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
									if (isset($this->tableblueprint['detail']['rows'][$x]['columns'][$i])) {
										$column = $this->tableblueprint['detail']['rows'][$x]['columns'][$i];
										$class = DplusWire::wire('config')->textjustify[$this->fields['data']['detail'][$column['id']]['headingjustify']];
										$colspan = $column['col-length'];
										$tb->td("colspan=$colspan|class=$class", $bootstrap->b('', $column['label']));
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
									$columncount += $colspan;
								}
							}
							
							for ($x = 1; $x < $this->tableblueprint['itemstatus']['maxrows'] + 1; $x++) {
								$tb->tr();
								$columncount = 0;
								for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
									if (isset($this->tableblueprint['itemstatus']['rows'][$x]['columns'][$i])) {
										$column = $this->tableblueprint['itemstatus']['rows'][$x]['columns'][$i];
										$class = DplusWire::wire('config')->textjustify[$this->fields['data']['itemstatus'][$column['id']]['datajustify']];
										$colspan = $column['col-length'];
										$celldata = strlen(trim($column['label'])) ? $bootstrap->b('',$column['label'].': ') : '';
										$celldata .= TableScreenMaker::generate_formattedcelldata($this->fields['data']['itemstatus'][$column['id']]['type'], $detail['itemstatus'], $column);
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
							
							if (!empty($this->tableblueprint['purchaseorder']['rows'])) {
								foreach ($detail['purchordrs'] as $purchaseorder) {
									$tb->tr();
									$columncount = 0;
									for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
										if (isset($this->tableblueprint['purchaseorder']['rows'][1]['columns'][$i])) {
											$column = $this->tableblueprint['purchaseorder']['rows'][1]['columns'][$i];
											$class = DplusWire::wire('config')->textjustify[$this->fields['data']['purchaseorder'][$column['id']]['datajustify']];
											$colspan = $column['col-length'];
											$celldata = strlen(trim($column['label'])) ? $bootstrap->b('',$column['label'].': ') : '';
											$celldata .= TableScreenMaker::generate_formattedcelldata($this->fields['data']['purchaseorder'][$column['id']]['type'], $purchaseorder, $column);
											$tb->td("colspan=$colspan|class=$class", $celldata);
											$i = ($colspan > 1) ? $i + ($colspan - 1) : $i;
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
						} // END foreach ($order['details'] as $detail)
						
						// ORDER TOTALS
						for ($x = 1; $x < $this->tableblueprint['total']['maxrows'] + 1; $x++) {
							$tb->tr();
							$columncount = 0;
							for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
								if (isset($this->tableblueprint['total']['rows'][$x]['columns'][$i])) {
									$column = $this->tableblueprint['total']['rows'][$x]['columns'][$i];
									$class = DplusWire::wire('config')->textjustify[$this->fields['data']['total'][$column['id']]['datajustify']];
									$colspan = $column['col-length'];
									$celldata = strlen(trim($column['label'])) ? $bootstrap->b('',$column['label'].': ') : '';
									$celldata .= TableScreenMaker::generate_formattedcelldata($this->fields['data']['total'][$column['id']]['type'], $order['totals'], $column);
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
						
						if (sizeof($order['shipments'])) {
							for ($x = 1; $x < $this->tableblueprint['shipments']['maxrows'] + 1; $x++) {
								$tb->tr();
								$columncount = 0;
								for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
									if (isset($this->tableblueprint['shipments']['rows'][$x]['columns'][$i])) {
										$column = $this->tableblueprint['shipments']['rows'][$x]['columns'][$i];
										$class = DplusWire::wire('config')->textjustify[$this->fields['data']['shipments'][$column['id']]['headingjustify']];
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
						}
						
						foreach ($order['shipments'] as $shipment) {
							for ($x = 1; $x < $this->tableblueprint['shipments']['maxrows'] + 1; $x++) {
								$tb->tr();
								$columncount = 0;
								for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
									if (isset($this->tableblueprint['shipments']['rows'][$x]['columns'][$i])) {
										$column = $this->tableblueprint['shipments']['rows'][$x]['columns'][$i];
										$class = DplusWire::wire('config')->textjustify[$this->fields['data']['shipments'][$column['id']]['datajustify']];
										$colspan = $column['col-length'];
										$celldata = TableScreenMaker::generate_formattedcelldata($this->fields['data']['shipments'][$column['id']]['type'], $shipment, $column);
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
					}
				$tb->closetablesection('tbody');
				$content .= $tb->close();
			}
			return $content;
		}
	}
