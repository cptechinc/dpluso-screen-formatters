<?php
	namespace Dplus\Dpluso\ScreenFormatters\II;
	
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Content\HTMLWriter;
	use Dplus\Content\Table;
	use Dplus\Dpluso\ScreenFormatters\TableScreenMaker;
	use Dplus\Content\JavaScripter;
	
	/**
	 * Formatter for II Item Usage Screen
	 * Not Formattable
	 */
	 class ItemUsageScreen extends TableScreenMaker {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ii-usage'; 
		protected $title = 'Item Usage';
		protected $datafilename = 'iiusage'; 
		protected $testprefix = 'iiu';
		protected $datasections = array();
		
		/* =============================================================
			PUBLIC FUNCTIONS
		============================================================ */
		public function generate_screen() {
			$bootstrap = new HTMLWriter();
			$content = $this->generate_salesusagetable();
			
			foreach (array_keys($this->json['data']['24month']) as $warehouse) {
				if ($warehouse != 'zz') {
					$content .= $this->generate_warehousediv($warehouse);
				}
			}
			$content .= $this->generate_warehousediv('zz');
			return $content;
		}
		
		public function generate_javascript() {
			$bootstrap = new HTMLWriter();
			$script = new JavaScripter(false);
			$content = $bootstrap->open('script', '');
				$content .= "\n";
				$script->generate_onready();
				
				foreach (array_keys($this->json['data']['24month']) as $warehouse) {
						$script->generate_functioncall("$('a[href=#$warehouse-graph]').on('shown.bs.tab', function(e) {");
							$script->generate_functioncall('new Morris.Line({');
								$script->line("element: '$warehouse-chart',");
								$script->line("data: ");
									$script->tabs++;
									$monthdata = array();
									foreach ($this->json['data']['24month'][$warehouse]['months'] as $month) {
										$month['month'] = ($month['month'] == 'Current') ? date('Y-m') : str_replace(' ', ' 20', $month['month']);
										$data = array(
											'month' => date('Y-m', strtotime($month['month'])),
											'saleamount' => (float)$month['sale amount'],
											'usageamount' => (float)$month['usage amount']
										);
										if (isset($month['lost amount'])) {
											$data['lostamount'] = (float)$month['lost amount'];
										}
										$monthdata[] = $data;
									}
									$script->line(json_encode($monthdata) .",");
									$script->tabs--;
								$script->line("xLabelFormat: function (x) {  ");
									$script->tabs++;
									$script->line("return  moment(x).format('MMM YYYY');");
									$script->tabs--;
								$script->line("},");
								
								$script->line("yLabelFormat: function (y) {");
									$script->tabs++;
									$script->line("return '$ '+y.formatMoney()+ ' dollars';");
									$script->tabs--;
								$script->line("},");
								
								$script->line("xkey: 'month',");
								
								$script->line('ykeys: [');
									$script->tabs++;
										$script->line("'saleamount',");
										if (isset($month['lost amount'])) {
											$script->line("'lostamount',");
										}
										$script->line("'usageamount'");
									$script->tabs--;
								$script->line('],');
								$script->line('labels: [');
									$script->tabs++;
										$script->line("'Amount Sold',");
										if (isset($month['lost amount'])) {
											$script->line("'Amount Lost',");
										}
										$script->line("'Amount Used'");
									$script->tabs--;
								$script->line('],');
								$script->line("dateFormat: function (d) {");
									$script->tabs++;
									$script->line("var ds = new Date(d);");
									$script->line("return moment(ds).format('MMM YYYY');");
									$script->tabs--;
								$script->line("}");
							$script->close_functioncall(); // CLOSES MORRIS.LINE
						$script->close_functioncall(); // CLOSES WAREHOUSE GRAPH
					
					$script->generate_functioncall("$('a[href=#$warehouse-graph]').on('hidden.bs.tab', function(e) {");
						$script->line("$('#$warehouse-chart').empty();");
					$script->close_functioncall();
					
					$script->generate_functioncall("$('#$warehouse').DataTable({");
						$script->line('order: [[0, "desc"]],');
						$script->line("columnDefs: [ {");
							$script->tabs++;
							$script->line('targets: 0,');
							$script->line("render: $.fn.dataTable.render.moment('MMM YYYY')");
							$script->tabs--;
						$script->line('},');
						$script->line("{");
							$script->tabs++;
							$script->line('targets: 2,');
							$script->line("render: function(d) {
								return parseFloat(d).formatMoney();
							}");
							$script->tabs--;
						$script->line('},');
						$script->line("{");
							$script->tabs++;
							$script->line('targets: 3,');
							$script->line("render: function(d) {
								return parseFloat(d).formatMoney();
							}");
							$script->tabs--;
						$script->line('},');
						$script->line("{");
							$script->tabs++;
							$script->line('targets: 6,');
							$script->line("render: function(d) {
								return parseFloat(d).formatMoney();
							}");
							$script->tabs--;
						$script->line('} ]');
					$script->close_functioncall();
				}
				$script->close_functioncall(); 
				$content .= $script->__toString();
			$content .= $bootstrap->close('script');
			return $content;
		}
		
		/**
		 * Returns an HTML Table with Item Information
		 * @return string HTML Table with Item Information
		 */
		public function generate_iteminfotable() {
			$bootstrap = new HTMLWriter();
			if ($this->json['error']) {
				return $bootstrap->alertpanel('warning', $this->json['errormsg']);
			} else {
				$tb = new Table('class=table table-striped table-bordered table-condensed table-excel');
				$tb->tr()->td('', $bootstrap->b('', 'Item ID:'))->td('', $this->json['itemid'])->td('colspan=2', $this->json['desc1']);
				$tb->tr()->td('', $bootstrap->b('', 'Sales UoM:'))->td('', $this->json['sale uom'])->td('colspan=2', $this->json['desc2']);
				$tb->tr()->td('', $bootstrap->b('', 'Last Sale Date:'))->td('colspan=3', $this->json['last sale date']);
				$tb->tr()->td('', $bootstrap->b('', 'Last Usage Date:'))->td('colspan=3', $this->json['last usage date']);
				return $tb->close();
			}
		}
		
		/* =============================================================
			CLASS FUNCTIONS
		============================================================ */
		/**
		 * Returns an HTML Table with Sales Usage Data
		 * @return string HTML Table with Item Information
		 */
		protected function generate_salesusagetable() {
			$tb = new Table('class=table table-striped table-bordered table-condensed table-excel no-bottom');
			$tb->tablesection('thead');
			$tb->tr();
			foreach  ($this->json['columns']['sales usage'] as $column) {
				$class = DplusWire::wire('config')->textjustify[$column['headingjustify']];
				$tb->th("class=$class", $column['heading']);
			}
			$tb->closetablesection('thead');
			
			$tb->tablesection('tbody');
			foreach ($this->json['data']['sales usage'] as $salesusage)  {
				$tb->tr();
				foreach (array_keys($this->json['columns']['sales usage']) as $column) {
					$class = DplusWire::wire('config')->textjustify[$this->json['columns']['sales usage'][$column]['datajustify']];
					$tb->td("class=$class", $salesusage[$column]);
				}
			}
			$tb->closetablesection('tbody');
			return $tb->close();
		}
		
		/**
		 * Returns HTML for the Warehouse Data
		 * @param  string $whse Warehouse ID
		 * @return string       HTML for the Warehouse Data
		 */
		protected function generate_warehousediv($whse) {
			$bootstrap = new HTMLWriter();
			$heading = $bootstrap->h3('', $this->json['data']['24month'][$whse]['whse name']);
			if ($this->forprint) {
				$tablediv = $bootstrap->div("role=tabpanel|class=tab-pane active|id=$whse-table", $this->generate_warehousetable($whse));
				$graphdiv = $bootstrap->div("role=tabpanel|class=tab-pane|id=$whse-graph", $bootstrap->div("id=$whse-chart", ' '));
				$tabcontent = $bootstrap->div('class=tab-content', $tablediv . $graphdiv);
				return $heading . $bootstrap->div('', $tabcontent);
			} else {
				$tablist = $bootstrap->li('role=presentation|class=active', $bootstrap->a("href=#$whse-table|aria-controls=$whse-table|role=tab|data-toggle=tab", 'Table'));
				$tablist .= $bootstrap->li('role=presentation', $bootstrap->a("href=#$whse-graph|aria-controls=$whse-table|role=tab|data-toggle=tab", 'Graph'));
				$tabnav = $bootstrap->ul('class=nav nav-tabs|role=tablist', $tablist);
				$tablediv = $bootstrap->div("role=tabpanel|class=tab-pane active|id=$whse-table", $this->generate_warehousetable($whse));
				$graphdiv = $bootstrap->div("role=tabpanel|class=tab-pane|id=$whse-graph", $bootstrap->div("id=$whse-chart", ' '));
				$tabcontent = $bootstrap->div('class=tab-content', $tablediv . $graphdiv);
				return $heading . $bootstrap->div('', $tabnav . $tabcontent);
			}
		}
		
		/**
		 * Returns an HTML table with Warehouse Item Data
		 * @param  string $whse Warehouse ID
		 * @return string       HTML table with Warehouse Item Data
		 */
		protected function generate_warehousetable($whse) {
			$bootstrap = new HTMLWriter();
			$tb = new Table("class=table table-striped table-bordered table-condensed table-excel no-bottom|id=$whse");
			$tb->tablesection('thead');
			$tb->tr();
			foreach  ($this->json['columns']['24month'] as $column) {
				$class = DplusWire::wire('config')->textjustify[$column['headingjustify']];
				$tb->th("class=$class", $column['heading']);
			}
			$tb->closetablesection('thead');
			$tb->tablesection('tbody');
				foreach ($this->json['data']['24month'][$whse]['months'] as $month) {
					$tb->tr();
					foreach (array_keys($this->json['columns']['24month']) as $column) {
						$month['month'] = ($month['month'] == 'Current') ? date('Y-m-01') : date('Y-m-01', strtotime(str_replace(' ', ' 20', $month['month'])));
						$class = DplusWire::wire('config')->textjustify[$this->json['columns']['24month'][$column]['datajustify']];
						$tb->td("class=$class", $month[$column]);
					}
				}
			$tb->closetablesection('tbody');
			return $tb->close();
		}
	}
