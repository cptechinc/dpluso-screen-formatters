<?php
	namespace Dplus\Dpluso\ScreenFormatters;
	
	/**
	 * Factory to load all the Screen Formatters
	 */
	class ScreenFormatterFactory {
		use \Dplus\Base\ThrowErrorTrait;
		
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
			'ii-sales-history' => 'SalesHistoryFormatter',
			'ii-sales-orders' => 'SalesOrdersFormatter',
			'ii-purchase-orders' => 'PurchaseOrdersFormatter',
			'ii-purchase-history' => 'PurchaseHistoryFormatter',
			'ii-quotes' => 'Quotes',
			'ii-item-page' => 'ItemPageFormatter',
			
			// CI
			'ci-sales-orders' => 'SalesOrdersFormatter',
			'ci-sales-history' => 'SalesHistoryFormatter',
			'ci-open-invoices' => 'OpenInvoicesFormatter',
			'ci-payment-history' => 'PaymentHistoryFormatter',
			'ci-quotes' => 'QuotesFormatter',
			
			// VI 
			'vi-purchase-orders' => 'PurchaseOrdersFormatter',
			'vi-purchase-history' => 'PurchaseHistoryFormatter',
			'vi-payment-history' => 'PaymentHistoryFormatter',
			'vi-open-invoices' => 'OpenInvoicesFormatter',
			'vi-unreleased-purchase-orders' => 'UnreleasedPurchaseOrdersFormatter',
			
			// NON FORMATABLE
			'ii-activity' => 'ItemActivityScreen',
			'ii-stock' => 'ItemWarehouseStockScreen',
			'ii-item-stock' => 'ItemStockScreen',
			'ii-requirements' => 'ItemRequirementsScreen',
			'ii-kit' => 'ItemKitScreen',
			'ii-lot-serial' => 'ItemLotSerialScreen',
			'ii-lot-serial' => 'ItemLotSerialFormatter',
			'ii-documents' => 'ItemDocumentScreen',
			'ii-substitutes' => 'ItemSubstituteScreen',
			'ii-pricing' => 'ItemPricingScreen',
			'ii-usage' => 'ItemUsageScreen',
			'ii-notes' => 'ItemNotesScreen',
			'ii-misc' => 'ItemMiscScreen',
			'ii-costing' => 'ItemCostingScreen',
			
			'ci-customer-page' => 'CustomerScreen',
			'ci-customer-shipto-page' => 'CustomerShiptoScreen',
			'ci-contacts' => 'ContactsScreen',
			
			'item-pricing' => 'ItemPricing',
			'item-stock' => 'ItemStock',
			'item-purchasehistory' => 'ItemPurchaseHistory',
			'item-kitcomponents' => 'ItemKitComponents'
		);
		
		/**
		 * Namespaces for each subsystem
		 * @var array
		 */
		protected $namespaces = array(
			'ii' => 'II',
			'ci' => 'CI',
			'vi' => 'VI',
			'Item' => 'Item' 
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
		 * @param  string           $formattercode Formatter Type
		 * @return TableScreenMaker                Screen Formatter object
		 */
		public function generate_screenformatter($formattercode) {
			if (in_array($formattercode, array_keys($this->formatters))) {
				$namespace = $this->get_namespace($formattercode);
				
				if ($namespace) {
					$fullnamespace = __NAMESPACE__ .  "\\$namespace\\";
					$class = $fullnamespace . $this->formatters[$formattercode];
					return new $class($this->sessionID);
				} else {
					$this->error("Namespace for Screen Formatter $formattercode does not exist");
					return false;
				}
			} else {
				$this->error("Screen Formatter $formattercode does not exist");
				return false;
			}
		}
		
		/**
		 * Returns Namespace after taking the formatter code and parsing out
		 * the namespace and validates it's a valid namespace
		 * @param  string $formattercode Formatter id / code e.g. ii-documents | ci-sales-orders
		 * @return string                Namespace for formatter
		 */
		private function get_namespace($formattercode) {
			$regex = "/-\w+/";
			$nskey = preg_replace($regex, "", $formattercode);
			
			if (in_array($nskey, array_keys($this->namespaces))) {
				return $this->namespaces[$nskey];
			} else {
				$this->error("Screen Formatter $formattercode does not exist");
				return false;
			}
		}
	} 
