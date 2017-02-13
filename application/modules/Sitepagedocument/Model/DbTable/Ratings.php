<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Ratings.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagedocument_Model_DbTable_Ratings extends Engine_Db_Table {

  protected $_rowClass = "Sitepagedocument_Model_Rating";

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
   * Do sitepagedocument rating
   * @param int $document_id : sitepagedocument id
   * @param int $user_id : user id
   * @param int $rating : rating id
   */
  public function doSitepagedocumentRating($document_id, $user_id, $rating) {

		//FETCH DATA
    $done_rating = $this->select()
                    ->from($this->info('name'), array('document_id'))
                    ->where('document_id = ?', $document_id)
                    ->where('user_id = ?', $user_id)
										->query()
										->fetchColumn();

		//IF USER IS NOT RATED YET THEN INSERT RATING
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
   * @param int $document_id : sitepagedocument id
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
    if (!empty($done_rating)) {
      return true;
		}

    return false;
  }

  /**
   * Get total rating
   * @param int $document_id : sitepagedocument id
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
?>