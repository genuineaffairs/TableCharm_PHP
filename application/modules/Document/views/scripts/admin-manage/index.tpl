<script type="text/javascript">
 en4.core.runonce.add(function(){
     $$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){
         var checked = $(this).checked;
         var checkboxes =$$('td.document_check input[type=checkbox]');
         checkboxes.each(function(item,index){
         item.checked = checked;
        });
     })
 });

function actionSelected(actionType){
    var checkboxes = $$('td.document_check input[type=checkbox]');
    var selecteditems = [];

    checkboxes.each(function(item){
      var checked = item.checked;
      var value = item.value;
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });
    $('action_selected').action = en4.core.baseUrl +'admin/document/manage/' + actionType + '-selected';
    $('ids').value = selecteditems;
    $('action_selected').submit();
  }
</script>

<h2>
  <?php echo $this->translate("Documents Plugin") ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("DOCUMENT_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>
<br/>
<?php if( count($this->paginator) ): ?>
  <table class='document_admin_tbl admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
        <th><?php echo $this->translate("ID") ?></th>
        <th><?php echo $this->translate("Title") ?></th>
        <th><?php echo $this->translate("Owner") ?></th>
        <th><?php echo $this->translate("Date") ?></th>
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td class="document_check"><input type='checkbox' class='checkbox' value='<?php echo $item->document_id ?>' /></td>
          <td><?php echo $item->document_id ?></td>
          <td><?php echo $item->title ?></td>
          <td><?php echo $this->user($item->owner_id);?></td>
          <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
          <td>
            <?php
                echo $this->htmlLink(
                    $item->getFilePath(),
                    $this->translate("download"));
            ?>
            |
            <?php
                echo $this->htmlLink(
                    $item->getHref(),
                    $this->translate("view"));
            ?>
            |
            <?php
                echo $this->htmlLink(
                    array('route' => 'default', 'module' => 'document', 'controller' => 'admin-manage', 'action' => 'delete', 'id' => $item->document_id),
                    $this->translate("delete"),
                    array('class' => 'smoothbox'));
            ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <br />
  <div class='buttons'>
    <button type='button' onclick="javascript:actionSelected('delete');"><?php echo $this->translate("Delete Selected") ?></button>
  </div>
  <br />
  <form id='action_selected' method="post" action="">
       <input type="hidden" id="ids" name="ids" value=""/>
  </form>
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no documents posted by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>