ALTER TABLE `engine4_mgslapi_devices`
  ADD UNIQUE(`user_id`, `app_id`),
  ADD UNIQUE(`device_token`)
  ;