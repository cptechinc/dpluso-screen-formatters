<?php 
    use Dplus\ProcessWire\DplusWire as DplusWire;
    
    /**
     * Formatter for II Item Stock Screen
     * Not Formattable
     */
     class II_ItemStockScreen extends TableScreenMaker {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ii-stock'; 
		protected $title = 'Item Stock';
		protected $datafilename = 'iistkstat'; 
		protected $testprefix = 'iiprc';
		protected $datasections = array();
        /**
         * Customer ID
         * @var string
         */
        protected $custID = false;
        
        /* =============================================================
            PUBLIC FUNCTIONS
       	============================================================= */
        public function generate_screen() {
            $bootstrap = new Dplus\Content\HTMLWriter();
            $content = '';
            $itemlink = new \Purl\Url(DplusWire::wire('config')->pages->products."redir/");
            
            $columns = array_keys($this->json['columns']);
			$tb = new Dplus\Content\Table('class=table table-striped table-condensed table-bordered table-excel');
			$tb->tablesection('thead');
				$tb->tr();
				foreach ($this->json['columns'] as $column) {
					$class = DplusWire::wire('config')->textjustify[$column['headingjustify']];
					$tb->th("class=$class", $column['heading']);
				}
			$tb->closetablesection('thead');
			$tb->tablesection('tbody');
				foreach ($this->json['data'] as $warehouse) {
					$tb->tr();
					foreach ($columns as $column) {
						$class = DplusWire::wire('config')->textjustify[$this->json['columns'][$column]['datajustify']];
						if ($column == "Item ID") {
							$itemlink->query->setData(array("action" => "ii-select", "custID" => $this->custID, 'itemID' => $warehouse[$column]));;
							$content = $bootstrap->create_element('a', "href=".$itemlink->getUrl(), $warehouse[$column]);
						} else {
							$content = $warehouse[$column];
						}
						$tb->td("class=$class", $content);
					}
				}
			$tb->closetablesection('tbody');
			return $tb->close();
        }
        
        /**
         * Sets the Cust ID property of this class
         * @param string $custID Customer ID
         */
        public function set_custid($custID) {
            $this->custID = $custID;
        }
    }
