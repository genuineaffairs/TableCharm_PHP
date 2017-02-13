<ul class="announcements"> 
      <?php $item = $this->announcement; ?>
       <li>
            <div class="announcements_title">
              <?php echo $item->title ?>
            </div>
            <div class="profile_fields" style="margin-top:0px;">
              <h4>
                <span class="announcements_author">
                  <?php echo $this->translate('Posted by %1$s %2$s',
                                $this->htmlLink($item->getOwner()->getOwner()->getHref(), $item->getOwner()->getOwner()->getTitle()),
                                $this->timestamp($item->creation_date)) ?>
                </span>
                <img style="float:right" rel="group_announce" id="announce_more_icon_id" src="./application/modules/Advgroup/externals/images/down.jpg" onmousedown="toggleInfo('full_information','announce_more_icon_id'); return false;" />
              </h4>
            <div  id="full_information" class="announcements_body">
              <?php echo $item->body ?>
            </div>
         </div>
      </li>
</ul>
<script type="text/javascript">
  function toggleInfo(block_id,img_id){
    if(document.getElementById(block_id).style.display == 'none'){
      document.getElementById(block_id).style.display = 'block';
      document.getElementById(img_id).src = './application/modules/Advgroup/externals/images/up.jpg';
    }else{
      document.getElementById(block_id).style.display = 'none';
      document.getElementById(img_id).src = './application/modules/Advgroup/externals/images/down.jpg';
    }
  }
</script>