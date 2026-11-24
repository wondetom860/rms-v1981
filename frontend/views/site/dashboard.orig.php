<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use meysampg\intldate\IntlDateTrait;
use kartik\popover\PopoverX;
// use yii\bootstrap\Modal;
use \common\components\CommonControllerFunctions as CCF;

$this->title = Yii::$app->name . ' | Dashboard';

//$this->params['breadcrumbs'][] = $this->title;
?>
<?php
PopoverX::begin([
    'header' => 'Hello world',
    'footer' => Html::button('View', ['class'=>'btn btn-primary']),
    'placement' => PopoverX::ALIGN_BOTTOM_RIGHT,
    'toggleButton' => ['class'=>'btn btn-primary btn-circle pull-right', 'style'=>'border-radius: 50%;'],
]);

echo '<p class="text-justify">' .
    'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.' . 
    '</p>';

PopoverX::end();
?>
<div class="site-dashboard">
    <h2>Dashboard</h2>
</div>