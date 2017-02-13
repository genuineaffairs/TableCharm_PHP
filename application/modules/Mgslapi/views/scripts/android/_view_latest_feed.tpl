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
            2,
            'android'
            )
?>  
 