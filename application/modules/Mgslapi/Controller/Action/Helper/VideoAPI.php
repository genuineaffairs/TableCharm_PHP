<?php

class Mgslapi_Controller_Action_Helper_VideoAPI extends Zend_Controller_Action_Helper_Abstract {

  public function getVideoLocation($video) {
    $httpInformation = _ENGINE_SSL ? 'https://' : 'http://';
    $video_location = null;

    if (isset($video->type)) {
      // if video type is youtube
      if ($video->type == 1) {
        $video_location = $httpInformation . 'www.youtube.com/watch?v=' . $video->code;
      }
      // if video type is vimeo
      if ($video->type == 2) {
        $video_location = $httpInformation . 'vimeo.com/moogaloop.swf?clip_id=' . $video->code . '&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1';
      }
      // if video type is uploaded
      if ($video->type == 3) {
        $video_location = Engine_Api::_()->storage()->get($video->file_id, $video->getType())->getHref();
      }
      // if video type is dailymotion
      if ($video->type == 4) {
        $video_location = $httpInformation . 'www.dailymotion.com/swf/video/' . $video->code;
      }
    }
    
    return $video_location;
  }
  
  /**
   * Get video source type
   * 0: upload
   * 1: link
   */
  public function getMobileVideoType($video) {
    return $video->type == 3 ? 0 : 1;
  }

}
