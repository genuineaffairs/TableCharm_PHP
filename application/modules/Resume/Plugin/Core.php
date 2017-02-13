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
 
 
 
class Resume_Plugin_Core
{
  public function onStatistics($event)
  {
    $table   = Engine_Api::_()->getDbTable('resumes', 'resume');
    
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'resume');
  }


  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {
      // Delete resumes
      $resumeTable = Engine_Api::_()->getDbtable('resumes', 'resume');

      $resumeSelect = $resumeTable->select()->where('user_id = ?', $payload->getIdentity());
      foreach( $resumeTable->fetchAll($resumeSelect) as $resume ) {
        $resume->delete();
      }

    }
  }
  
  public function onResumeDeleteBefore($event) {
    $payload = $event->getPayload();

    if($payload instanceof Resume_Model_Resume) {
      $resumeVideoTable = Engine_Api::_()->getDbTable('videos', 'resume');
      $db = $resumeVideoTable->getAdapter();
      
      $db->beginTransaction();
      
      $resumeVideoSelect = $resumeVideoTable->select()->where('resume_id = ?', $payload->getIdentity());
      foreach($resumeVideoTable->fetchAll($resumeVideoSelect) as $video) {
        $video->delete();
      }
      
      $db->commit();
    }
  }
}