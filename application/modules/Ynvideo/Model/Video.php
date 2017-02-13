<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Model_Video extends Core_Model_Item_Abstract {

//    protected $_parent_type = 'user';
    protected $_owner_type = 'user';
    protected $_type = 'video';

//    protected $_parent_is_owner = true;

    public function getHref($params = array()) {
        $params = array_merge(array(
            'route' => 'video_view',
            'reset' => true,
            'user_id' => $this->owner_id,
            'video_id' => $this->video_id,
            'slug' => $this->getSlug(),
            ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
                ->assemble($params, $route, $reset);
    }

    public function getRichContent($view = false, $params = array()) {
        $session = new Zend_Session_Namespace('mobile');
        $mobile = $session->mobile;

        $paramsForCompile = array_merge(array(
            'video_id' => $this->video_id,
            'code' => $this->code,
            'view' => $view,
            'mobile' => $mobile,
            'duration' => $this->duration
            ), $params);
        if ($this->type == Ynvideo_Plugin_Factory::getUploadedType()) {
            $paramsForCompile['location'] = Engine_Api::_()->storage()->get($this->file_id, $this->getType())->getHref();
        } else if ($this->type == Ynvideo_Plugin_Factory::getVideoURLType()) {
            $paramsForCompile['location'] = $this->code;
        }

        // $view == false means that this rich content is requested from the activity feed
        if ($view == false) {
            $video_duration = "";
            if ($this->duration) {
                if ($this->duration >= 3600) {
                    $duration = gmdate("H:i:s", $this->duration);
                } else {
                    $duration = gmdate("i:s", $this->duration);
                }
                $duration = ltrim($duration, '0:');

                $video_duration = "<span class='video_length'>" . $duration . "</span>";
            }

            // prepare the thumbnail
            $thumb = Zend_Registry::get('Zend_View')->itemPhoto($this, 'thumb.video.activity');

            if ($this->photo_id) {
                $thumb = Zend_Registry::get('Zend_View')->itemPhoto($this, 'thumb.video.activity');
            } else {
                $thumb = '<img alt="" src="' . Zend_Registry::get('StaticBaseUrl') . 'application/modules/Video/externals/images/video.png">';
            }

            if (!$mobile) {
                $thumb = '<a id="video_thumb_' . $this->video_id . '" style="" href="' . $this->getHref($params) . '">
                  <div class="video_thumb_wrapper">' . $video_duration . $thumb . '</div>
                  </a>';
            } else {
                $thumb = '<a id="video_thumb_' . $this->video_id . '" class="video_thumb" href="' . $this->getHref($params) . '">
                  <div class="video_thumb_wrapper">' . $video_duration . $thumb . '</div>
                  </a>';
            }

            // prepare title and description
            $title = "<a href='" . $this->getHref($params) . "'>$this->title</a>";
            $tmpBody = strip_tags($this->description);
            $description = "<div class='video_desc'>" . (Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody) . "</div>";

            return $thumb . '<div class="video_info">' . $title . $description . '</div>';
        }

        return Ynvideo_Plugin_Factory::getPlugin((int) $this->type)->compileVideo($paramsForCompile);
    }

    public function getEmbedCode(array $options = null) {
        $options = array_merge(array(
            'height' => '525',
            'width' => '525',
            ), (array) $options);

        $view = Zend_Registry::get('Zend_View');
        $url = 'http://' . $_SERVER['HTTP_HOST']
            . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                'module' => 'ynvideo',
                'controller' => 'video',
                'action' => 'external',
                'video_id' => $this->getIdentity(),
                ), 'default', true) . '?format=frame';
        return '<iframe '
            . 'src="' . $view->escape($url) . '" '
            . 'width="' . sprintf("%d", $options['width']) . '" '
            . 'height="' . sprintf("%d", $options['width']) . '" '
            . 'style="overflow:hidden;"'
            . '>'
            . '</iframe>';
    }

    public function getKeywords($separator = ' ') {
        $keywords = array();
        foreach ($this->tags()->getTagMaps() as $tagmap) {
            $tag = $tagmap->getTag();
            $keywords[] = $tag->getTitle();
        }

        if (null === $separator) {
            return $keywords;
        }

        return join($separator, $keywords);
    }

    // Interfaces

    /**
     * Gets a proxy object for the comment handler
     *
     * @return Engine_ProxyObject
     * */
    public function comments() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
    }

    /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     * */
    public function likes() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
    }

    /**
     * Gets a proxy object for the tags handler
     *
     * @return Engine_ProxyObject
     * */
    public function tags() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
    }

    public function storeThumbnail($thumbnail, $type = 'small') {
        if (!empty($thumbnail)) {
            if (is_string($thumbnail)) {
                $pathInfo = @pathinfo($thumbnail);
                $parts = explode('?', preg_replace("/#!/", "?", $pathInfo['extension']));
                $ext = $parts[0];
                $thumbnail_parsed = @parse_url($thumbnail);

                if (@GetImageSize($thumbnail)) {
                    $valid_thumb = true;
                } else {
                    $valid_thumb = false;
                }

                if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
                    $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
                    $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;

                    $src_fh = fopen($thumbnail, 'r');
                    $tmp_fh = fopen($tmp_file, 'w');
                    stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

                    $image = Engine_Image::factory();

                    $width = 240;
                    $height = 120;
                    if ($type == 'large') {
                        $width = 480;
                        $height = 360;
                    }
                    $image->open($tmp_file)->resize($height, $width)->write($thumb_file)->destroy();

                    try {
                        $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                            'parent_type' => $this->getType(),
                            'parent_id' => $this->getIdentity(),
                            'type' => ($type == 'large') ? 'thumb.large' : 'thumb.normal'
                            ));

                        // Remove temp file
                        @unlink($thumb_file);
                        @unlink($tmp_file);

                        if ($type == 'large') {
                            $this->large_photo_id = $thumbFileRow->file_id;
                        } else {
                            $this->photo_id = $thumbFileRow->file_id;
                        }
                        $this->save();
                    } catch (Exception $e) {
                        Zend_Registry::get('Zend_Log')->log($e->__toString(), Zend_Log::WARN);
                    }
                }
            } else if (is_numeric($thumbnail)) {
                if ($type == 'large') {
                    $this->large_photo_id = $thumbnail;
                } else {
                    $this->photo_id = $thumbnail;
                }
            }
        }
    }

    protected function _postInsert() {
        $table = Engine_Api::_()->getDbTable('signatures', 'ynvideo');
        $select = $table->select()
            ->where('user_id = ?', $this->owner_id)
            ->limit(1);
        $row = $table->fetchRow($select);

        if (null == $row) {
            $row = $table->createRow();
            $row->user_id = $this->owner_id;
            $row->video_count = 1;
        } else {
            $row->video_count = new Zend_Db_Expr('video_count + 1');
        }
        $row->save();
        parent::_postInsert();
    }

    protected function _delete() {
        // remove video from favorite table
        Engine_Api::_()->getDbTable('favorites', 'ynvideo')->delete(array(
            'video_id = ?' => $this->getIdentity(),
        ));

        // remove video from favorite table
        Engine_Api::_()->getDbTable('favorites', 'ynvideo')->delete(array(
            'video_id = ?' => $this->getIdentity(),
        ));

        // remove video from rating table
        Engine_Api::_()->getDbTable('ratings', 'ynvideo')->delete(array(
            'video_id = ?' => $this->getIdentity(),
        ));

        // remove video from watchlater table
        Engine_Api::_()->getDbTable('watchlaters', 'ynvideo')->delete(array(
            'video_id = ?' => $this->getIdentity(),
        ));

        // update video count in signature table
        $signatureTbl = Engine_Api::_()->getDbTable('signatures', 'ynvideo');
        $signature = $signatureTbl->fetchRow($signatureTbl->select()->where('user_id = ?', $this->owner_id));
        if ($signature) {
            $signature->video_count = new Zend_Db_Expr('video_count - 1');
        }
        $signature->save();

        // remove video from playlists
        $playlistAssocTbl = Engine_Api::_()->getDbTable('playlistassoc', 'ynvideo');
        $playlistAssocs = $playlistAssocTbl->fetchAll($playlistAssocTbl->select()->where('video_id = ?', $this->getIdentity()));
        foreach ($playlistAssocs as $playlistAssoc) {
            $playlistAssoc->delete();
        }

        parent::_delete();
    }

    protected function _postDelete() {
        parent::_postDelete();

//         $signatureItem = Engine_Api::_()->getItem('ynvideo_signature', $this->owner_id);
//         if ($signatureItem) {
//             if ($signatureItem->video_count > 0) {
//                 $signatureItem->video_count = new Zend_Db_Expr('video_count - 1');
//                 $signatureItem->save();
//             }
//         }
    }

    /**
     * Gets a url to the current photo representing this item. Return null if none
     * set
     *
     * @param string The photo type (null -> main, thumb, icon, etc);
     * @return string The photo url
     */
    public function getPhotoUrl($type = null) {
        $field = 'photo_id';
        if ($type == 'thumb.large') {
            $field = 'large_photo_id';
        }
        if (empty($this->$field)) {
            return null;
        }

        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->$field, $type);
        if (!$file) {
            return null;
        }

        return $file->map();
    }

}