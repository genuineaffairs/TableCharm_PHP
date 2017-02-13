<?php

/**
 * Description of Error
 *
 * @author abakivn
 */
class Mgslapi_Controller_Action_Helper_Error {
  // REQUEST AND AUTHENTICATION CHECKS
  const NOT_SIGN_IN = 'Please sign in to continue.';
  const INVALID_DATA = 'Invalid data';
  const NO_EMAIL_FOUND = 'No record of a member with that email was found.';
  const INVALID_CREDENTIALS = 'Invalid credentials supplied';
  const USER_NOT_FOUND = 'User not found';
  const NOT_ALLOWED = 'You are not allowed to perform this action';
  const INVALID_REQUEST_METHOD = 'Invalid request method';
  const ALREADY_LOG_OUT = 'You are already logged out.';
  
  // VIDEO UPLOAD
  const MAX_VIDEO_UPLOADED = 'You have already uploaded the maximum number of videos allowed. If you would like to upload a new video, please delete an old one first.';
  const VIDEO_URL_NOT_FOUND = 'We could not find a video there - please check the URL and try again.';
  const UPLOAD_MAX_SIZE = 'Max file size limit exceeded (probably).';
  const FILE_FIELD_NOT_FOUND = 'File field is not found';
  const INVALID_UPLOAD = 'Invalid Upload';
  
  // FEED COMMENT
  const FEED_DELETED = 'This feed has already been deleted';
  const FEED_NOT_FOUND = 'Feed is not found';
  const NOT_ALLOW_COMMENT_DELETE = 'You do not have the privilege to delete this comment';
  const NOT_ALLOW_COMMENT = 'This user is not allowed to comment on this item.';
  const NOT_ALLOW_FEED_UNLIKE = 'This user is not allowed to unlike this item';
  const NOT_ALLOW_FEED_LIKE = 'This user is not allowed to like this item';
  const FEED_NOT_SHAREABLE = 'You cannot share this item because it has been removed.';
  
  // ALBUM
  const ALBUM_NOT_FOUND = 'Album is not found';
  const PHOTO_NOT_FOUND = 'Photo is not found';
  
  // VIDEO
  const VIDEO_NOT_FOUND = 'Video is not found';
  
  // MESSAGE
  const NOT_BELONG_TO_CONVERSATION = 'You do not belong to this conversation';
  const CANNOT_REPLY = 'You cannot reply to this message';
  
  // FRIENDS
  const ALLREADY_FRIENDS = 'Already friends';
  const SELF_FRIEND = 'You cannot befriend yourself.';
  const BLOCKED_FRIEND = 'Friendship request was not sent because you blocked this member.';
  
  // MEDICAL RECORD
  const EMR_NOT_FOUND = 'Medical Record not found';
  
  // EVENT
  const EVENT_NOT_FOUND = "Event doesn't exists";
  const NOT_A_MEMBER_OF_EVENT = "You are not a member of this event.";
  const TOPIC_NOT_FOUND = "Topic not found";
  
  // COMMENT
  const COMMENT_NOT_FOUND = "No comment found";
  
  // COMMON
  const ITEM_NOT_FOUND = "Item not found";
  const NO_DATA = "No data to display";
  const OWNER_CAN_NOT_LEAVE = "Owner cannot leave";
  
  // CIRCLE
  const CIRCLE_NOT_FOUND = 'Circle not found';
  
  // RESUME
  const RESUME_DRAFT = 'This CV is not live yet.';
  const RESUME_EXPIRED = 'This CV has been expired.';
  const RESUME_NOT_APPROVED = 'This CV is not approved yet.';
  
}
