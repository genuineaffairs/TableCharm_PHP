<?php
class Ynvideo_Plugin_Adapter_VideoURL {

    protected $_params;

    public function compileVideo($params) {
        $video_id = $params['video_id'];
        $location = $params['location'];
        $view = $params['view'];

            $embedded = "
    <div id='videoFrame" . $video_id . "'></div>
    <script type='text/javascript'>
    en4.core.runonce.add(function(){\$('video_thumb_" . $video_id . "').removeEvents('click').addEvent('click', function(){flashembed('videoFrame$video_id',{src: '" . Zend_Registry::get('StaticBaseUrl') . "externals/flowplayer/flowplayer-3.1.5.swf', width: " . ($view ? "640" : "360") . ", height: " . ($view ? "386" : "326") . ", wmode: 'opaque'},{config: {clip: {url: '$location',autoPlay: " . ($view ? "false" : "true") . ", autoBuffering: true},plugins: {controls: {background: '#000000',bufferColor: '#333333',progressColor: '#444444',buttonColor: '#444444',buttonOverColor: '#666666'}},canvas: {backgroundColor:'#000000'}}});})});
    </script>";

        return $embedded;
    }

    public function setParams($options) {
        foreach ($options as $key => $value) {            
            $this->_params[$key] = $value;
        }
    }

    public function getVideoLargeImage() {
        return null;    
    }
    
    public function isValid() {
        if (isset($this->_params['link'])) {
            $valid = preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->_params['link']);
            $params = @pathinfo($this->_params['link']);
            if (isset($params['extension']) 
            	&& (strtoupper($params['extension']) == 'FLV' || strtoupper($params['extension']) == 'MP4')) {
                return true;
            }    
        }
        return false;
    }
    
    public static function getDefaultTitle() {
        return Zend_Registry::get('Zend_Translate')->_('Untitled video');
    }
}