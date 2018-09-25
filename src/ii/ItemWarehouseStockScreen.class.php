<?php 
    /**
     * Formatter for II Item Warehouse Stock Screen
     * Not Formattable
     */
     class II_ItemWarehouseStockScreen extends TableScreenMaker {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ii-stock'; 
		protected $title = 'Item Stock by Warehouse';
		protected $datafilename = 'iistkbywhse'; 
		protected $testprefix = 'debugstkbywhse';

		protected $datasections = array();
        
        /* =============================================================
            PUBLIC FUNCTIONS
       	============================================================ */
        public function generate_screen() {
            $bootstrap = new HTMLWriter();
            $content = '';
            
            foreach($this->json['data'] as $whse) {
                $content .= $bootstrap->h3('', $whse['Whse Name']);
                
                // Warehouse Totals
                $tb = new Table('class=table table-striped table-bordered table-condensed table-excel');
				$tb->tablesection('thead');
					$tb->tr();
					foreach($this->json['columns']['warehouse'] as $column) {
						$class = DplusWire::wire('config')->textjustify[$column['headingjustify']];
						$tb->th("class=$class", $column['heading']);
					}
				$tb->closetablesection('thead');
				$tb->tablesection('tbody');
					$tb->tr();
					foreach(array_keys($this->json['columns']['warehouse']) as $column) {
						$class = DplusWire::wire('config')->textjustify[$this->json['columns']['warehouse'][$column]['datajustify']];
						$tb->td("class=$class", $whse[$column]);
					}
				$tb->closetablesection('tbody');
				$content .= $tb->close();
                
                // Stock By Lot 
                if (array_key_exists('lots', $whse)) {
                    $tb = new Table('class=table table-striped table-bordered table-condensed table-excel');
					$tb->tr();
					foreach ($this->json['columns']['lots'] as $column) {
						$class = DplusWire::wire('config')->textjustify[$column['headingjustify']];
						$tb->th("class=$class", $column['heading']);
					}
					
					$tb->tr();
					foreach ($this->json['columns']['orders'] as $column) {
						$class = DplusWire::wire('config')->textjustify[$column['headingjustify']];
						$tb->td("class=$class", $bootstrap->b('', $column['heading']));
					}
					
					foreach ($whse['lots'] as $lot) {
						$tb->tr();
						foreach(array_keys($this->json['columns']['lots']) as $column) {
							$class = DplusWire::wire('config')->textjustify[$this->json['columns']['lots'][$column]['datajustify']];
							$tb->td("class=$class", $lot[$column]."123");
						}
                        
						foreach($lot['orders'] as $order) {
							$tb->tr();
							foreach(array_keys($this->json['columns']['orders']) as $column) {
								$class = DplusWire::wire('config')->textjustify[$this->json['columns']['orders'][$column]['datajustify']];
								$tb->td("class=$class", $order[$column]);
							}
						}
					}
					$content .= $tb->close();
                }
			}
            return $content;
        }
    }
