<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 */
?>

<?php if(!empty($this->sitepageevent_info_collapsible)) :?>
<div class="sm_ui_item_profile_details" data-role="collapsible" id="collapsibles" <?php if(!empty($this->sitepageevent_info_collapsible_default)):?> data-collapsed='false' <?php else:?> data-collapsed='true' <?php endif;?> data-mini="true">
  <h3><?php echo $this->translate('Page Event Details');?></h3>
<?php else:?>
<div class="sm_ui_item_profile_details">
<?php endif;?>
	<table>
		<tbody>
			<?php if( !empty($this->sitepageevent_subject->description) ): ?>
				<tr valign="top">
					<td class="label"><div><?php echo $this->translate('Details') ?></div></td>
					<td><?php echo nl2br($this->sitepageevent_subject->description) ?></td>
				</tr>
    	<?php endif ?>   	
      <?php
        // Convert the dates for the viewer
        $startDateObject = new Zend_Date(strtotime($this->sitepageevent_subject->starttime));
        $endDateObject = new Zend_Date(strtotime($this->sitepageevent_subject->endtime));
        if( $this->viewer() && $this->viewer()->getIdentity() ) {
          $tz = $this->viewer()->timezone;
          $startDateObject->setTimezone($tz);
          $endDateObject->setTimezone($tz);
        }
      ?>
		  <?php if( $this->sitepageevent_subject->starttime == $this->sitepageevent_subject->endtime ): ?>
		  	<tr valign="top">
    			<td class="label"><div><?php echo $this->translate('Date') ?></div></td>
    			<td>
	          <?php echo $this->locale()->toDate($startDateObject) ?>
	        </td>
	      </tr> 
	      
		  	<tr valign="top">
    			<td class="label"><div><?php echo $this->translate('Time') ?></div></td>
    			<td>
	          <?php echo $this->locale()->toTime($startDateObject) ?>
	        </td>
	      </tr> 
		  <?php elseif( $startDateObject->toString('y-MM-dd') == $endDateObject->toString('y-MM-dd') ): ?>
		  	<tr valign="top">
    			<td class="label"><div><?php echo $this->translate('Date') ?></div></td>
    			<td>
	          <?php echo $this->locale()->toDate($startDateObject) ?>
	        </td>
	      </tr> 
		  	<tr valign="top">
    			<td class="label"><div><?php echo $this->translate('Time') ?></div></td>
    			<td>
	          <?php echo $this->locale()->toTime($startDateObject) ?>
		          -
		          <?php echo $this->locale()->toTime($endDateObject) ?>
	        </td>
	      </tr>
	    <?php else: ?>  
	    	<tr valign="top">
	    		<td class="label"><div><?php echo $this->translate('When') ?></div></td>
	    		<td>
		        <div class="event_stats_content">
		          <?php echo $this->translate('%1$s at %2$s',
		            $this->locale()->toDate($startDateObject),
		            $this->locale()->toTime($startDateObject)
		          ) ?>
		          - 
		          <?php echo $this->translate('%1$s at %2$s',
		            $this->locale()->toDate($endDateObject),
		            $this->locale()->toTime($endDateObject)
		          ) ?>
		        </div>
	  			</td>
	  		</tr>
		 	<?php endif ?>
    		
    	<?php if( !empty($this->sitepageevent_subject->location) ): ?>
    		<tr valign="top">
      		<td class="label"><div><?php echo $this->translate('Where')?></div></td>
      		<td><?php echo $this->sitepageevent_subject->location; ?> <?php echo $this->htmlLink('http://maps.google.com/?q='.urlencode($this->sitepageevent_subject->location), $this->translate('Map'), array('target' => 'blank')) ?></td>
    		</tr>
    	<?php endif ?>
    	
	    <?php if( !empty($this->sitepageevent_subject->host) ): ?>
	      <?php if( $this->sitepageevent_subject->host != $this->sitepageevent_subject->getParent()->getTitle()): ?>
	        <tr valign="top">
	          <td class="label"><div><?php echo $this->translate('Host') ?></div></td>
	          <td><?php echo $this->sitepageevent_subject->host ?></td>
	        </tr>
	      <?php endif ?>
	      <tr valign="top">
	        <td class="label"><div><?php echo $this->translate('Led by') ?></div></td>
	        <td><?php echo $this->sitepageevent_subject->getParent()->__toString() ?></td>
	      </tr>
	    <?php endif ?>
	    
	    <?php if( !empty($this->sitepageevent_subject->category_id) ): ?>
		    <tr valign="top">
		      <td class="label"><div><?php echo $this->translate('Category')?></div></td>
		      <td>
		        <?php echo $this->htmlLink(array(
		          'route' => 'sitepageevent_browse',
		          'action' => 'browse',
		          'event_category_id' => $this->sitepageevent_subject->category_id,
		        ), $this->translate((string)$this->sitepageevent_subject->categoryName())) ?>
		      </td>
		    </tr>
	    <?php endif ?>
	    
	    <tr valign="top">
	      <td class="label"><div><?php echo $this->translate('RSVPs');?></div></td>
	      <td>
	        <ul>
	          <li>
	            <strong><?php echo $this->locale()->toNumber($this->sitepageevent_subject->getAttendingCount()) ?></strong>
	            <span><?php echo $this->translate('attending');?></span>
	          </li>
	          <li>
	            <strong><?php echo $this->locale()->toNumber($this->sitepageevent_subject->getMaybeCount()) ?></strong>
	            <span><?php echo $this->translate('maybe attending');?></span>
	          </li>
	          <li>
	            <strong><?php echo $this->locale()->toNumber($this->sitepageevent_subject->getNotAttendingCount()) ?></strong>
	            <span><?php echo $this->translate('not attending');?></span>
	          </li>
	          <li>
	            <strong><?php echo $this->locale()->toNumber($this->sitepageevent_subject->getAwaitingReplyCount()) ?></strong>
	            <span><?php echo $this->translate('awaiting reply');?></span>
	          </li>
	        </ul>
	      </td>
	    </tr>
    </tbody>
  </table>
</div>