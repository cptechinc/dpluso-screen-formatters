<?php 
    /**
     * Formatter for II Item Kit Screen
     * Not Formattable
     */
     class II_ItemKitScreen extends TableScreenMaker {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ii-kit'; 
		protected $title = 'Item Kit Components';
		protected $datafilename = 'iikit'; 
		protected $testprefix = 'iikt';
		protected $datasections = array();
        
        /* =============================================================
            PUBLIC FUNCTIONS
       	============================================================ */
        public function generate_screen() {
            $bootstrap = new HTMLWriter();
            $content = $bootstrap->p('', $bootstrap->b('', 'Kit Qty:') . " " . $this->json['qtyneeded']);
            
            foreach ($this->json['data']['component'] as $component) {
                $content .= $bootstrap->h3('', $component['component item']);
                
                $tb = new Table('class=table table-striped table-bordered table-condensed table-excel no-bottom');
				$tb->tablesection('thead');
					$tb->tr();
					foreach($this->json['columns']['component'] as $column) {
						$class = DplusWire::wire('config')->textjustify[$column['headingjustify']];
						$tb->th("class=$class", $column['heading']);
					}
				$tb->closetablesection('thead');
				$tb->tablesection('tbody');
					$tb->tr();
					foreach (array_keys($this->json['columns']['component']) as $column) {
						$class = DplusWire::wire('config')->textjustify[$this->json['columns']['component'][$column]['datajustify']];
						$tb->td("class=$class", $component[$column]);
					}
				$tb->closetablesection('tbody');
				$content .= $tb->close();
				
                // Warehouse Table 
				$tb = new Table('class=table table-striped table-bordered table-condensed table-excel');
				$tb->tablesection('thead');
					$tb->tr();
					foreach($this->json['columns']['warehouse'] as $column) {
						$class = DplusWire::wire('config')->textjustify[$column['headingjustify']];
						$tb->th("class=$class", $column['heading']);
					}
				$tb->closetablesection('thead');
				$tb->tablesection('tbody');
					foreach ($component['warehouse'] as $whse) {
						foreach (array_keys($this->json['columns']['warehouse']) as $column) {
							$class = DplusWire::wire('config')->textjustify[$this->json['columns']['warehouse'][$column]['datajustify']];
							$tb->td("class=$class", $whse[$column]);
						}
					}
				$tb->closetablesection('tbody');
				$content .= $tb->close();
            } // foreach ($this->json['data']['component'] as $component)
            
            $warehouses = '';
            
			foreach ($this->json['data']['whse meeting req'] as $whse => $name) {
				$warehouses .= $name . ' ';
			}
            $bootstrap->p('', $bootstrap->b('', 'Warehouses that meet the Requirement:'). " $warehouses");
            return $content;
        }
    }
