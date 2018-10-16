<?php
	namespace Dplus\Dpluso\ScreenFormatters\CI;

	use Dplus\ProcessWire\DplusWire;
	use Dplus\Content\HTMLWriter;
	use Dplus\Content\Table;
	use \Customer;
	
	/**
	 * Formatter for CI Customer Shipto Screen
	 * Formattable
	 */
	class CustomerShiptoScreen extends CustomerScreen {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ci-customer-shipto-page'; // ii-sales-history
		protected $title = 'Customer Shipto Screen';
		protected $datafilename = 'cishiptoinfo'; // iisaleshist.json
		protected $testprefix = 'cicust'; // iish
		
		/* =============================================================
			PUBLIC FUNCTIONS
		============================================================ */
		public function generate_customertable(Customer $customer) {
			$tableformatter = new CI_CustomerScreen($this->sessionID);
			$tableformatter->process_json();
			return $tableformatter->generate_customertable($customer);
		}

		public function generate_shiptotable(Customer $customer) {
			$bootstrap = new HTMLWriter();
			$tb = new Table("class=table table-striped table-bordered table-condensed table-excel");
			foreach (array_keys($this->json['columns']['top']) as $column) {
				if ($this->json['columns']['top'][$column]['heading'] == '' && $this->json['data']['top'][$column] == '') {

				} else {
					$tb->tr();
					$tb->td('', $this->json['columns']['top'][$column]['heading']);
					if ($column == 'shiptoid') {
						$options = $bootstrap->option('value= ', 'No Shipto Selected');
						$shiptos = get_customershiptos($customer->custID);
						foreach ($shiptos as $shipto) {
							$show = $shipto->shiptoid.' '.$shipto->name.' - '.$shipto->city.', '.$shipto->state;
							$attr = ($shipto->shiptoid == $customer->shipID) ? "value=$shipto->shiptoid|selected" : "value=$shipto->shiptoid";
							$options .= $bootstrap->option($attr, $show);
						}
						$select = $bootstrap->create_element('select', "class=form-control input-sm|onchange=refreshshipto(this.value, '$customer->custID')", $options);
						$tb->td('', $select);
					} else {
						$tb->td('', $this->json['data']['top'][$column]);
					}
				}
			}
			$href = DplusWire::wire('config')->pages->custinfo.$customer->custID.'/';
			$tb->tr()->td('colspan=2|class=text-center', $bootstrap->a("href=$href|class=btn btn-primary", 'Clear Shipto'));
			return $tb->close();
		}

		public function generate_tableright() {
			$tb = new Table('class=table table-striped table-bordered table-condensed table-excel');

			foreach (array('activity', 'saleshistory') as $section) {
				$tb->tablesection('thead');
				$tb->tr();
				foreach ($this->json['columns']['right'][$section] as $column) {
					$class = DplusWire::wire('config')->textjustify[$column['headingjustify']];
					$tb->th("class=$class", $column['heading']);
				}
				$tb->closetablesection('thead');
				foreach (array_keys($this->json['data']['right'][$section]) as $row) {
					$tb->tr();
					foreach (array_keys($this->json['data']['right'][$section][$row]) as $column) {
						$class = DplusWire::wire('config')->textjustify[$this->json['columns']['right'][$section][$column]['datajustify']];
						$tb->td("class=$class", $this->json['data']['right'][$section][$row][$column]);
					}
				}
				$tb->tr('class=last-section-row')->td('colspan='.sizeof(array_keys($this->json['data']['right'][$section][$row])), '&nbsp;');
			}

			foreach(array('rfml', 'dateentered', 'lastsaledate') as $misc) {
				$tb->tr();
				$class = DplusWire::wire('config')->textjustify[$this->json['columns']['right'][$misc]['headingjustify']];
				$tb->td("class=$class", $this->json['columns']['right'][$misc]['heading']);
				$class = DplusWire::wire('config')->textjustify[$this->json['columns']['right'][$misc]['datajustify']];
				$tb->td("class=$class", $this->json['data']['right'][$misc])->td();
			}
			return $tb->close();
		}
	}
