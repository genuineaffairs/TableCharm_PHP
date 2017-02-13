<div class='form-wrapper'>
  <div class='form-logo'><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Zulu/externals/images/Health_Form_DSG-000.jpg' /></div>
  <div class="form-header">HEALTH FORM 2013</div>
  <div class="form-body">
    <p class="single-line mbot15">
      <span class="label">PUPIL: </span><span class="answer bigwidth"><?php echo $this->userMetaData->getRowMatching(array('label' => 'First Name'))->getValue($this->user)->value ?> <?php echo $this->userMetaData->getRowMatching(array('label' => 'Last Name'))->getValue($this->user)->value ?></span>
    </p>
    <p class="single-line mbot15">
      <span class="label">DATE OF BIRTH: </span><span class="answer"><?php echo date($this->dateFormat, strtotime($this->userMetaData->getRowMatching(array('label' => 'Birthday'))->getValue($this->user)->value)) ?></span>
      <span class="label">GRADE (2013): </span><span class="answer">&nbsp;</span>
    </p>
    <p class="underline">To be completed by new pupils only</p>
    <p class="fontweightnormal normal mbot15">Please mark the applicable boxes below:</p>

    <!--DISEASES-->
    <p class="normal">DISEASES which your child has had:</p>
    <table class="mbot15">
      <tr>
        <th>Childhood diseases</th>
        <th>Specify which and what year</th>
      </tr>
      <!--Dynamic Content-->
      <?php foreach($this->formPartData['childhoodDiseases'] as $disease) : ?>
        <?php if($disease['currentlyAffected'] === false) : ?>
        <tr>
          <td><?php echo $disease['label']; ?></td>
          <td><?php echo $disease['date']; ?></td>
        </tr>
        <?php endif; ?>
      <?php endforeach; ?>
      <!--/Dynamic Content-->
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
    </table>
    <!--/DISEASES-->

    <!--IMMUNISATIONS-->
    <p class="normal">Routine childhood immunisations:</p>
    <table class="mbot15">
      <tr>
        <td>Up to date</td>
        <td class='mediumwidth'>Yes</td>
        <td>No</td>
        <td>&nbsp;</td>
      </tr>
      <!--Dynamic Content-->
      <?php foreach($this->formPartData['immunisations'] as $immunisation) : ?>
        <tr>
          <td><?php echo $immunisation['label'] ?></td>
          <td>Yes</td>
          <td>Date: <?php echo $immunisation['date'] ?></td>
          <td>&nbsp;</td>
        </tr>
      <?php endforeach; ?>
      <!--/Dynamic Content-->
      <tr>
        <td>Other</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table>
    <!--/IMMUNISATIONS-->

    <!--ALLERGIES-->
    <p class="normal">ALLERGIES: (It is essential that we know how severe the allergy is and what treatment to give)</p>
    <table class="mbot15">
      <!--Dynamic Content-->
      <?php foreach($this->formPartData['allergies'] as $allergy) : ?>
        <tr>
          <td><?php echo $allergy['label']; ?></td>
          <td>&nbsp;</td>
          <td>Treatment: <?php echo $allergy['actionTaken']; ?></td>
        </tr>
      <?php endforeach; ?>
      <!--/Dynamic Content-->
      <tr>
        <td>Other</td>
        <td class='mediumwidth'>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table>
    <!--/ALLERGIES-->

    <!--OPERATIONS-->
    <p class="normal mbot15">NB: A MEDIC ALERT BRACELET MUST BE WORN WHERE ALLERGY OR ILLNESS IS A DANGER TO LIFE, eg  SEVERE BEESTING ALLERGY, ASTHMA OR EPILEPSY</p>
    <p class="normal">OPERATIONS:</p>
    <table class="mbot15">
      <tr>
        <td>(Please list dates and details):</td>
      </tr>
      <!--Dynamic Content-->
      <?php if(count($this->formPartData['operations']) > 0) : ?>
        <?php foreach($this->formPartData['operations'] as $operation) : ?>
          <?php
          $sep = '';
          if($operation['date'] && $operation['details']) {
            $sep = ' - ';
          } ?>
          <tr>
            <td><?php echo $operation['date'] ?><?php echo $sep; ?><?php echo $operation['details'] ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <?php for($i = 0; $i < 3; $i++) : ?>
          <tr>
            <td>&nbsp;</td>
          </tr>
        <?php endfor; ?>
      <?php endif; ?>
      <!--/Dynamic Content-->
    </table>
    <!--/OPERATIONS-->

    <div style="page-break-after:always;"></div>

    <!--CURRENT DISEASES-->
    <p class="normal mbot15 fontweightnormal">Does your child suffer from:</p>
    <table class="mbot15">
      <tr>
        <td>Diabetes</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>Epilepsy</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>Any other illnesses</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2">Details:</td>
      </tr>
      <!--Dynamic Content-->
      <?php
      $this->formPartData['currentDiseases'] =  array_filter($this->formPartData['childhoodDiseases'], function($el) {
                                                  return (bool)$el['currentlyAffected'];
                                                });
      ?>
      <?php if(count($this->formPartData['currentDiseases']) > 0) : ?>
        <?php foreach($this->formPartData['currentDiseases'] as $disease) : ?>
          <tr>
            <td colspan="2"><?php echo $disease['label']; ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <?php for($i = 0; $i < 3; $i++) : ?>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
        <?php endfor; ?>
      <?php endif; ?>
      <!--/Dynamic Content-->
    </table>
    <!--/CURRENT DISEASES-->

    <!--MEDICATIONS-->
    <table class="mbot15">
      <tr>
        <td>Is your child on medication?</td>
        <td class="mediumwidth"><?php echo ($this->formPartData['medications']['taking_medications'] ? 'Yes' : 'No'); ?></td>
      </tr>
      <tr>
        <td colspan="2">If so, please list medication and dosage:</td>
      </tr>
      <!--Dynamic Content-->
      <?php if(count($this->formPartData['medications']['list']) > 0) : ?>
        <?php foreach($this->formPartData['medications']['list'] as $medication) : ?>
          <?php if($medication['medication']) : ?>
          <tr>
            <td colspan="2">
              Medication: <?php echo $medication['Medication']; ?><br />
              Dosage: <?php echo $medication['Dosage']; ?>
            </td>
          </tr>
          <?php endif; ?>
        <?php endforeach; ?>
      <?php else: ?>
        <?php for($i = 0; $i < 3; $i++) : ?>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
        <?php endfor; ?>
      <?php endif; ?>
      <!--/Dynamic Content-->
    </table>
    <!--/MEDICATIONS-->

    <!--OTHERS-->
    <table class="mbot15">
      <tr>
        <td>Date of last visit to the dentist</td>
        <td colspan="2"><?php if(isset($this->formPartData['others']['last_visit_dentist']['value'])) echo date($this->dateFormat, strtotime($this->formPartData['others']['last_visit_dentist']['value'])) ?></td>
      </tr>
      <tr>
        <td>Does your child wear glasses?</td>
        <td class='mediumwidth'><?php if(isset($this->formPartData['others']['wear_glasses']['value'])) echo $this->formPartData['others']['wear_glasses']['value'] ?></td>
        <td>Reason: <?php if(isset($this->formPartData['others']['wear_glasses']['reason'])) echo $this->formPartData['others']['wear_glasses']['reason'] ?></td>
      </tr>
      <tr>
        <td>Does your child wear contact lenses?</td>
        <td class='mediumwidth'><?php if(isset($this->formPartData['others']['wear_lenses']['value'])) echo $this->formPartData['others']['wear_lenses']['value'] ?></td>
        <td>Reason: <?php if(isset($this->formPartData['others']['wear_lenses']['reason'])) echo $this->formPartData['others']['wear_lenses']['reason'] ?></td>
      </tr>
      <tr>
        <td colspan="2">Has your child ever seen a Psychologist or Psychiatrist?</td>
        <td><?php if(isset($this->formPartData['others']['see_psychologist']['value'])) echo $this->formPartData['others']['see_psychologist']['value'] ?></td>
      </tr>
      <tr>
        <td colspan='3'>
          Please discuss with Sister or school psychologist, so that we can provide the necessary support
        </td>
      </tr>
      <tr>
        <td colspan='3'>
          <?php if(isset($this->formPartData['others']['discuss_psychologist']['value'])) echo $this->formPartData['others']['discuss_psychologist']['value']; else echo '&nbsp;'; ?>
        </td>
      </tr>
    </table>
    <!--/OTHERS-->
    
    <p class="normal fontweightnormal">This form will be treated as highly confidential.</p>
  </div>
</div>