<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Listing
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
?>

<?php $description = $this->translate('Are you sure that you want to delete "%1$s" feed last modified %2$s? It will not be recoverable after being deleted.', $this->epayment->__toString(), $this->timestamp($this->epayment->modified_date)); ?>
<?php $this->form->getDecorator('Description')->setEscape(false); echo $this->form->setDescription($description)->render($this); ?>

