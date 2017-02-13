<?php
 
 header("Content-type: text/css");
 
?>

<style type="text/css">
 .floatl
 {
   float:left;

}
.cmadrem,
.cmaddis
{
	width:<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.block.width', 150);?>px;
}

</style>