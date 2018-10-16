<?php
	namespace Dplus\Dpluso\ScreenFormatters\II;
	
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Content\HTMLWriter;
	use Dplus\Content\Table;
	use Dplus\Dpluso\ScreenFormatters\TableScreenMaker;
	use Dplus\Dpluso\ScreenFormatters\TableScreenFormatter;
	
	/**
	 * Formatter of II Item Purchase Orders
	 * Formattable
	 */
	class PurchaseOrdersFormatter extends TableScreenFormatter {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ii-purchase-orders'; // ii-sales-history
		protected $title = 'Item Purchase Orders';
		protected $datafilename = 'iipurchordr'; // iisaleshist.json
		protected $testprefix = 'iipo'; // iish
		protected $formatterfieldsfile = 'iipofmattbl'; // iishfmtbl.json
		protected $datasections = array(
			"detail" => "Detail"
		);
		
		/* =============================================================
			PUBLIC FUNCTIONS
		============================================================ */
		public function generate_screen() {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->ajaxload."ii/ii-documents/order/");
			$itemID = $this->json['itemid'];
			$bootstrap = new HTMLWriter();
			$content = '';
			$this->generate_tableblueprint();
			
			foreach ($this->json['data'] as $whseid => $whse) {
				$content .= $bootstrap->h3('', $whse['Whse Name']);
				
				$tb = new Table("class=table table-striped table-bordered table-condensed table-excel|id=$whseid");
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
						foreach($whse['orders'] as $order) {
							$ponbr = $order['Order Number'];
							if ($order != $whse['orders']['TOTAL']) {
								for ($x = 1; $x < $this->tableblueprint['detail']['maxrows'] + 1; $x++) {
									$tb->tr();
									$columncount = 0;
									for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
										if (isset($this->tableblueprint['detail']['rows'][$x]['columns'][$i])) {
											$column = $this->tableblueprint['detail']['rows'][$x]['columns'][$i];
											$class = DplusWire::wire('config')->textjustify[$this->fields['data']['detail'][$column['id']]['datajustify']];
											$colspan = $column['col-length'];
											$celldata = TableScreenMaker::generate_formattedcelldata($this->fields['data']['detail'][$column['id']]['type'], $order, $column);
											
											if ($column['id'] == 'Order Number') {
												$url->query->setData(array('itemID' => $this->json['itemid'], 'ordn' => $ponbr, 'returnpage' => urlencode(DplusWire::wire('page')->fullURL->getUrl())));
												$href = $url->getUrl();
												$celldata .= "&nbsp; " . $bootstrap->create_element('a', "href=$href|class=load-order-documents|title=Load Order Documents|aria-label=Load Order Documents|data-ordn=$ponbr|data-itemid=$itemID|data-type=$this->type", $bootstrap->icon('fa fa-file-text'));
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
							}
						}
					$tb->closetablesection('tbody');
					$tb->tablesection('tfoot');
						$order = $whse['orders']['TOTAL'];
						$x = 1;
						$tb->tr('class=totals');
						$columncount = 0;
						for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
							if ($i == 1) {
								$tb->td('', 'TOTALS');
								$colspan = 1;
							} elseif (isset($this->tableblueprint['detail']['rows'][$x]['columns'][$i])) {
								$column = $this->tableblueprint['detail']['rows'][$x]['columns'][$i];
								$class = DplusWire::wire('config')->textjustify[$this->fields['data']['detail'][$column['id']]['datajustify']];
								$colspan = $column['col-length'];
								$celldata = TableScreenMaker::generate_formattedcelldata($this->fields['data']['detail'][$column['id']]['type'], $order, $column);
								$tb->td("colspan=$colspan|class=$class", $celldata);
							} else {
								if ($columncount < $this->tableblueprint['cols']) {
									$colspan = 1;
									$tb->th();
								}
								$columncount += $colspan;
							}
						}
					$tb->closetablesection('tfoot');
					$table = $tb->close();
					$content .= $table;
			}
			return $content;
		}
		
		public function generate_javascript() {
			$bootstrap = new HTMLWriter();
			$content = $bootstrap->open('script', '');
				if (!$this->forprint) {
					$content .= "\n";
					$content .= $bootstrap->indent().'$(function() {';
						if ($this->tableblueprint['detail']['maxrows'] < 2) {
							foreach ($this->json['data'] as $whseid => $whse) {
								$content .= $bootstrap->indent()."$('#$whseid').DataTable();";
							}
						}
					$content .= $bootstrap->indent().'});';
					$content .= "\n";
				}
			$content .= $bootstrap->close('script');
			return $content;
		}
	}
