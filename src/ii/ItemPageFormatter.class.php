<?php
	use Dplus\ProcessWire\DplusWire as DplusWire;
	
	/**
	 * Formatter for the II Item Page
	 * Formattable
	 */
	class II_ItemPageFormatter extends TableScreenFormatter {
        protected $tabletype = 'grid'; // grid or normal
		protected $type = 'ii-item-page'; // ii-sales-history
		protected $title = 'Item Page';
		protected $datafilename = 'iiitem'; // iisaleshist.json
		protected $testprefix = 'iiitemid'; // iish
		protected $formatterfieldsfile = 'iihfmattbl'; // iishfmtbl.json
		protected $datasections = array(
			"header" => "Header"
		);
		
		/* =============================================================
            PUBLIC FUNCTIONS
       	============================================================ */
        public function generate_screen() {
            $bootstrap = new Dplus\Content\HTMLWriter();
            $content = '';
			$this->generate_tableblueprint();
			$item = XRefItem::load($this->json['itemid']);
			
			if (DplusWire::wire('pages')->get('/config/item-information/')->show_itemimages) {
				$imagediv = $bootstrap->div('class=col-sm-4 form-group', $bootstrap->img("src=".$item->generate_itemimagesource()."|class=img-responsive|data-desc=$item->itemid image"));
				$itemform = $bootstrap->div('class=col-sm-8 form-group', $this->generate_itemformsection());
				$content .= $bootstrap->div('class=row', $imagediv . $itemform);
			} else {
				$itemform = $bootstrap->div('class=col-xs-12 form-group', $this->generate_itemformsection());
				$content .= $bootstrap->div('class=row', $itemform);
			}
			
			$content .= $bootstrap->div('class=row', $this->generate_othersections());
            return $content;
        }
		
		/* =============================================================
            CLASS FUNCTIONS
       	============================================================ */
		/**
		 * Returns the table that contains the Item Form
		 * @return string HTML Table
		 */
		protected function generate_itemformsection() {
			$bootstrap = new Dplus\Content\HTMLWriter();
			$itemID = $this->json['itemid'];
			$custID = DplusWire::wire('input')->get->text('custID');
			$shipID = DplusWire::wire('input')->get->text('shipID');
			$tb = new Dplus\Content\Table('class=table table-striped table-bordered table-condensed table-excel');

			foreach ($this->tableblueprint['header']['sections']['1'] as $column) {
				$tb->tr();
				$class = DplusWire::wire('config')->textjustify[$this->fields['data']['header'][$column['id']]['headingjustify']];
				$colspan = $column['col-length'];
				$tb->td("colspan=$colspan|class=$class", $bootstrap->b('', $column['label']));
				$class = DplusWire::wire('config')->textjustify[$this->fields['data']['header'][$column['id']]['datajustify']];
				$colspan = $column['col-length'];
				
				if ($column['id'] == 'Item ID') {
					$action = DplusWire::wire('config')->pages->ajax."load/ii/search-results/modal/";
					$form = new Dplus\Content\FormMaker("action=$action|method=POST|id=ii-item-lookup|class=allow-enterkey-submit");
					$form->input('type=hidden|name=action|value=ii-item-lookup');
					$form->input("type=hidden|name=custID|class=custID|value=$custID");
					$form->input("type=hidden|name=shipID|class=shipID|value=$shipID");
					$form->div('class=form-group', false);
						$form->div('class=input-group custom-search-form', false);
							$form->input("type=text|class=form-control not-round itemID|name=itemID|placeholder=Search ItemID, X-ref|value=$itemID");
							$button = $form->bootstrap->button('type=submit|class=btn btn-default not-round', $form->bootstrap->icon('glyphicon glyphicon-search'));
							$form->span('class=input-group-btn', $button);
						$form->close('div');
					$form->close('div');
					$form->input('type=hidden|class=prev-itemID|value='.XRefItem::get_nextorpreviousitemid($itemID, "prev"));
					$form->input('type=hidden|class=next-itemID|value='.XRefItem::get_nextorpreviousitemid($itemID, "next"));
					$celldata = $form->finish();
				} else {
					$celldata = Table::generatejsoncelldata($this->fields['data']['header'][$column['id']]['type'], $this->json['data'], $column);
				}
				$tb->td("colspan=$colspan|class=$class", $celldata);
			}
			return $tb->close();
		}
		
		/**
		 * Returns the bottom portion of the II item page screen
		 * @return string HTML
		 */
		protected function generate_othersections() {
			$bootstrap = new Dplus\Content\HTMLWriter();
			$content = '';
			
			for ($i = 2; $i < 5; $i++) {
				$content .= $bootstrap->open('div', 'class=col-sm-4 form-group');
				$tb = new Dplus\Content\Table('class=table table-striped table-bordered table-condensed table-excel');

				foreach ($this->tableblueprint['header']['sections']["$i"] as $column) {
					$tb->tr();
					$class = DplusWire::wire('config')->textjustify[$this->fields['data']['header'][$column['id']]['headingjustify']];
					$colspan = $column['col-length'];
					$tb->td("colspan=$colspan|class=$class", $bootstrap->b('', $column['label']));

					$class = DplusWire::wire('config')->textjustify[$this->fields['data']['header'][$column['id']]['datajustify']];
					$colspan = $column['col-length'];
					$celldata = Table::generatejsoncelldata($this->fields['data']['header'][$column['id']]['type'], $this->json['data'], $column);
					$tb->td("colspan=$colspan|class=$class", $celldata);
				}
				$content .=  $tb->close();
				$content .=  $bootstrap->close('div');
			}
			return $content;
		}
		
		/**
		 * Generates the table blueprint
		 * This page divides the Item Page Screen into 4 sections / columns
		 * @return void
		 */
		protected function generate_tableblueprint() {
			$table = array(
				'header' => array(
					'sections' => array(
						'1' => array(),
						'2' => array(),
						'3' => array(),
						'4' => array()
					)
				)
			);
			
			for ($i = 1; $i < 5; $i++) {
				foreach(array_keys($this->formatter['header']['columns']) as $column) {
					if ($this->formatter['header']['columns'][$column]['column'] == $i) {
						$col = array(
							'id' => $column,
							'label' => $this->formatter['header']['columns'][$column]['label'],
							'column' => $this->formatter['header']['columns'][$column]['column'],
							'col-length' => $this->formatter['header']['columns'][$column]['col-length'],
							'before-decimal' => $this->formatter['header']['columns'][$column]['before-decimal'],
							'after-decimal' => $this->formatter['header']['columns'][$column]['after-decimal'],
							'date-format' => $this->formatter['header']['columns'][$column]['date-format']
						 );
						$table['header']['sections'][$i][$this->formatter['header']['columns'][$column]['line']] = $col;
					}
				}
			}
			$this->tableblueprint = $table;
        }
    }
