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

<?php if ($location = $this->resume->getLocation()): ?>
  <?php echo $location->formatted_address; ?>
<?php else: ?>
  <?php echo $this->resume->location; ?>
<?php endif; ?>