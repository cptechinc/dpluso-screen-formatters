<?php
	namespace Dplus\Dpluso\ScreenFormatters\II;
	
	use Dplus\ProcessWire\DplusWire as DplusWire;
	
	/**
	 * Formatter for II Purchase History Screen
	 * Formattable
	 */
	class II_PurchaseHistoryFormatter extends TableScreenFormatter {
        protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ii-purchase-history'; // ii-sales-history
		protected $title = 'Item Purchase History';
		protected $datafilename = 'iipurchhist'; // iisaleshist.json
		protected $testprefix = 'iiph'; // iish
		protected $formatterfieldsfile = 'iiphfmattbl'; // iishfmtbl.json
		protected $datasections = array(
			"detail" => "Detail",
            "lotserial" => "Lot / Serial"
		);
		
		/* =============================================================
            PUBLIC FUNCTIONS
       	============================================================ */
        public function generate_screen() {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->ajaxload."ii/ii-documents/order/");
			$itemID = $this->json['itemid'];
            $bootstrap = new Dplus\Content\HTMLWriter();
            $content = '';
			$this->generate_tableblueprint();
		    
			foreach ($this->json['data'] as $whseid => $whse) {
				$content .= $bootstrap->h3('', $whse['Whse Name']);
				$tb = new Dplus\Content\Table("class=table table-striped table-bordered table-condensed table-excel|id=$whseid");
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
						foreach ($whse['orders'] as $invoice) {
							$ponbr = $invoice['Purchase Order Number'];
							if ($invoice != $whse['orders']['TOTAL']) {
								for ($x = 1; $x < $this->tableblueprint['detail']['maxrows'] + 1; $x++) {
									$tb->tr();
									$columncount = 0;
									for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
										if (isset($this->tableblueprint['detail']['rows'][$x]['columns'][$i])) {
											$column = $this->tableblueprint['detail']['rows'][$x]['columns'][$i];
											$class = DplusWire::wire('config')->textjustify[$this->fields['data']['detail'][$column['id']]['datajustify']];
											$colspan = $column['col-length'];
											$celldata = TableScreenMaker::generate_formattedcelldata($this->fields['data']['detail'][$column['id']]['type'], $invoice, $column);
											
											if ($column['id'] == 'Purchase Order Number') {
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
								if (sizeof($invoice['lots']) > 0) {
									for ($x = 1; $x < $this->tableblueprint['lotserial']['maxrows'] + 1; $x++) {
										$tb->tr();
										$columncount = 0;
										for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
											if (isset($this->tableblueprint['lotserial']['rows'][$x]['columns'][$i])) {
												$column = $this->tableblueprint['lotserial']['rows'][$x]['columns'][$i];
												$class = DplusWire::wire('config')->textjustify[$this->fields['data']['lotserial'][$column['id']]['headingjustify']];
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
									foreach ($invoice['lots'] as $lot) {
										for ($x = 1; $x < $this->tableblueprint['lotserial']['maxrows'] + 1; $x++) {
											$tb->tr();
											$columncount = 0;
											for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
												if (isset($this->tableblueprint['lotserial']['rows'][$x]['columns'][$i])) {
													$column = $this->tableblueprint['lotserial']['rows'][$x]['columns'][$i];
													$class = DplusWire::wire('config')->textjustify[$this->fields['data']['lotserial'][$column['id']]['datajustify']];
													$colspan = $column['col-length'];
													$celldata = TableScreenMaker::generate_formattedcelldata($this->fields['data']['lotserial'][$column['id']]['type'], $lot, $column);
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
							} // END IF($invoice != $whse['orders']['TOTAL']) 
						} // END FOREACH ($whse['orders'] as $invoice) {
					$tb->closetablesection('tbody');
					$tb->tablesection('tfoot');
						$invoice = $whse['orders']['TOTAL'];
						$x = 1;
						$tb->tr('class=totals');
						for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
							if (isset($this->tableblueprint['detail']['rows'][$x]['columns'][$i])) {
								$column = $this->tableblueprint['detail']['rows'][$x]['columns'][$i];
								$class = DplusWire::wire('config')->textjustify[$this->fields['data']['detail'][$column['id']]['datajustify']];
								$celldata = TableScreenMaker::generate_formattedcelldata($this->fields['data']['detail'][$column['id']]['type'], $invoice, $column);
								$tb->td("class=$class", $celldata);
							} else {
								$tb->td();
							}
						}
					$tb->closetablesection('tfoot');
					$content .= $tb->close();
			}
            return $content;
        }
		
		public function generate_javascript() {
			$bootstrap = new Dplus\Content\HTMLWriter();
			$content = $bootstrap->open('script', '');
				$content .= "\n";
				$content .= $bootstrap->indent().'$(function() {';
					if ($this->tableblueprint['detail']['rows'] < 2) {
						foreach ($this->json['data'] as $whseid => $whse) {
							$content .= $bootstrap->indent()."$('#$whseid').DataTable();";
						}
					}
				$content .= $bootstrap->indent().'});';
				$content .= "\n";
			$content .= $bootstrap->close('script');
			return $content;
		}
    }
