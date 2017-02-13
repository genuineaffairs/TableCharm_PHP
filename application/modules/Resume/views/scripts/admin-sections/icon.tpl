<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @section   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>
<?php 
/*
$this->section->getType() = article_section
$this->section->getType(true) = ArticleSection
$this->section->getShortType() = Section
$this->section->getShortType(true) = Section
$this->section->getModuleName() = Article

echo '<br>$this->section->getType() = ' .$this->section->getType();
echo '<br>$this->section->getType(true) = ' .$this->section->getType(true);
echo '<br>$this->section->getShortType() = ' .$this->section->getShortType(tue);
echo '<br>$this->section->getShortType(true) = ' .$this->section->getShortType(true);
echo '<br>$this->section->getModuleName() = ' . $this->section->getModuleName();
echo '<br>$this->section->getModuleItemType() = ' . $this->section->getModuleItemType();
 */
?>
<div class="global_form_popup">
<p>thumb.mini</p>
<?php echo $this->itemPhoto($this->section, 'thumb.mini') ?>
<p>thumb.icon</p>
<?php echo $this->itemPhoto($this->section, 'thumb.icon') ?>
<p>thumb.normal</p>
<?php echo $this->itemPhoto($this->section, 'thumb.normal') ?>
<p>thumb.profile</p>
<?php echo $this->itemPhoto($this->section, 'thumb.profile') ?>
<br />
<p><?php echo $this->translate('Sample PHP Usage:')?>
<br />
<code style="white-space: nowrap;">&lt;?php echo $this-&gt;itemPhoto($section, 'thumb.normal'); ?&gt;</code>
</div>

<?php echo $this->form->setAction($this->url(array('action'=>'delete-photo')))->render($this) ?>