<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
?>

<?php 
$baseUrl = Zend_Controller_Front::getInstance()->getRequest()->getScheme() . '://' .Zend_Controller_Front::getInstance()->getRequest()->getHttpHost();
?>

<div class="folder_file_share_link">
  <h4><?php echo $this->translate('Share This File')?></h4>
  <table>
    <tr>
      <td><?php echo $this->translate('URL:')?></td>
      <td><input type="text" value="<?php echo $baseUrl . $this->attachment->getHref(); ?>" onclick="this.focus();this.select();" readonly="readonly" /></td>
    </tr>
  </table>
</div>
