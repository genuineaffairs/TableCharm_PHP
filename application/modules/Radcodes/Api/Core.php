<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
class Radcodes_Api_Core extends Core_Api_Abstract
{
  public function serverToLocalTime($server_time, $user = null)
  {
    if (!($user instanceof User_Model_User))
    {
      $user = Engine_Api::_()->user()->getViewer();
    }    
    
    $end = strtotime($server_time);
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($user->timezone);
    $local_time = date('Y-m-d H:i:s', $end);
    date_default_timezone_set($oldTz);
    
    return $local_time;
  }
  // serverToLocalTime
  
  public function filterEmptyParams($values)
  {
    foreach ($values as $key => $value)
    {
      if (is_array($value))
      {
        foreach ($value as $value_k => $value_v)
        {
          if (!strlen($value_v))
          {
            unset($value[$value_k]);
          }
        }
      }
      
      if (is_array($value) && count($value) == 0)
      {
        unset($values[$key]);
      }
      else if (!is_array($value) && !strlen($value))
      {
        unset($values[$key]);
      }
    }
    
    return $values;
  }
  // filterEmptyParams
  
	/**
	 * @param string $type
	 * @return Radcodes_Lib_Rest_Store
	 */
	public function getRest($type)
	{
		if (strtolower($type) == 'store')
		{
			$rest = new Radcodes_Lib_Rest_Store();
		}
		else {
		  $rest = new Radcodes_Lib_Rest();
		}
		return $rest;
	}
	
	public function varPrint($var, $name=null)
	{
		echo "<pre>";
		if ($name) echo "$name ::\n";
		print_r($var);
		echo "</pre>";
	}
	
	
  public function varClass($var, $name=null)
  {
    echo "<pre>";
    if ($name) echo "$name ::\n";
    echo get_class($var);
    echo "</pre>";
  }
	
  
  public function varDump($var, $name=null)
  {
    echo "<pre>";
    if ($name) echo "$name ::\n";
    var_dump($var);
    echo "</pre>";
  }
  
  public function getPopularTags($resource_type, $options=array())
  {
    $tag_table = Engine_Api::_()->getDbtable('tags', 'core');
    $tagmap_table = $tag_table->getMapTable();
    
    $tName = $tag_table->info('name');
    $tmName = $tagmap_table->info('name');
    
    if (isset($options['order']))
    {
      $order = $options['order'];
    }
    else
    {
    	$order = 'text';
    }
    
    if (isset($options['sort']))
    {
    	$sort = $options['sort'];
    }
    else
    {
    	$sort = $order == 'total' ? SORT_DESC : SORT_ASC;
    }
    
    $limit = isset($options['limit']) ? $options['limit'] : 50;
    
    $select = $tag_table->select()
        ->setIntegrityCheck(false)
        ->from($tmName, array('total' => "COUNT(*)"))
        ->join($tName, "$tName.tag_id = $tmName.tag_id")
        ->where('resource_type = ?', $resource_type)
        ->where('tag_type = ?', 'core_tag')
        ->group("$tName.tag_id")
        ->order("total desc")
        ->limit("$limit");

    $tags = $tag_table->fetchAll($select);   
    
    $records = array();
    
    $columns = array();
    if (!empty($tags))
    {
	    foreach ($tags as $k => $tag)
	    {
	      $records[$k] = $tag;
	      $columns[$k] = $order == 'total' ? $tag->total : $tag->text; 
	    }
    }

    $tags = array();
    if (count($columns))
    {
      asort($columns, $sort);
	    foreach ($columns as $k => $name)
	    {
	      $tags[$k] = $records[$k];
	    }
    }

    return $tags; 
  }


  public function query($sql)
  {
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    
    try 
    {
	    $db->query($sql);
      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
  }
  
  public function validateLicense($module)
  {
    $license = Engine_Api::_()->getApi('settings', 'core')->getSetting($module.'.license');
    return (trim($license) && $license != 'XXXX-XXXX-XXXX-XXXX');
  }  
  
  public function getTinyMceEditorOptions($params = array())
  {
    $default_options = array(
      'upload_url' => false,
      'remove_script_host' => '',
      'convert_urls' => false,
      'relative_urls' => '',
      'mode' => 'exact',
      'elements' => 'body',
      'width' => "500px",
      'height' => "320px",
      'media_strict' => false,
      'extended_valid_elements' => '*[*],**,object[width|height|classid|codebase|id|name],param[name|value],embed[src|type|width|height|flashvars|wmode|id|name],iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder|id|name|class],video[src|type|width|height|flashvars|wmode|class|poster|preload|id|name],source[src]',
      'plugins' => "table,fullscreen,media,preview,paste,code,image,textcolor,link,anchor,charmap,emoticons",
      'toolbar1' => "cut,copy,|,undo,redo,removeformat,pastetext,|,code,media,image,link,fullscreen,preview",
      'toolbar2' => "fontselect,fontsizeselect,bold,italic,underline,strikethrough,forecolor,backcolor",
    	'toolbar3' => "alignleft,aligncenter,alignright,alignjustify,|,bullist,numlist,|,outdent,indent,blockquote,|,table,emoticons,charmap",
	  );
	  $options = array_merge($default_options, $params);
	  return $options;
  }
}