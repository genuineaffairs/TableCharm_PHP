<ul class = "global_form_box" style="margin-bottom: 15px; padding:5px 10px 5px 10px;">
  <table style="width:100%">
     <?php foreach ($this->categories as $category): ?>
                    <tr class="advgroup_category_row">
                      <td>
                          <?php echo $this->htmlLink($category->getHref(), Engine_Api::_()->advgroup()->subPhrase($category->title,30),
                              array('class'=>'')); ?>
                          <?php if(count($category->getSubCategories()) > 0) : ?>
                            <span class="advgroup-category-collapse-control advgroup-category-collapsed"></span>
                          <?php else : ?>
                              <span class="advgroup-category-collapse-nocontrol"></span>
                          <?php endif; ?>
                      </td>
                    </tr>
                    <?php foreach ($category->getSubCategories() as $subCat) : ?>
                      <tr class="advgroup-category-sub-category">
                          <td>
                              <?php echo $this->htmlLink($subCat->getHref(), Engine_Api::_()->advgroup()->subPhrase($subCat->title,30),
                              array('class'=>'')); ?>
                          </td>
                      </tr>
                    <?php endforeach ?>
            <?php endforeach; ?>
  </table>
</ul>

