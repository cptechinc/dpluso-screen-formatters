<?php
	namespace Dplus\Dpluso\ScreenFormatters\CI;
	
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Content\HTMLWriter;
	use Dplus\Content\Table;
	use Dplus\Dpluso\ScreenFormatters\TableScreenMaker;
	
	/**
	 * Formatter for the CI Contacts Screen
	 * Not Formatterable
	 */
	class ContactsScreen extends TableScreenMaker {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ci-contacts'; // ii-sales-history
		protected $title = 'Customer Contacts';
		protected $datafilename = 'cicontact'; // iisaleshist.json
		protected $testprefix = 'cicont'; // iish
		
		/* =============================================================
			PUBLIC FUNCTIONS
		============================================================ */
		public function generate_screen() {
			$bootstrap = new HTMLWriter();
			$content = '';
			
			if (sizeof($this->json['data']) > 0) {
				$content .= $bootstrap->open('div','class=row');
					$content .= $bootstrap->open('div','class=col-sm-6'); // CUSTOMER LEFT
						$tb = new Table('class=table table-striped table-bordered table-condensed table-excel');
						foreach (array_keys($this->json['columns']['customer']['customerleft']) as $column) {
							$tb->tr();
							$tb->td('class='.DplusWire::wire('config')->textjustify[$this->json['columns']['customer']['customerleft'][$column]['headingjustify']], $this->json['columns']['customer']['customerleft'][$column]['heading']);
							$tb->td('class='.DplusWire::wire('config')->textjustify[$this->json['columns']['customer']['customerleft'][$column]['datajustify']], $this->json['data']['customer']['customerleft'][$column]);
						}
						$content .= $tb->close();
					$content .= $bootstrap->close('div');
					$content .= $bootstrap->open('div','class=col-sm-6'); // CUSTOMER RIGHT
						$tb = new Table('class=table table-striped table-bordered table-condensed table-excel');
						foreach (array_keys($this->json['columns']['customer']['customerright']) as $column) {
							$tb->tr();
							$tb->td('class='.DplusWire::wire('config')->textjustify[$this->json['columns']['customer']['customerright'][$column]['headingjustify']], $this->json['columns']['customer']['customerright'][$column]['heading']);
							$tb->td('class='.DplusWire::wire('config')->textjustify[$this->json['columns']['customer']['customerright'][$column]['headingjustify']], $this->json['data']['customer']['customerright'][$column]);
						}
						$content .= $tb->close();
					$content .= $bootstrap->close('div');
				$content .= $bootstrap->close('div');
				
				$content .= $bootstrap->open('hr', '');
				
				if (isset($this->json['data']['shipto'])) {
					$content .= $bootstrap->h2('', 'Ship-To Contact Info');
					$content .= $this->generate_shiptosection();
				}

				if (isset($this->json['data']['contact'])) {
					$content .= $this->generate_contactsection();
				}
				
				if (isset($this->json['columns']['forms'])) { 
					$content .= $this->generate_formsection();
				}
			} else {
				$content = $page->bootstrap->alertpanel('warning', 'Information Not Available'); 
			} // END if (sizeof($this->json['data']) > 0)
			return $content;
		}
		
		protected function generate_shiptosection() {
			$bootstrap = new HTMLWriter();
			$content = '';
			foreach ($this->json['data']['shipto'] as $shipto) {
				$content .= $bootstrap->h3('',$shipto['shiptoleft']['shiptoid'].' - '.$shipto['shiptoleft']['shiptoname']);
				//foreach ($shipto['shiptocontacts'] as $contact) {
					$content .= $bootstrap->open('div','class=row');
						$content .= $bootstrap->open('div','class=col-sm-6'); // CUSTOMER LEFT
							$tb = new Table('class=table table-striped table-bordered table-condensed table-excel');
							foreach (array_keys($this->json['columns']['shipto']['shiptoleft']) as $column) {
								$tb->tr();
								$tb->td('class='.DplusWire::wire('config')->textjustify[$this->json['columns']['shipto']['shiptoleft'][$column]['headingjustify']], $this->json['columns']['shipto']['shiptoleft'][$column]['heading']);
								$tb->td('class='.DplusWire::wire('config')->textjustify[$this->json['columns']['shipto']['shiptoleft'][$column]['datajustify']], $shipto['shiptoleft'][$column]);
							}
							$content .= $tb->close();
						$content .= $bootstrap->close('div');
						$content .= $bootstrap->open('div','class=col-sm-6'); // CUSTOMER RIGHT
							$tb = new Table('class=table table-striped table-bordered table-condensed table-excel');
							foreach (array_keys($this->json['columns']['shipto']['shiptoright']) as $column) {
								$tb->tr();
								$tb->td('class='.DplusWire::wire('config')->textjustify[$this->json['columns']['shipto']['shiptoright'][$column]['headingjustify']], $this->json['columns']['shipto']['shiptoright'][$column]['heading']);
								$tb->td('class='.DplusWire::wire('config')->textjustify[$this->json['columns']['shipto']['shiptoright'][$column]['datajustify']], $shipto['shiptoright'][$column]);
							}
							$content .= $tb->close();
						$content .= $bootstrap->close('div');
					$content .= $bootstrap->close('div');
				//}
				
			}
			return $content;
		}
		
		protected function generate_contactsection() {
			$bootstrap = new HTMLWriter();
			$content = '';
			$content .= $bootstrap->h2('', 'Customer Contact Info');
			$tb = new Table('class=table table-striped table-bordered table-condensed table-excel');
				$tb->tablesection('thead');
				$tb->tr();
					foreach (array_keys($this->json['columns']['contact']) as $column) {
						$tb->th('class='.DplusWire::wire('config')->textjustify[$this->json['columns']['contact'][$column]['headingjustify']], $this->json['columns']['contact'][$column]['heading']);
					}
				$tb->closetablesection('thead');
				$tb->tablesection('tbody');
					foreach ($this->json['data']['contact'] as $contact) {
						$tb->tr();
						$tb->td('class='.DplusWire::wire('config')->textjustify[$this->json['columns']['contact']['contactshipto']['datajustify']], $contact['contactshipto']);
						$tb->td('class='.DplusWire::wire('config')->textjustify[$this->json['columns']['contact']['contactname']['datajustify']], $contact['contactname']);
						$tb->td('class='.DplusWire::wire('config')->textjustify[$this->json['columns']['contact']['contactemail']['datajustify']], $contact['contactemail']);
						if (isset($contact['contactnumbers']["1"]['contactnbr'])) {
							$tb->td('class='.DplusWire::wire('config')->textjustify[$this->json['columns']['contact']['contactnbr']['datajustify']], $contact['contactnumbers']["1"]['contactnbr']);
						} else {
							$tb->td();
						}
						for ($i = 1; $i < sizeof($contact['contactnumbers']) + 1; $i++) {
							if ($i != 1) {
								$tb->tr();
								$tb->td()->td();
								$tb->td('class='.DplusWire::wire('config')->textjustify[$this->json['columns']['contact']['contactnbr']['datajustify']], $contact['contactnumbers']["$i"]['contactnbr']);
							}
						}
					}
				$tb->closetablesection('tbody');
			$content .= $tb->close();
			return $content;
		}
		
		protected function generate_formsection() {
			$bootstrap = new HTMLWriter();
			$content = '';
			$content .= $bootstrap->h2('', 'Forms Information');
			$tb = new Table('class=table table-striped table-bordered table-condensed table-excel');
				$tb->tablesection('thead');
					$tb->tr();
					foreach (array_keys($this->json['columns']['forms']) as $column) {
						$tb->th('class='.DplusWire::wire('config')->textjustify[$this->json['columns']['forms'][$column]['headingjustify']], $this->json['columns']['forms'][$column]['heading']);
					}
				$tb->closetablesection('thead');
				$tb->tablesection('tbody');
					foreach ($this->json['data']['forms'] as $form) {
						$tb->tr();
						foreach (array_keys($this->json['columns']['forms']) as $column) {
							$tb->th('class='.DplusWire::wire('config')->textjustify[$this->json['columns']['forms'][$column]['datajustify']], $form[$column]);
						}
					}
				$tb->closetablesection('tbody');
			$content .= $tb->close();
			return $content;
		}
	}
