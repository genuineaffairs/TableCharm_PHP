<div id="printButtons">
  <?php if($this->is_allow_read_full) : ?>
  <div>
    <label for="mode" class="optional">Please select which parts you want to print</label>
  </div>
  <div>
    <form id='printForm' method='get'>
      <select id="mode" name="mode">
        <option <?php if(!$this->show_hidden) echo "selected='selected'" ?> value="emergency">Emergency</option>
        <option <?php if($this->show_hidden) echo "selected='selected'" ?> value="all">All Sections</option>
      </select>
    </form>
  </div>
  <?php endif; ?>
  <div>
    <a href="javascript:window.print();" class="link_button">Print</a>
    <?php echo $this->htmlLink($this->user->getParent()->getHref() . '/tab/' . Engine_Api::_()->zulu()->getClinicalProfileTabId(), 'Go Back', array(
      'class' => 'link_button',
    )); ?>
  </div>
  <script type='text/javascript'>
    $('#mode').change(function() {
      $('#printForm').submit();
    });
  </script>
</div>

<div class='form-wrapper generic'>
  <div class="dark-background mbot_big_gap">
    <img src="<?php echo $this->baseUrl() ?>application/modules/Zulu/externals/images/dark-grey.png" width="600px" height="65px" />
    <h3 class="medical_record_heading_print">
      <img src="<?php echo $this->baseUrl() ?>application/modules/Zulu/externals/images/medical-record-logo.png" />
    </h3>
  </div>
  <div class="form-body">
    <p class="single-line mbot15">
      <span class="label">NAME: </span><span class="answer bigwidth"><?php echo $this->userMetaData->getRowMatching(array('label' => 'First Name'))->getValue($this->user)->value ?> <?php echo $this->userMetaData->getRowMatching(array('label' => 'Last Name'))->getValue($this->user)->value ?></span>
    </p>
    <p class="single-line mbot15">
      <span class="label">DATE OF BIRTH: </span><span class="answer bigwidth"><?php echo date($this->dateFormat, strtotime($this->userMetaData->getRowMatching(array('alias' => 'birthdate'))->getValue($this->user)->value)) ?></span>
    </p>

    <!--INTERNATIONAL TRAVEL INSURANCE-->
    <?php if(count($this->formPartData['travel_insurance']['headings'])) : ?>
    <div class="display_group">
      <p class="normal section_heading">INTERNATIONAL TRAVEL INSURANCE:</p>
      <table class="mbot15">
        <tr>
          <?php foreach($this->formPartData['travel_insurance']['headings'] as $heading) : ?>
          <th>
            <?php echo $heading; ?>
          </th>
          <?php endforeach; ?>
        </tr>
        <?php if(count($this->formPartData['travel_insurance']['rows'])) : ?>
          <!--Dynamic Content-->
          <?php foreach($this->formPartData['travel_insurance']['rows'] as $row) : ?>
          <tr>
            <?php foreach($row as $column) : ?>
            <td>
              <?php echo $column; ?>
            </td>
            <?php endforeach; ?>
          </tr>
          <?php endforeach; ?>
          <!--/Dynamic Content-->
        <?php else: ?>
          <?php for($i = 0; $i < 3; $i++) : ?>
          <tr>
            <?php foreach($this->formPartData['travel_insurance']['headings'] as $heading) : ?>
            <td>&nbsp;</td>
            <?php endforeach; ?>
          </tr>
          <?php endfor; ?>
        <?php endif; ?>
      </table>
    </div>
    <?php endif; ?>
    <!--/INTERNATIONAL TRAVEL INSURANCE-->
    
    <!--NEXT OF KIN-->
    <?php if(count($this->formPartData['next_of_kin']['headings'])) : ?>
    <div class="display_group">
      <p class="normal section_heading">NEXT OF KIN:</p>
      <table class="mbot15">
        <tr>
          <?php foreach($this->formPartData['next_of_kin']['headings'] as $heading) : ?>
          <th>
            <?php echo $heading; ?>
          </th>
          <?php endforeach; ?>
        </tr>
        <?php if(count($this->formPartData['next_of_kin']['rows'])) : ?>
        <!--Dynamic Content-->
        <?php foreach($this->formPartData['next_of_kin']['rows'] as $row) : ?>
        <tr>
          <?php foreach($row as $column) : ?>
          <td>
            <?php echo $column; ?>
          </td>
          <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
        <!--/Dynamic Content-->
        <?php else: ?>
          <?php for($i = 0; $i < 3; $i++) : ?>
          <tr>
            <?php foreach($this->formPartData['next_of_kin']['headings'] as $heading) : ?>
            <td>&nbsp;</td>
            <?php endforeach; ?>
          </tr>
          <?php endfor; ?>
        <?php endif; ?>
      </table>
    </div>
    <?php endif; ?>
    <!--/NEXT OF KIN-->
    
    <!--BLOOD TYPE-->
    <div class="display_group">
      <p class="normal section_heading">BLOOD TYPE:</p>
      <table class="mbot15">
        <tr>
          <td><?php echo $this->formPartData['blood_type']['heading'] ?></td>
          <td><?php echo $this->formPartData['blood_type']['value'] ?></td>
        </tr>
      </table>
    </div>
    <!--/BLOOD TYPE-->
    
    <!--MEDICATIONS-->
    <?php $heading_count = count($this->formPartData['medications']['headings']); ?>
    <div class="display_group medications">
      <p class="normal section_heading">MEDICATIONS:</p>
      <table class="mbot15">
        <tr>
          <td colspan="<?php echo ($heading_count > 1 ? ($heading_count-1) : 1) ?>">Are you taking any medications? Include over the counter drugs from your pharmacy or supermarket, vitamins, and dietary supplements.</td>
          <td class="mediumwidth"><?php echo ($this->formPartData['medications']['taking_medications'] ? 'Yes' : 'No'); ?></td>
        </tr>
        <?php if($this->formPartData['medications']['taking_medications']) : ?>
          <tr>
            <td colspan="<?php echo ($heading_count > 1 ? $heading_count : 2) ?>">Please list the medication:</td>
          </tr>
          <!--Dynamic Content-->
          <?php if(count($this->formPartData['medications']['list']) && $heading_count) : ?>
            <tr>
            <?php foreach($this->formPartData['medications']['headings'] as $heading) : ?>
              <th><?php echo $heading; ?></th>
            <?php endforeach; ?>
            </tr>
            <?php foreach($this->formPartData['medications']['list'] as $medication) : ?>
            <tr>
              <?php foreach($medication as $key => $value) : ?>
              <td><?php echo $value; ?></td>
              <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <?php for($i = 0; $i < 3; $i++) : ?>
            <tr>
              <td colspan="2">&nbsp;</td>
            </tr>
            <?php endfor; ?>
          <?php endif; ?>
        <?php endif; ?>
        <!--/Dynamic Content-->
      </table>
    </div>
    <!--/MEDICATIONS-->

    <!--ALLERGIES-->
    <div class="display_group allergies">
      <p class="normal section_heading">ALLERGIES:</p>
      <table class="mbot15">
        <?php if(count($this->formPartData['allergies'])) : ?>
          <!--Dynamic Content-->
          <?php foreach($this->formPartData['allergies'] as $allergy) : ?>
          <tr>
            <td><?php echo $allergy['label']; ?></td>
            <td>
              <?php unset($allergy['label']); ?>
              <?php foreach($allergy as $key => $value) : ?>
              <span class="label"><?php echo $key; ?></span><span class="label">:</span><br /> <span><?php echo $value; ?></span><br />
              <?php endforeach; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <!--/Dynamic Content-->
        <?php else: ?>
          <tr>
            <td>Other</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        <?php endif; ?>
      </table>
    </div>
    <!--/ALLERGIES-->
    
    <!--OVERSEAS TRAVEL-->
    <div class="display_group">
      <p class="normal section_heading">OVERSEAS TRAVEL:</p>
      <table class="mbot15">
        <!--Dynamic Content-->
        <?php foreach($this->formPartData['overseas_travel'] as $key => $item) : ?>
        <tr>
          <td><?php echo $key; ?></td>
          <td><?php echo $item; ?></td>
        </tr>
        <?php endforeach; ?>
        <!--/Dynamic Content-->
      </table>
    </div>
    <!--/OVERSEAS TRAVEL-->
    
    <!--IMMUNISATIONS-->
    <?php $heading_count = count($this->formPartData['immunisations']['headings']); ?>
    <div class="display_group">
      <p class="normal section_heading">IMMUNISATIONS:</p>
      <table class="mbot15">
        <?php if($heading_count > 0) : ?>
          <tr>
          <?php foreach($this->formPartData['immunisations']['headings'] as $heading) : ?>
            <th><?php echo (!is_numeric($heading) ? $heading : ''); ?></th>
          <?php endforeach; ?>
          </tr>
          <!--Dynamic Content-->
          <?php foreach($this->formPartData['immunisations'] as $immunisation) : ?>
            <tr>
            <?php foreach($immunisation['items'] as $key => $value) : ?>
              <td><?php echo $value; ?></td>
            <?php endforeach; ?>
            </tr>
          <?php endforeach; ?>
          <!--/Dynamic Content-->
        <?php else: ?>
        <tr>
          <td>Other</td>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td></td>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td></td>
          <td colspan="2">&nbsp;</td>
        </tr>
        <?php endif; ?>
      </table>
    </div>
    <!--/IMMUNISATIONS-->
    
    <?php if($this->show_hidden) : ?>
    <!--PERSONAL HISTORY-->
    <div class="display_group">
      <p class="normal section_heading">PERSONAL HISTORY:</p>
      <table class="mbot15">
        <?php if(count($this->formPartData['personal_history'])) : ?>
          <!--Dynamic Content-->
          <?php foreach($this->formPartData['personal_history'] as $disease) : ?>
            <tr>
              <td>
              <?php $first_item = 1; ?>
              <?php foreach($disease as $key => $item) : ?>
                <span class="label"><?php echo $key; ?></span><span class="label">:</span> <span <?php if($first_item) : ?>class='fontweightbold'<?php endif; ?>><?php echo $item; ?></span><br />
                <?php $first_item = 0; ?>
              <?php endforeach; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          <!--/Dynamic Content-->
        <?php else: ?>
          <tr>
            <td>
              Other:
              <br><br><br><br>
            </td>
          </tr>
        <?php endif; ?>
      </table>
    </div>
    <!--/PERSONAL HISTORY-->
    
    <!--PHYSICAL HISTORY-->
    <div class="display_group">
      <p class="normal section_heading">PHYSICAL HISTORY:</p>
      <table class="mbot15">
        <?php foreach($this->formPartData['physical_history'] as $key => $items) : ?>
          <tr>
            <td><span class='fontweightbold'><?php echo $key ?></span></td>
            <td><?php echo $items['value'] ?></td>
          </tr>
          <?php if(trim($items['value']) === 'Yes') : ?>
            <?php if(count($items['list'])) : ?>
              <?php foreach($items['list'] as $subitem) : ?>
              <tr>
                <td colspan="2">
                  <?php foreach($subitem as $subkey => $value) : ?>
                    <?php if(!is_numeric($subkey) && $value) : ?>
                    <span class="label"><?php echo $subkey; ?></span><span class="label">:</span> <span><?php echo $value; ?></span><br />
                    <?php endif; ?>
                  <?php endforeach; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <?php for($i = 0; $i < 3; $i++) : ?>
                <tr>
                  <td colspan="2">&nbsp;</td>
                </tr>
              <?php endfor; ?>
            <?php endif; ?>
          <?php endif; ?>
        <?php endforeach; ?>
      </table>
    </div>
    <!--/PHYSICAL HISTORY-->
    
    <!--FAMILY HISTORY-->
    <div class="display_group">
      <p class="normal section_heading">FAMILY HISTORY:</p>
      <table class="mbot15">
        <?php if(count($this->formPartData['family_history'])) : ?>
          <!--Dynamic Content-->
          <?php foreach($this->formPartData['family_history'] as $disease) : ?>
            <tr>
              <td>
              <?php $first_item = 1; ?>
              <?php foreach($disease as $key => $item) : ?>
              <span class="label"><?php echo $key; ?></span><span class="label">:</span><span <?php if($first_item) : ?>class='fontweightbold'<?php endif; ?>><?php echo $item; ?></span><br />
                <?php $first_item = 0; ?>
              <?php endforeach; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          <!--/Dynamic Content-->
        <?php else: ?>
          <tr>
            <td>
              Please fill in the details:
              <br><br><br><br>
            </td>
          </tr>
        <?php endif; ?>
      </table>
    </div>
    <!--/FAMILY HISTORY-->
    
    <!--LIFESTYLE HISTORY-->
    <div class="display_group">
      <p class="normal section_heading">LIFESTYLE HISTORY:</p>
      <table class="mbot15">
        <?php if(count($this->formPartData['lifestyle_history'])) : ?>
          <!--Dynamic Content-->
          <?php foreach($this->formPartData['lifestyle_history'] as $key => $item) : ?>
            <tr>
              <td class='bigwidth'>
                <span class="label"><?php echo $key; ?></span>
              </td>
              <td>
                <span><?php echo $item; ?></span>
              </td>
            </tr>
          <?php endforeach; ?>
          <!--/Dynamic Content-->
        <?php else: ?>
          <tr>
            <td>
              Please fill in the details:
              <br><br><br><br>
            </td>
          </tr>
        <?php endif; ?>
      </table>
    </div>
    <!--/LIFESTYLE HISTORY-->
    <?php endif; ?>

    <p class="normal fontweightnormal">This form will be treated as highly confidential.</p>
  </div>
</div>