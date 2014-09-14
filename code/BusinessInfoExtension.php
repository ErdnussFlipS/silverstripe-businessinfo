<?php
/**
 * Business Info Extension
 * Adds Business info to a given {@see DataObject}
 * 
 * @package businessinfo
 * @author Anselm Christophersen <ac@anselm.dk>
 * 
 */
class BusinessInfoExtension extends DataExtension {

	public static $db = array (
		'BusinessName' => 'Varchar(255)',
		'BusinessTagline' => 'Varchar(255)',
		'BusinessOwner' => 'Varchar(255)',

		'Street' => 'Varchar(255)',
		'PostalCode' => 'Varchar(10)',
		'Location' => 'Varchar(255)',
		'Country' => 'Varchar(255)',

		'Phone' => 'Varchar(255)',
		'Fax' => 'Varchar(255)',

		'MainContact' => 'Varchar(255)', //Full name of main contact
		'MainContactPhone' => 'Varchar(255)',

		'Email' => 'Varchar(255)',
//		'Website' => 'Varchar(255)',

		'VatNumber' => 'Varchar(255)',
		'TaxNumber' => 'Varchar(255)',
	);

	public function getInfoArray()
	{
		$infos = array();

		foreach (self::$db as $name => $type) {
			$var = '$'.$name;
			$val = $this->owner->{$name};

			switch ($name) {
				case 'Phone':
				case 'Fax':
				case 'VarNumber':
				case 'TaxNumber':
					$val = str_replace(' ', '&nbsp;', $val);
			}

			if (!empty($val)) {
				$infos[$var] = $val;
			}
		}

		return $infos;
	}

	private function varText($variable)
	{
		return ' {$'.$variable.'}';
	}
	
	public function updateCMSFields(FieldList $fields) {
		$fields->addFieldToTab('Root', new Tab('BusinessInfo', _t('BusinessInfo.TITLE')), 'Access');
		$fields->addFieldsToTab(
			'Root.BusinessInfo',
			array(
				LiteralField::create('Description', _t('BusinessInfo.DESCRIPTION') . '<br />' . _t('BusinessInfo.DESCRIPTIONEXAMPLE')),

				TextField::create('BusinessName', _t('BusinessInfo.BUSINESSNAME') . $this->varText('BusinessName')),
					// ->setRightTitle('$BusinessName'),
					// ->setRightTitle(_t('BusinessInfo.Description.BUSINESSNAME')),
				TextField::create('BusinessTagline', _t('BusinessInfo.BUSINESSTAGLINE') . $this->varText('BusinessTagline')),
					// ->setRightTitle('$BusinessTagline'),
					// ->setRightTitle(_t('BusinessInfo.Description.BUSINESSTAGLINE')),
				TextField::create('BusinessOwner', _t('BusinessInfo.BUSINESSOWNER') . $this->varText('BusinessOwner')),
					// ->setRightTitle('$BusinessOwner'),
					// ->setRightTitle(_t('BusinessInfo.Description.BUSINESSOWNER')),

				TextField::create('Street', _t('BusinessInfo.STREET') . $this->varText('Street')),
					// ->setRightTitle('$Street'),
					// ->setRightTitle(_t('BusinessInfo.Description.STREET')),
				TextField::create('PostalCode', _t('BusinessInfo.POSTALCODE') . $this->varText('PostalCode')),
					// ->setRightTitle('$PostalCode'),
					// ->setRightTitle(_t('BusinessInfo.Description.POSTALCODE')),
				TextField::create('Location', _t('BusinessInfo.LOCATION') . $this->varText('Location')),
					// ->setRightTitle('$Location'),
					// ->setRightTitle(_t('BusinessInfo.Description.LOCATION')),
				TextField::create('Country', _t('BusinessInfo.COUNTRY') . $this->varText('Country')),
					// ->setRightTitle('$Country'),
					// ->setRightTitle(_t('BusinessInfo.Description.BUSINESSADDRESSCOUNTRY')),

				TextField::create('Phone', _t('BusinessInfo.PHONE') . $this->varText('Phone')),
					// ->setRightTitle('$Phone'),
					// ->setRightTitle(_t('BusinessInfo.Description.PHONE')),
				TextField::create('Fax', _t('BusinessInfo.FAX') . $this->varText('Fax')),
					// ->setRightTitle('$Fax'),
					// ->setRightTitle(_t('BusinessInfo.Description.FAX')),
				
				TextField::create('MainContact', _t('BusinessInfo.MAINCONTACT') . $this->varText('MainContact')),
					// ->setRightTitle('Full name of your business\' primary contact (probably yourself) $MainContact'),
					// ->setRightTitle(_t('BusinessInfo.Description.MAINCONTACT')),
				TextField::create('MainContactPhone', _t('BusinessInfo.MAINCONTACTPHONE') . $this->varText('MainContactPhone')),
					// ->setRightTitle('$MainContactPhone'),
					// ->setRightTitle(_t('BusinessInfo.Description.MAINCONTACTPHONE')),
					// ->setRightTitle('Direct phone (mobile) of the primary contact. Can be left out if this is the same as the business\' phone.'),
				
				TextField::create('Email', _t('BusinessInfo.EMAIL') . $this->varText('Email')),
					// ->setRightTitle('$Email'),
					// ->setRightTitle(_t('BusinessInfo.Description.EMAIL')),
				// TextField::create('Website', _t('BusinessInfo.WEBSITE') . $this->varText('Website')),
					// ->setRightTitle(_t('BusinessInfo.Description.WEBSITE')),
				
				TextField::create('VatNumber', _t('BusinessInfo.VATNUMBER') . $this->varText('VatNumber')),
					// ->setRightTitle('$VatNumber'),
					// ->setRightTitle(_t('BusinessInfo.Description.VATNUMBER')),
				TextField::create('TaxNumber', _t('BusinessInfo.TAXNUMBER') . $this->varText('TaxNumber')),
					// ->setRightTitle('$TaxNumber'),
					// ->setRightTitle(_t('BusinessInfo.Description.TAXNUMBER')),
			)
		);
		
	}
	
	/**
	 * Return address so it's suitable for a one-line google maps string,
	 * stripping out all breaks, etc
	 */
	public function getAddressForGoogleMaps(){
		$address = '';

		if(!empty($value = $this->owner->Street)) $address .= $value . ', ';

		if(!empty($value = $this->owner->PostalCode)) $address .= $value;
		if(!empty($this->owner->PostalCode) && !empty($this->owner->Location)) $address .= ' ';
		if(!empty($value = $this->owner->Location)) $address .= $value;
		if(!empty($this->owner->PostalCode) || !empty($this->owner->Location)) $address .= ', ';

		if(!empty($value = $this->owner->Country)) $address .= $value;

		return $address;

		/*$address = $this->owner->Address;
		$str = str_replace(array(
			"\r\n", 
			"\r", 
			"\n",
			"'"
			), " ", $address);
		
		return $str;*/
	}

}

class PageControllerExtension extends Extension {
	public function replaceVariables()
	{
		$content = $this->owner->Content;

		foreach ($this->owner->SiteConfig()->getInfoArray() as $variable => $value) {
			$content = str_replace($variable , $value, $content);
		}

		$this->owner->Content = $content;
	}
}