<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Ratings.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_Model_DbTable_Ratings extends Engine_Db_Table
{
  protected $_rowClass = "Document_Model_Rating";

  /**
   * Return rating data
   *
   * @param array params
   * @return Zend_Db_Table_Select
   */
  public function avgRating($document_id) {

    //FETCH DATA
    $avg_rating = $this->select()
                    ->from($this->info('name'), array('AVG(rating) AS avg_rating'))
                    ->where('document_id = ?', $document_id)
                    ->group('document_id')
                    ->query()
                    ->fetchColumn();

    //RETURN DATA
    return $avg_rating;
  }

	 /**
   * Do document rating
   * @param int $document_id : document id
	 * @param int $user_id : user id
	 * @param int $rating : $rating id
   */
  public function doDocumentRating($document_id, $user_id, $rating) {

    //FETCH DATA
    $done_rating = $this->select()
                    ->from($this->info('name'), array('document_id'))
                    ->where('document_id = ?', $document_id)
                    ->where('user_id = ?', $user_id)
                    ->query()
                    ->fetchColumn();

		//INSERT RATING ENTRIES IN TABLE
    if (empty($done_rating)) {
      $this->insert(array(
          'document_id' => $document_id,
          'user_id' => $user_id,
          'rating' => $rating
      ));
    }
  }

	/**
   * Get previous rated or not by user
   * @param int $document_id : document id
	 * @param int $user_id : user id
   */
  public function previousRated($document_id, $user_id) {

		//FETCH DATA
    $done_rating = $this->select()
                    ->from($this->info('name'), array('document_id'))
                    ->where('document_id = ?', $document_id)
                    ->where('user_id = ?', $user_id)
                    ->query()
										->fetchColumn();

		//RETURN DATA
    if (!empty($done_rating))
      return true;
    
		return false;
  }
  
	/**
   * Get total rating
   * @param int $document_id : document id
	 * @return  total rating
   */
  public function countRating($document_id) {

    //FETCH DATA
    $total_count = $this->select()
                    ->from($this->info('name'), array('COUNT(document_id) AS total_count'))
                    ->where('document_id = ?', $document_id)
                    ->query()
                    ->fetchColumn();

    //RETURN DATA
    return $total_count;
  }
}