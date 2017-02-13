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
class Zulu_Model_DbTable_Zulus extends SharedResources_Model_DbTable_Abstract {

    protected $_rowClass = "Zulu_Model_Zulu";

    public function getZuluByUserId($user_id) {
        return $this->fetchRow(
                        $this->select()
                                ->where('user_id = ?', $user_id)
        );
    }

}
