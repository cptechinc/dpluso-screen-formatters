<?php
    namespace Dplus\Dpluso\ScreenFormatters\II;
    
    use Dplus\ProcessWire\DplusWire as DplusWire;
    
    /**
     * Formatter for Item Misc Screen
     * Not Formattable
     */
     class II_ItemMiscScreen extends TableScreenMaker {
		protected $tabletype = 'normal'; // grid or normal
		protected $type = 'ii-misc'; 
		protected $title = 'Item Misc';
		protected $datafilename = 'iimisc'; 
		protected $testprefix = 'iim';
		protected $datasections = array();
        
        /* =============================================================
            PUBLIC FUNCTIONS
       	============================================================ */
        public function generate_screen() {
            return $this->generate_misctable();
        }
        
        /* =============================================================
            CLASS FUNCTIONS
       	============================================================ */
        protected function generate_misctable() {
            $bootstrap = new Dplus\Content\HTMLWriter();
            $tb = new Dplus\Content\Table('class=table table-striped table-condensed table-excel');
            foreach ($this->json['data'] as $misc) {
                foreach (array_keys($this->json['columns']['misc info']) as $column) {
                    $tb->tr();
                    $tb->td('', $this->json['columns']['misc info'][$column]['heading']);
                    $class = DplusWire::wire('config')->textjustify[$this->json['columns']['misc info'][$column]['datajustify']];
                    $tb->td("class=$class", $misc[$column]);
                }
            }
        }
    }
