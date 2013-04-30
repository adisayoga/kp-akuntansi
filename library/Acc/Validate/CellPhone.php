<?php
class Acc_Validate_CellPhone extends Zend_Validate_Abstract
{
	const NOT_PHONE = "notPhone";
	const INVALID_PHONE = "invalidPhone";
	const STRING_EMPTY = "stringEmpty";
	
	protected $_messageTemplates = array(
		self::NOT_PHONE => "'%value%' is not a phone number",
		self::INVALID_PHONE => "'%value%' must starting with 123, 213, or 231",
		self::STRING_EMPTY => "please provide a cellphone number",
	);
	
	public function isValid($value)
	{
		if (!is_string($value) && !is_int($value)) {
			$this->_error(self::NOT_PHONE);
			return false;
		}
		$this->_setValue((string) $value);
		
		$numberOnly = ereg_replace("[^0-9]", "", str_replace("-", "", $value));
		if (strlen($numberOnly) != 10) {
			$this->_error(self::NOT_PHONE);
			return false;
		}
		
		// Cell phones have an area code of 123, 213 or 231
		$areacode = substr($value, 0, 3);
		if ($areacode != 123 && $areacode != 213 && $areacode != 231) {
			$this->_error(self::INVALID_PHONE);
			return false;
		}
		
		$this->_setValue((string) value);
		return true;
	}
}