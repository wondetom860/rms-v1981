<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
<?= $form->field($model, 'image')->fileInput(['required' => true])->label('Reassessment report')
    ->hint('Reassessment report after submission reassessor mark shall be attached with the request for further verification.') ?>
<div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success float-right']) ?>
</div>
<?php ActiveForm::end() ?>