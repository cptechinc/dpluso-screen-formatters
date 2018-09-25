<?php 
    /**
     * Formatter For II Item Lot Serial
     * Formattable
     */
     class II_ItemLotSerialFormatter extends TableScreenFormatter {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ii-lot-serial'; 
		protected $title = 'Item Lot Serial';
		protected $datafilename = 'iilotser'; 
		protected $testprefix = 'iilot';
		protected $datasections = array(
			"detail" => "Detail"
		);
        
        /* =============================================================
            PUBLIC FUNCTIONS
       	============================================================ */
        public function generate_screen() {
            $bootstrap = new HTMLWriter();
            $content = '';
			$count = 0; 
            //if ($column['sortavailable'] == 'n') { $array[] = $count; }
            $tb = new Table("class=table table-striped table-bordered table-condensed table-excel|id=table");
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
				foreach ($this->json['data']['lots'] as $lot) {
                    for ($x = 1; $x < $this->tableblueprint['detail']['maxrows'] + 1; $x++) {
                        $tb->tr();
                        $columncount = 0;
                        for ($i = 1; $i < $this->tableblueprint['cols'] + 1; $i++) {
                            if (isset($this->tableblueprint['detail']['rows'][$x]['columns'][$i])) {
                                $column = $this->tableblueprint['detail']['rows'][$x]['columns'][$i];
                                $class = DplusWire::wire('config')->textjustify[$this->fields['data']['detail'][$column['id']]['datajustify']];
                                $colspan = $column['col-length'];
                                $celldata = TableScreenMaker::generate_formattedcelldata($this->fields['data']['detail'][$column['id']]['type'], $lot, $column);
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
			$tb->closetablesection('tbody');
			$content = $tb->close();
            return $content;
        }
        
        /* =============================================================
            CLASS FUNCTIONS
       	============================================================ */
        /**
         * Sets the property fields with the data sections
         * @return void
         */
        protected function load_fields() {
            $this->fields = array(
                'data' => array(
                    'detail' => array(
                        "warehouse" => array(
                            "type" => "C",
                            "heading" => "WH",
                            "headingjustify" => "l",
                            "datajustify" => "l",
                        ),
                        "warehouse city" => array(
                            "type" => "C",
                            "heading" => "Warehouse City",
                            "headingjustify" => "l",
                            "datajustify" => "l",
                        ),
                        "lot/serial" => array(
                            "type" => "C",
                            "heading" => "Lot/Serial Number",
                            "headingjustify" => "l",
                            "datajustify" => "l",
                        ),
                        "bin" => array(
                            "type" => "C",
                            "heading" => "Bin Nbr",
                            "headingjustify" => "l",
                            "datajustify" => "l",
                        ),
                        "lot reference" => array(
                            "type" => "C",
                            "heading" => "Lot Reference",
                            "headingjustify" => "l",
                            "datajustify" => "l",
                        ),
                        "expire date" => array(
                            "type" => "C",
                            "heading" => "Expire Date",
                            "headingjustify" => "r",
                            "datajustify" => "r",
                        ),
                        "available" => array(
                            "type" => "C",
                            "heading" => "Available",
                            "headingjustify" => "r",
                            "datajustify" => "r",
                        )
                    )
                )
            );
        }
    }
