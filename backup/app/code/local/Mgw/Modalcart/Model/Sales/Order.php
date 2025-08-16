<?php
class Mgw_Modalcart_Model_Sales_Order extends Mage_Sales_Model_Order{
	public function hasCustomFields(){
		$var = $this->get3rdpartyFedex();
		if($var && !empty($var)){
			return true;
		}else{
			return false;
		}
	}
	public function getFieldHtml(){
		$var = $this->get3rdpartyFedex();
		$html = '<b>Fedex/UPS 3rd Party:</b>'.$var.'<br/>';
		return $html;
	}
}