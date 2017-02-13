<div class='seaocore_members_popup seaocore_members_popup_notbs'>
  <div class='top'>
    <h3 class="heading">
      <?php  $itemCount = $this->requests->getTotalItemCount(); ?>
      <span><?php echo $this->translate(array("My Request (%d)","My Requests (%d)", $itemCount), $itemCount) ?></span>
    </h3>
  </div>
  <div class="members_friend_request">
    <ul class='item_member_list'>
      <?php if( $this->requests->getTotalItemCount() > 0 ): ?>
        <?php foreach( $this->requests as $notification ): ?>
        <?php
          try {
            $parts = explode('.', $notification->getTypeInfo()->handler);
            echo $this->action($parts[2], $parts[1], $parts[0], array('notification' => $notification));
          } catch( Exception $e ) {
            if( APPLICATION_ENV === 'development' ) {
              echo $e->__toString();
            }
            continue;
          }
        ?>
        <?php endforeach; ?>
      <?php else: ?>
        <li>
          <?php echo $this->translate("You have no requests.") ?>
        </li>
      <?php endif; ?>
    </ul>
  </div>
</div>