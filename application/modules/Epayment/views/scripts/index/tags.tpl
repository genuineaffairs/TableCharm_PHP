<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Epayment
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>
<?php 
$this->headTitle('RSS Feed Popular Tags');
?>

<div class="headline">
  <h2>
    <?php echo $this->translate('RSS Feed');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>
<script type="text/javascript">
  var searchEpayments = function() {
    $('filter_form').submit();
  }
</script>
<div class='layout_right epayment_layout_right'>
  <?php echo $this->form->render($this) ?>

  <?php if( count($this->quickNavigation) > 0 ): ?>
    <div class="quicklinks">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->quickNavigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

<div class='layout_middle epayment_layout_middle'>

  <?php if (!empty($this->tags)): ?>
  
      <h3 class="sep">
        <span><?php echo $this->translate('Popular Tags'); ?></span>
      </h3>    
  
      <div class="radcodes_popular_tags epayments_popular_tags">
        <ul>
        <?php foreach ($this->tags as $k => $tag): ?>
          <li><?php echo $this->htmlLink(array(
                      'route' => 'epayment_browse',
                      'tag' => $tag->tag_id),
            $tag->text, 
            array('class'=> "tag_x tag_$k")
          )?>
          <sup><?php echo $tag->total; ?></sup>
          </li>
        <?php endforeach; ?>
        </ul>
      </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('Nobody has posted a RSS feed yet.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Be the first to <a href=\'%s\'>post</a> one!', $this->url(array('action'=>'create'), 'epayment_general', true)); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>
  
</div>

