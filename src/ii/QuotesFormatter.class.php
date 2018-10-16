<?php
	namespace Dplus\Dpluso\ScreenFormatters\II;
	
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Dpluso\ScreenFormatters\TableScreenFormatter;
	use Dplus\Content\HTMLWriter;
	use Dplus\Content\Table;
	
	/**
	 * Formatter for II Item Quotes
	 * Formattable
	 */
	class Quotes extends TableScreenFormatter {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ii-quotes'; // ii-sales-history
		protected $title = 'Item Quotes';
		protected $datafilename = 'iiquote'; // iisaleshist.json
		protected $testprefix = 'iiqt'; // iish
		protected $formatterfieldsfile = 'iiqtfmattbl'; // iishfmtbl.json
		protected $datasections = array(
			"header" => "Header",
			"detail" => "Detail",
		);
		
		/* =============================================================
			PUBLIC FUNCTIONS
		============================================================ */
		public function generate_screen() {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->ajaxload."ii/ii-documents/quote/");
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
					foreach($whse['quotes'] as $quote) {
						for ($x = 1; $x < $this->tableblueprint['header']['maxrows'] + 1; $x++) {
							$tb->tr();
							$columncount = 0;
							for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
								if (isset($this->tableblueprint['header']['rows'][$x]['columns'][$i])) {
									$column = $this->tableblueprint['header']['rows'][$x]['columns'][$i];
									$class = DplusWire::wire('config')->textjustify[$this->fields['data']['header'][$column['id']]['datajustify']];
									$colspan = $column['col-length'];
									$celldata = strlen($column['label']) ? '<b>'.$column['label'].'</b>: ' : '';
									$celldata .= TableScreenMaker::generate_formattedcelldata($this->fields['data']['header'][$column['id']]['type'], $quote, $column);
									
									if ($i == 1 && !empty($quote["Quote ID"])) {
										$qnbr = $quote["Quote ID"];
										$itemID = $this->json['itemid'];
										$url->query->setData(array('itemID' => $this->json['itemid'], 'qnbr' => $qnbr, 'returnpage' => urlencode(DplusWire::wire('page')->fullURL->getUrl())));
										$href = $url->getUrl();
										$celldata .= "&nbsp; " . $bootstrap->create_element('a', "href=$href|class=load-quote-documents|title=Load Quote Documents|aria-label=Load Quote Documents|data-qnbr=$qnbr|data-itemid=$itemID|data-type=ii-quotes", $bootstrap->icon('fa fa-file-text'));
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

						foreach ($quote['details'] as $item) {
							for ($x = 1; $x < $this->tableblueprint['detail']['maxrows'] + 1; $x++) {
								$tb->tr();
								$columncount = 0;
								for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
									if (isset($this->tableblueprint['detail']['rows'][$x]['columns'][$i])) {
										$column = $this->tableblueprint['detail']['rows'][$x]['columns'][$i];
										$class = DplusWire::wire('config')->textjustify[$this->fields['data']['detail'][$column['id']]['datajustify']];
										$colspan = $column['col-length'];
										$celldata = TableScreenMaker::generate_formattedcelldata($this->fields['data']['detail'][$column['id']]['type'], $item, $column);
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
						$tb->tr('class=last-row-bottom');
						$tb->td('colspan='.$this->tableblueprint['cols'],'&nbsp;');
					}
				$tb->closetablesection('tbody');
				$content .= $tb->close();
			}
			return $content;
		}
	}
