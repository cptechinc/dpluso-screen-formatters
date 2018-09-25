<?php
    /**
     * Factory to load all the Screen Formatters
     */
    class ScreenFormatterFactory {
        /**
         * Session Identifier
         * @var string
         */
        protected $sessionID;
        
        /**
         * Formatter Array with code as the key and the NAme as the value
         * @var array
         */
        protected $formatters = array(
            'ii-sales-history' => 'II_SalesHistoryFormatter',
            'ii-sales-orders' => 'II_SalesOrdersFormatter',
            'ii-purchase-orders' => 'II_PurchaseOrdersFormatter',
            'ii-purchase-history' => 'II_PurchaseHistoryFormatter',
            'ii-quotes' => 'II_Quotes',
            'ii-item-page' => 'II_ItemPageFormatter',
            
            // CI
            'ci-sales-orders' => 'CI_SalesOrdersFormatter',
            'ci-sales-history' => 'CI_SalesHistoryFormatter',
            'ci-open-invoices' => 'CI_OpenInvoicesFormatter',
            'ci-payment-history' => 'CI_PaymentHistoryFormatter',
            'ci-quotes' => 'CI_QuotesFormatter',
            
            // VI 
            'vi-purchase-orders' => 'VI_PurchaseOrdersFormatter',
            'vi-purchase-history' => 'VI_PurchaseHistoryFormatter',
            'vi-payment-history' => 'VI_PaymentHistoryFormatter',
            'vi-open-invoices' => 'VI_OpenInvoicesFormatter',
            'vi-unreleased-purchase-orders' => 'VI_UnreleasedPurchaseOrdersFormatter',
            
            // NON FORMATABLE
            'ii-activity' => 'II_ItemActivityScreen',
            'ii-stock' => 'II_ItemWarehouseStockScreen',
            'ii-item-stock' => 'II_ItemStockScreen',
            'ii-requirements' => 'II_ItemRequirementsScreen',
            'ii-kit' => 'II_ItemKitScreen',
            'ii-lot-serial' => 'II_ItemLotSerialScreen',
            'ii-lot-serial' => 'II_ItemLotSerialFormatter',
            'ii-documents' => 'II_ItemDocumentScreen',
            'ii-substitutes' => 'II_ItemSubstituteScreen',
            'ii-pricing' => 'II_ItemPricingScreen',
            'ii-usage' => 'II_ItemUsageScreen',
            'ii-notes' => 'II_ItemNotesScreen',
            'ii-misc' => 'II_ItemMiscScreen',
			'ii-costing' => 'II_ItemCostingScreen',
            
            'ci-customer-page' => 'CI_CustomerScreen',
            'ci-customer-shipto-page' => 'CI_CustomerShiptoScreen',
            'ci-contacts' => 'CI_ContactsScreen',
			
			'item-pricing' => 'Item_ItemPricing',
			'item-stock' => 'Item_ItemStock',
			'item-purchasehistory' => 'Item_ItemPurchaseHistory',
			'item-kitcomponents' => 'Item_ItemKitComponents'
        );
        
        /**
         * Constructor
         * @param string $sessionID Session Identifier
         */
        public function __construct($sessionID) {
            $this->sessionID = $sessionID;
        }
        
        /**
         * Returns Screen formatter object of the type provided
         * @param  string $type Formatter Type
         * @return TableScreenMaker       Screen object
         */
        public function generate_screenformatter($type) {
            if (in_array($type, array_keys($this->formatters))) {
                return new $this->formatters[$type]($this->sessionID);
            } else {
                $this->error("Screen Formatter $type does not exist");
                return false;
            }
        }
        
        /**
         * Throws Error
         * @param  string $error Error Message
         * @param  int    $level Error Level
         * @return void
         */
        protected function error($error, $level = E_USER_ERROR) {
			$error = (strpos($error, 'DPLUSO[SCREEN-FORMATTER]: ') !== 0 ? 'DPLUSO[SCREEN-FORMATTER]: ' . $error : $error);
			trigger_error($error, $level);
			return;
		}
    } 
