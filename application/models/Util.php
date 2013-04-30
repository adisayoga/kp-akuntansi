<?php
class Model_Util {
	
	/**
	 * Implode array field menjadi string.
	 * @param array $fields
	 * @param string $tablePrefix - OPTIONAL Nama tabel prefix
	 * @return string
	 */
	public static function implodeField($fields, $tablePrefix = "")
	{
		$result = "";
		$prefix = ($tablePrefix)? "$tablePrefix.": "";
		
		foreach ($fields as $field) {
			if ($field) {
				if ($result) $result .= ", ";
				if ($field instanceof Zend_Db_Expr) {
					$result .= $field->__toString();
				} else {
					$result .= $prefix . $field;
				}
			}
		}
		
		return $result;
	}
	
}