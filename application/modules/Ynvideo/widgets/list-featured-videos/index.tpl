<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>
<style>
    #ynvideo_featured .slides_container div.slide {
        width : <?php echo $this->slideWidth - 30?>px;
    }
    
    #ynvideo_featured .slides_container {
        height : <?php echo $this->slideHeight - 40?>px;
    }
    
    #ynvideo_featured .slides_container .slide {
        height : <?php echo $this->slideHeight - 40?>px;
    }
</style>

<div id="ynvideo_featured">
    <div id="slides" style="width:<?php echo $this->slideWidth?>px;height:<?php echo $this->slideHeight?>px">
        <div class="slides_container">
            <?php foreach ($this->videos as $index => $video) : ?>
                <?php if ($index % 2 == 0) : ?>
                    <div class="slide" style="width:<?php echo $this->slideWidth - 20?>px">
                <?php endif; ?>
                    <?php
                        echo $this->partial('_video_featured.tpl', 'ynvideo', array(
                            'video' => $video,
                            'videoWidth' => ($this->slideWidth - 10) / 2 - 10,
                            'videoHeight' => $this->slideHeight - 140,
                        ));
                    ?>
                <?php if ( (($index + 1) % 2 == 0) || ($index == (count($this->videos)-1)) ) : ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>