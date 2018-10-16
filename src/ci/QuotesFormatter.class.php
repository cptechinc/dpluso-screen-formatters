<?php
	namespace Dplus\Dpluso\ScreenFormatters\CI;

	use Dplus\ProcessWire\DplusWire;
	use Dplus\Dpluso\ScreenFormatters\TableScreenFormatter;
	use Dplus\Content\HTMLWriter;
	use Dplus\Content\Table;
	
	/**
	 * Formatter for CI Quotes
	 * Formattable
	 */
	class QuotesFormatter extends TableScreenFormatter {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ci-quotes'; // ii-sales-history
		protected $title = 'Customer Quotes';
		protected $datafilename = 'ciquote'; // iisaleshist.json
		protected $testprefix = 'ciqt'; // iish
		protected $formatterfieldsfile = 'ciqtfmattbl'; // iishfmtbl.json
		protected $datasections = array(
			"header" => "Header",
			"detail" => "Detail",
			"totals" => "Totals"
		);

		/* =============================================================
			PUBLIC FUNCTIONS
		============================================================ */
		public function generate_screen() {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->ajaxload."ci/ci-documents/quote/");
			$bootstrap = new HTMLWriter();
			$this->generate_tableblueprint();
			$content = '';

			foreach ($this->json['data'] as $whseid => $whse) {
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
							$attr = $x == 1 ? 'class=first-txn-row' : '';
							$tb->tr($attr);
							$columncount = 0;

							for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
								if (isset($this->tableblueprint['header']['rows'][$x]['columns'][$i])) {
									$column = $this->tableblueprint['header']['rows'][$x]['columns'][$i];
									$class = DplusWire::wire('config')->textjustify[$this->fields['data']['header'][$column['id']]['datajustify']];
									$colspan = $column['col-length'];
									$label = strlen(trim($column['label'])) ? '<b>'.$column['label'].'</b>: ' : '';
									$celldata = $label.TableScreenMaker::generate_formattedcelldata($this->fields['data']['header'][$column['id']]['type'], $quote, $column);

									if ($i == 1 && !empty($quote['Quote ID'])) {
										$qnbr = $quote['Quote ID'];
										$custID = $this->json['custid'];
										$url->query->setData(array('custID' => $custID, 'qnbr' => $qnbr, 'returnpage' => urlencode(DplusWire::wire('page')->fullURL->getUrl())));
										$href = $url->getUrl();
										$celldata .= "&nbsp; " . $bootstrap->create_element('a', "href=$href|class=load-quote-documents|title=Load Order Documents|aria-label=Load Quote Documents|data-qnbr=$qnbr|data-custid=$custID|data-type=$this->type", $bootstrap->icon('fa fa-file-text'));
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

						if (isset($this->tableblueprint['totals']) && isset($quote['totals'])) {
							for ($x = 1; $x < $this->tableblueprint['totals']['maxrows'] + 1; $x++) {
								$tb->tr();
								$columncount = 0;

								for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
									if (isset($this->tableblueprint['totals']['rows'][$x]['columns'][$i])) {
										$column = $this->tableblueprint['totals']['rows'][$x]['columns'][$i];
										$class = DplusWire::wire('config')->textjustify[$this->fields['data']['totals'][$column['id']]['datajustify']];
										$colspan = $column['col-length'];
										$celldata = '<b>'.$column['label'].'</b>: '.TableScreenMaker::generate_formattedcelldata($this->fields['data']['totals'][$column['id']]['type'], $quote['totals'], $column);
										$tb->td("colspan=$colspan|class=$class", $celldata);
									} else {
										if ($columncount < $this->tableblueprint['cols']) {
											$colspan = 1;
											$tb->td();
										};
									}
									$columncount += $colspan;
								}
							}
						}
						//$tb->tr('class=last-row-bottom');
						//$tb->td('colspan='.$this->tableblueprint['cols'],'&nbsp;');
					}
				$tb->closetablesection('tbody');
				return $tb->close();
			}
		}
	}
