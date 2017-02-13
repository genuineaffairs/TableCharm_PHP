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
<?php $resume = $this->resume; ?>

<div class="resume_profile_icon_package">
  <?php echo $this->htmlLink($this->subject()->getPackage()->getHref(), $this->itemPhoto($this->subject()->getPackage(), 'thumb.profile'))?>
</div>
