<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
?>

<h2>
    <?php echo $this->resume->__toString() ?>
    <?php echo $this->translate('&#187; Photos');?>
</h2>

<?php echo $this->form->render($this) ?>