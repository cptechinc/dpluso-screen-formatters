<?php 
    use Dplus\ProcessWire\DplusWire as DplusWire;
    
    /**
     * Formatter for II Item Substitute Screen
     * Not Formattable
     */
     class II_ItemSubstituteScreen extends TableScreenMaker {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ii-substitute'; 
		protected $title = 'Item Substitute';
		protected $datafilename = 'iisub'; 
		protected $testprefix = 'iisub';
		protected $datasections = array();
        
        /* =============================================================
            PUBLIC FUNCTIONS
       	============================================================ */
        public function generate_screen() {
            $bootstrap = new Dplus\Content\HTMLWriter();
            $content = '';
            
            $content .= $bootstrap->open('div', 'class=row');
                $content .= $bootstrap->open('div', 'class=col-sm-6');
                    $content .= $this->generate_itemtable();
                $content .= $bootstrap->close('div');
                
                $content .= $bootstrap->open('div', 'class=col-sm-6');
                    $content .= $this->generate_saletable();
                $content .= $bootstrap->close('div');
            $content .= $bootstrap->close('div');
            
            $content .= $this->generate_substitutetable();
            return $content;
        }
        
        /* =============================================================
            PROTECTED FUNCTIONS
       	============================================================ */
        /**
         * Returns HTML table for Item Summary
         * @return string HTML table for Item Summary
         */
        protected function generate_itemtable() {
            $tb = new Dplus\Content\Table('class=table table-striped table-bordered table-condensed table-excel');
        	$tb->tr();
        	$tb->td('', 'Item ID:');
        	$content = $this->json['itemid'] . "<br>";
        	$content .= $this->json['desc1'] . "<br>";
        	
        	if (isset($this->json['alt item'])) {
        		$content .= "<b>Alt Item ID:</b> ".$this->json['alt item'];
        	} else {
        		$content .= $this->json['desc2'];
        	}
        	
        	$tb->td('', $content);
        	return $tb->close();
        }
        
        /**
         * Returns HTML table for Item UoM and Pricing
         * @return string HTML table for Item UoM and Pricing
         */
        protected function generate_saletable() {
            $tb = new Dplus\Content\Table('class=table table-striped table-bordered table-condensed table-excel');
        	$tb->tr();
        	$tb->td('', 'Sale UoM');
        	$tb->td('', $this->json['sale uom']);
        	$tb->tr();
        	$tb->td('', 'Base Price');
        	$tb->td('', $this->json['base price']);
        	return $tb->close();
        }
        
        /**
         * Returns HTML table for Item substitutes
         * @return string HTML table for Item Usubstitutes
         */
        protected function generate_substitutetable() {
            $tb = new Dplus\Content\Table('class=table table-striped table-bordered table-condensed table-excel');
        	$tb->tablesection('thead');
        		$tb->tr();
        		foreach ($this->json['columns'] as $column) {
        			$class = DplusWire::wire('config')->textjustify[$column['headingjustify']];
        			$tb->td("class=$class", $column['heading']);
        		}
        	$tb->closetablesection('thead');

        	$tb->tablesection('tbody');
        		foreach ($this->json['data']['sub items'] as $item) {
        			$tb->tr();
        			$class = DplusWire::wire('config')->textjustify[$this->json['columns']["sub item"]['datajustify']];
        			$tb->td("colspan=2|class=$class", $item["sub item"]);
        			$tb->td('', $item['same/like']);
        			$colspan = sizeof($this->json['columns']) - 3;
        			$tb->td("colpan=$colspan", $item['sub desc']);
        			
        			if (isset($item['alt items'])) {
        				$tb->td('colspan=2', '&nbsp; &nbsp; &nbsp; &nbsp;'.$item["alt items"]["alt item"]);
        				$colspan = sizeof($columns) - 2;
        				$tb->td("colpan=$colspan", $item["alt items"]["bag qty"]);
        			}
        			
        			foreach ($item['whse'] as $whse) {
        				$tb->tr();
        				foreach(array_keys($this->json['columns']) as $column) {
        					if ($column == 'sub item') {
        						$tb->td();
        					} else {
        						$class = DplusWire::wire('config')->textjustify[$this->json['columns'][$column]['datajustify']];
        						$tb->td("class=$class", $whse[$column]);
        					}
        				}
        			}
        		}
        	$tb->closetablesection('tbody');
        	return $tb->close();
        }
    }
