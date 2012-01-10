<?php
namespace xframe\validation;

interface Validator {
	
	/**
	 * @param string $value
	 * @return boolean
	 * @throws \xframe\validation\Exception
	 */
	public function validate($value);

}
