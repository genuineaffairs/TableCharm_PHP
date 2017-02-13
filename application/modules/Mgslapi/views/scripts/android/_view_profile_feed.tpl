<?php $staticBaseUrl = $this->serverUrl((string) $this->layout()->staticBaseUrl);  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta name="apple-mobile-web-app-capable" content="yes">
            <meta name="viewport" content="width=device-width, target-densitydpi=device-dpi, initial-scale=0, maximum-scale=0, user-scalable=no" />
            <title>Untitled Document</title>
            <link href="<?php echo $staticBaseUrl .'application/modules/Mgslapi/externals/styles/android/styles.css'?>" rel="stylesheet" type="text/css" />
            <script type="text/javascript">
                window.baseurl = '<?php echo $this->serverUrl((string) $this->layout()->staticBaseUrl); ?>';
            </script>
            <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
            <script src='<?php echo $staticBaseUrl .'application/modules/Mgslapi/externals/scripts/android/swipe.js'?>'></script>
            <script src='<?php echo $staticBaseUrl .'application/modules/Mgslapi/externals/scripts/android/jquery-base64/jquery.base64.min.js'?>'></script>
            <script src='<?php echo $staticBaseUrl .'application/modules/Mgslapi/externals/scripts/android/profile.js'?>'></script>
    </head>
    <body>
            <div class="match_info">
                <div class="team_photo">
                    <div class="img_container">
                        <img src="<?php echo Engine_Api::_()->mgslapi()->getItemPhotoUrl($this->subject);  ?>" alt="" title="" />
                    </div>
                </div>
                <div class="team_details">
                    <?php $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($this->subject);
                    $genderID=$fieldsByAlias['gender']->getValue($this->subject);
                    $optionObj = Engine_Api::_()->fields()->getFieldsOptions($this->subject)->getRowMatching('option_id', $genderID->value);
                    $gender = $optionObj->label; 
                    ?>
                    <h3 class="title"><?php echo $this->subject->getTitle(); ?></h3>
<!--                    <ul class="team_info">
                        <li><img src="<?php //echo  $this->serverUrl((string)$this->baseUrl())?>/application/modules/Mgslapi/externals/images/gender-icon.png" alt="" title="" /><span>Gender <?php echo $gender ?></span></li>
                        <li><img src="<?php //echo  $this->serverUrl((string)$this->baseUrl())?>/application/modules/Mgslapi/externals/images/born-icon.png" alt="" title="" /><span>Born on <?php echo $this->timestamp($fieldsByAlias['birthdate']->getValue($this->subject)->value) ?></span></li>
                        <li><img src="<?php //echo  $this->serverUrl((string)$this->baseUrl())?>/application/modules/Mgslapi/externals/images/update-icon.png" alt="" title="" /><span>Last update <?php echo $this->timestamp($this->subject->modified_date)?></span></li>
                        <li class="nomargin"><img src="<?php //echo  $this->serverUrl((string)$this->baseUrl())?>/application/modules/Mgslapi/externals/images/join-icon.png" alt="" title="" /><span>Joined <?php echo $this->timestamp($this->subject->creation_date)?></span></li>
                    </ul>-->
                    <?php
                        $subject = $this->subject;
                        // get the subject profile field structured
                        $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($subject);
                        // get the profile field map
                        foreach ($fieldStructure as $map) {
                            $field = $map->getChild();
                            $value = $field->getValue($subject);
                            if (is_object($value)) {
                                if ($field->label === 'Primary Sport') {
                                    $profileData[$field->label] = $field->getOption($value->getValue())->label;
                                }
                                if ($field->label === 'Other Sports') {
                                    $profileData[$field->label] = $field->getOption($value->getValue())->label;
                                }
                                if ($field->label === 'Participation Level') {
                                    $profileData[$field->label] = $field->getOption($value->getValue())->label;
                                } else {
                                    $profileData[$field->label] = $value->getValue();
                                }
                            }
                        }
                        
                        // get the participition data for specific user
                        $valueTable = Engine_Api::_()->fields()->getTable('user', 'values');
                        $optonTable = Engine_Api::_()->fields()->getTable('user', 'options');
                        $select = $valueTable->select()
                                ->where('item_id = ?', $subject->getIdentity())
                                ->where('field_id = ?', 382);
                        $valueData = $valueTable->fetchAll($select);
                        // get the all option value that the user set
                        $participation_level = NULL;
                        if (count($valueData) > 0) 
                        {
                            $optionValue=array();
                            foreach ($valueData as $data)
                            {
                                $optionValue[]=$data->value;
                            }
                                
                                // get the option name
                            $select = $optonTable->select()
                                  ->where('option_id IN(?)', $optionValue);
                            $optionNames = $optonTable->fetchAll($select);
                            
                            // get the perticipition label
                            $participation_Level = array();
                            foreach ($optionNames as $optionName)
                                $participation_Level[]=$optionName->label;

                            // perticipition level in to view
                            $participation_level= implode(', ', $participation_Level);
                        }
                        

                        
                        
                        if ($profileData["Country of Residence"] != null) 
                        {
                            try {
                                $locale = new Zend_Locale(Zend_Locale::BROWSER);
                                $countries = $locale->getTranslationList('Territory', Zend_Locale::BROWSER, 2);
                            } catch (exception $e) {
                                $locale = new Zend_Locale('en_US');
                                $countries = $locale->getTranslationList('Territory', 'en_US', 2);
                            }
                            $residence= $countries[$profileData["Country of Residence"]];
                        }
                        ?>
                        <ul class="team_info">                                                   
                                <li>                                    
                                    <span><?php echo $this->translate('Primary Sport '); ?>
                                        <?php if($profileData['Primary Sport'] != NULL): ?>  
                                            <?php echo $this->translate($profileData['Primary Sport']) ?>
                                        <?php endif; ?>
                                    </span>
                                </li>                                                  
                                <li>                                   
                                    <span><?php echo $this->translate('Participation Level'); ?> 
                                        <?php if($participation_level != NULL): ?>   
                                            <?php echo $participation_level; ?>
                                        <?php endif; ?>
                                    </span>
                                </li>                
                                <li>
                                    <span><?php echo $this->translate('Currently resides in'); ?>
                                        <?php if($residence != NULL): ?>
                                            <?php echo $residence; ?>
                                        <?php endif; ?>
                                    </span>
                                </li>
                        </ul>
                </div>
                <div class="clear"></div>
            </div>

            <!-- event content -->
            <div class="event_option profile_event_option">
                <ul>
                    <li><a id="do_status_post" href="#statusAction">Status</a></li>
                    <li><a id="do_photo_video_post" href="#photoVideoAction">Photo / Video</a></li>
                    <li class="lastchild"><a id="do_check_in_post" href="#checkInAction">Check in</a></li>
                    <div  class="clear"></div>
                </ul>
            </div>
        <!-- end event content -->
        <div class="wrap">             
        <?php if (!$this->activity): ?>
            <?php if ($this->action_id): ?>
                <h2><?php echo $this->translate("Activity Item Not Found") ?></h2>
                <p><?php echo $this->translate("The page you have attempted to access could not be found.") ?></p>
                <?php return;
            else:
                ?>
                <div class="tip">
                    <span><?php echo $this->translate("Nothing has been posted here yet - be the first!") ?></span>
                </div>
                <?php return;
            endif;
            ?>
            <?php endif; ?>
            <?php
            echo $this->activityLoopApi($this->activity, array(
                'action_id' => $this->action_id,
                'viewAllComments' => $this->viewAllComments,
                'viewAllLikes' => $this->viewAllLikes,
                'getUpdate' => $this->getUpdate,
                'deviceType' => $this->deviceType,
                'viewer' => $this->viewer,
                'subject' => $this->subject
            ),
                0,
                'android')
            ?>
            <!--<div class="clear"></div>  
            <div class="lazyloaderpane"></div>-->

            <!--</ul>-->
        </div>  
                <script type="text/javascript">
                $('.info a').attr('href','javascript:void(0)');
                </script>
    </body>
</html>
