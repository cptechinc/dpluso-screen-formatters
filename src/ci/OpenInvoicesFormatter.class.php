<?php
	namespace Dplus\Dpluso\ScreenFormatters\CI;

	use Dplus\ProcessWire\DplusWire;
	use Dplus\Dpluso\ScreenFormatters\TableScreenFormatter;
	use Dplus\Content\HTMLWriter;
	use Dplus\Content\Table;
	
	/**
	 * Formatter for CI Open Invoices
	 * Formattable
	 */
	class OpenInvoicesFormatter extends TableScreenFormatter {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ci-open-invoices'; // ii-sales-history
		protected $title = 'Customer Open Invoices';
		protected $datafilename = 'ciopeninv'; // iisaleshist.json
		protected $testprefix = 'cioi'; // iish
		protected $formatterfieldsfile = 'cioifmattbl'; // iishfmtbl.json
		protected $datasections = array(
			"detail" => "Detail",
		);

		/* =============================================================
			PUBLIC FUNCTIONS
		============================================================ */
		public function generate_screen() {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->ajaxload."ci/ci-documents/order/");
			$bootstrap = new HTMLWriter();
			$this->generate_tableblueprint();

			$tb = new Table('class=table table-striped table-bordered table-condensed table-excel|id=invoices');
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
				foreach($this->json['data']['invoices'] as $invoice) {
					if ($invoice != $this->json['data']['invoices']['TOTAL']) {
						for ($x = 1; $x < $this->tableblueprint['detail']['maxrows'] + 1; $x++) {
							$tb->tr();
							$columncount = 0;
							for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
								if (isset($this->tableblueprint['detail']['rows'][$x]['columns'][$i])) {
									$column = $this->tableblueprint['detail']['rows'][$x]['columns'][$i];
									$class = DplusWire::wire('config')->textjustify[$this->fields['data']['detail'][$column['id']]['datajustify']];
									$colspan = $column['col-length'];
									$celldata = TableScreenMaker::generate_formattedcelldata($this->fields['data']['detail'][$column['id']]['type'], $invoice, $column);

									if ($i == 1 && !empty($invoice['Invoice Number'])) {
										$ordn = $invoice['Ordn'];
										$custID = $this->json['custid'];
										$url->query->setData(array('custID' => $custID, 'ordn' => $ordn, 'returnpage' => urlencode(DplusWire::wire('page')->fullURL->getUrl())));
										$href = $url->getUrl();
										$celldata .= "&nbsp; " . $bootstrap->create_element('a', "href=$href|class=load-order-documents|title=Load Order Documents|aria-label=Load Order Documents|data-ordn=$ordn|data-custid=$custID|data-type=ci-sales-history", $bootstrap->icon('fa fa-file-text'));
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
				$invoice = $this->json['data']['invoices']['TOTAL'];
				$x = 1;
				$tb->tr('class=has-warning');
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
			$tb->closetablesection('tfoot');
			return $tb->close();
		}

		public function generate_javascript() {
			if (!$this->forprint) {
				$bootstrap = new HTMLWriter();
				$content = $bootstrap->open('script', '');
					$content .= "\n";
					$content .= $bootstrap->indent().'$(function() {';
						$content .= "$('#invoices').DataTable();";
					$content .= $bootstrap->indent().'});';
					$content .= "\n";
				$content .= $bootstrap->close('script');
				return $content;
			} else {
				return '';
			}
		}
	}
