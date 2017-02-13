<?php
/**
 * This class is heavily modelled after both Album_Model_Photo and Video_Model_Video. Use them as a reference if making modifications.
 */
class Document_Model_Document extends Core_Model_Item_Abstract
{
  public function getRichContent($view = false, $params = array())
  {
    // $view == false means that this rich content is requested from the activity feed
    if ($view == false) {
      $titleDiv = "<div class='title'>Document: <a href='" . $this->getHref($params) . "' class='info'>" . $this->title . "</a></div>";
      $descriptionDiv = "<div class='description'>" . Engine_Api::_()->document()->subPhrase(strip_tags($this->description), 255) . "</div>";
      return "<div class='document-info'>" . $titleDiv . $descriptionDiv . "</div>";
    }

    return '<p>Placeholder.</p>'; // TODO: figure out what to return here. this will likely be used on individual document pages.
  }

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'document_view',
      'reset' => true,
      'user_id' => $this->owner_id,
      'document_id' => $this->document_id,
      'slug' => $this->getSlug(),
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
  }

  public function getFilePath()
  {
    $document_id = $this->file_id;
    if( !$document_id ) {
      return null;
    }

    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($document_id);
    if( !$file ) {
      return null;
    }

    return $file->map();
  }

  public function comments() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  public function likes() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  public function tags() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }
}