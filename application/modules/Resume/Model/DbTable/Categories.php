<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
class Resume_Model_DbTable_Categories extends SharedResources_Model_DbTable_RadcodesAbstract
{
  const PLAYER_CATEGORY_ID = 13;
  const COACH_CATEGORY_ID = 15;
  const AGENT_CATEGORY_ID = 18;

  protected $_rowClass = 'Resume_Model_Category';
}
