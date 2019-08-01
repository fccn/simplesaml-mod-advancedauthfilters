<?php

/**
 * Filter that validates if a list of parameters exist in the SAML attributes
 * Based on authorize:Authorize by Ernesto Revilla, Yaco Sistemas SL., Ryan Panning
 *
 * @author Paulo Costa FCT|FCCN
 * @package advancedauthfilters
 * @version $Id$
 */
class sspmod_advancedauthfilters_Auth_Process_ValidateParameters extends SimpleSAML_Auth_ProcessingFilter
{
  /**
	 * Flag to turn the REGEX pattern matching on or off
	 *
	 * @var bool
	 */
	protected $regex = TRUE;

	/**
	 * Array of valid users. Each element is a regular expression. You should
	 * user \ to escape special chars, like '.' etc.
	 *
	 */
	protected $saml_attributes = array();


	/**
	 * Initialize this filter.
	 * Validate configuration parameters.
	 *
	 * @param array $config  Configuration information about this filter.
	 * @param mixed $reserved  For future use.
	 */
	public function __construct($config, $reserved) {
    parent::__construct($config, $reserved);

		assert('is_array($config)');

    // Check for the regex option, get it and remove it
		// Must be bool specifically, if not, it might be for a attrib filter below
		if (isset($config['regex']) && is_bool($config['regex'])) {
			$this->regex = $config['regex'];
			unset($config['regex']);
		}

    foreach ($config as $attribute => $values) {
			if (is_string($values))
				$values = array($values);
			if (!is_array($values))
				throw new SimpleSAML_Error_Exception('[AdvancedAuthFilters:ValidateParameters]: Attribute is neither string nor array: ' . var_export($attribute, TRUE));
			foreach ($values as $value){
				if(!is_string($value)) {
					throw new SimpleSAML_Error_Exception('[AdvancedAuthFilters:ValidateParameters]: Each value should be a string for attribute: ' . var_export($attribute, TRUE) . ' value: ' . var_export($value, TRUE) . ' Config is: ' . var_export($config, TRUE));
				}
			}
			$this->saml_attributes[$attribute] = $values;
		}
  }

  /**
	 * Apply filter to validate SAML attributes.
	 *
	 * @param array &$request  The current request
	 */
	public function process(&$request) {
    
  }

}
