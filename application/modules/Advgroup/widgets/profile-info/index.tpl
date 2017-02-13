<script type="text/javascript">
 var pageAction =function(id){
    $('category_id').value = id;
    $('filter_form').submit();
  }

 function toggleDesc(block_id,img_id){
    if(document.getElementById(block_id).style.display == 'none'){
      document.getElementById(block_id).style.display = 'block';
      document.getElementById(img_id).src = './application/modules/Advgroup/externals/images/up.jpg';
    }else{
      document.getElementById(block_id).style.display = 'none';
      document.getElementById(img_id).src = './application/modules/Advgroup/externals/images/down.jpg';
    }
  }
</script>
<ul class="group_info">
  <li class="group_stats_title">
    <span>
      <h3>
         <?php echo $this->group->getTitle() ?>
      </h3>
    </span>
  </li>

  <li>
  <div class="profile_fields">
      <h4><span><?php echo $this->translate("Basic Information");?></span></h4>
      <?php if( !empty($this->group->category_id) ):
              $group = $this->group;
              $category = $group->getCategory();
              if($category):?>
                  <ul style="padding: 0px 0px 0px 10px;">
                    <li>
                      <span><?php echo $this->translate("Category")?></span>
                      <a href ="<?php echo $category->getHref()?>" style="font-weight: bold; background: none; border: none; color: #5F93B4; padding:0px; margin:0px;">
                            <?php echo $category->title;?>
                      </a>
                    </li>
                  </ul>
               <?php endif; ?>
      <?php endif;?>
       <?php if($this->fieldStructure):?>
         <?php echo $this->fieldValueLoop($this->group, $this->fieldStructure) ?>
      <?php endif;?>
  </div>
  </li>
  <?php if( '' !== ($description = $this->group->description) ): ?>
  <li>
    <div class="profile_fields">
      <h4 id="group_desc">
          <span>
            <?php echo $this->translate("Description")?>
          </span>
        <img alt=""  style="float:right;" rel="group_desc" id="desc_more_icon_id" src="./application/modules/Advgroup/externals/images/up.jpg" onmousedown="toggleDesc('full_description','desc_more_icon_id'); return false;" />
      </h4>
      <div id="full_description" class="group_stats_description">
         <?php echo $description; ?>
      </div>
    </div>
   </li>
  <?php endif; ?>
</ul>