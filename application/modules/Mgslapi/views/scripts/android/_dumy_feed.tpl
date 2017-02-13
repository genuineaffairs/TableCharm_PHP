<?php

echo $this->activityLoopApi($this->activity, array(
    'action_id' => $this->action_id,
    'viewAllComments' => $this->viewAllComments,
    'viewAllLikes' => $this->viewAllLikes,
    'getUpdate' => $this->getUpdate,
    'deviceType' => $this->deviceType,
    'max_id' => $this->max_id,
    'viewer' => $this->viewer,
    'subject' => $this->subject
    ),
     1,
    'android'
)
?>