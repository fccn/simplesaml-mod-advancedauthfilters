<?php
/**
 * Show a 403 Forbidden page with information about missing parameters.
 *
 * @author Paulo Costa FCT|FCCN
 * @package advancedauthfilters
 * @version $Id$
 */

if (!array_key_exists('StateId', $_REQUEST)) {
	throw new SimpleSAML_Error_BadRequest('Missing required StateId query parameter.');
}
$state = SimpleSAML_Auth_State::loadState($_REQUEST['StateId'], 'advancedauthfilters:CheckParameters');

$globalConfig = SimpleSAML_Configuration::getInstance();
$t = new SimpleSAML_XHTML_Template($globalConfig, 'advancedauthfilters:missing_parameters.php');
if (isset($state['Source']['auth'])) {
    $t->data['LogoutURL'] = SimpleSAML_Module::getModuleURL('core/authenticate.php', array('as' => $state['Source']['auth']))."&logout";
}
header('HTTP/1.0 403 Forbidden');
$t->show();
