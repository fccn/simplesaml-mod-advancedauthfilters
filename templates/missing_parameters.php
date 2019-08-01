<?php
/**
 * Default template that is shown when there are SAML parameters missing
 *
 * Parameters:
 *
 * @author Paulo Costa FCT|FCCN
 * @package advancedauthfilters
 * @version $Id$
 */


$this->data['403_header'] = $this->t('{authorize:Authorize:403_header}');
$this->data['403_text'] = $this->t('{authorize:Authorize:403_text}');

$this->includeAtTemplateBase('includes/header.php');
?>
<h1><?php echo $this->data['403_header']; ?></h1>
<p><?php echo $this->data['403_text']; ?></p>
<?php
if (isset($this->data['LogoutURL'])) {
?>
<p><a href="<?php echo htmlspecialchars($this->data['LogoutURL']); ?>"><?php echo $this->t('{status:logout}'); ?></a></p>
<?php
}
?>
<?php
$this->includeAtTemplateBase('includes/footer.php');
