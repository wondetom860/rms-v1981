<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use meysampg\intldate\IntlDateTrait;
use kartik\popover\PopoverX;
// use yii\bootstrap\Modal;
use \common\components\CommonControllerFunctions as CCF;

$this->title = Yii::$app->name . ' | Dashboard';

$this->params['breadcrumbs'][] = $this->title;
?>
<?php
// PopoverX::begin([
//     'header' => 'Hello world',
//     'footer' => Html::button('View', ['class'=>'btn btn-primary']),
//     'placement' => PopoverX::ALIGN_BOTTOM_RIGHT,
//     'toggleButton' => ['class'=>'btn btn-primary btn-circle pull-right', 'style'=>'border-radius: 50%;'],
// ]);

// echo '<p class="text-justify">' .
//     'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.' . 
//     '</p>';

// PopoverX::end();
?>
<div class="site-dashboard">
    <div class="row">
        <div class="col-md-7">
            <div class="panel panel-default">
                <div class="panel-heading">
                    New Admission - Year wise
                </div>
                <div class="panel-body">
                    <?= $this->render('_pages/dashboard/charts/new-admission-year-wise') ?>
                </div>
                <div class="panel-footer">

                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Tabular Form
                </div>
                <div class="panel-body">
                    <?= $this->render('_pages/dashboard/tables/new-admission-year-wise') ?>
                </div>
                <div class="panel-footer">

                </div>
            </div>
        </div>
    </div>
    <!-- end of year-gender-wise report -->

    <div class="row">
        <div class="col-md-7">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Yearly Intake Report - Military Profession Wise
                </div>
                <div class="panel-body">
                    <?= $this->render('_pages/dashboard/charts/new-admission-profession-wise') ?>
                </div>
                <div class="panel-footer">

                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Tabular Form
                </div>
                <div class="panel-body">
                    <?= $this->render('_pages/dashboard/tables/new-admission-profession-wise') ?>
                </div>
                <div class="panel-footer">

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            <div class="panel panel-success">
                <div class="panel-heading">
                    Enrollments - ACY wise (Academic Year - Semester)
                </div>
                <div class="panel-body">
                    <?= $this->render('_pages/dashboard/tables/enrollment-acy-wise') ?>
                </div>
                <div class="panel-footer">

                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="panel panel-success">
                <div class="panel-heading">
                    Enrollments - Class Level (Academic Year - Semester : Class level)
                </div>
                <div class="panel-body">
                    <?= $this->render('_pages/dashboard/tables/enrollment-classlevel-wise') ?>
                </div>
                <div class="panel-footer">

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-7">
            <div class="panel panel-info">
                <div class="panel-heading">
                    Semester Registrations - Department (Stream/Specialization) Wise
                </div>
                <div class="panel-body">
                    <?= $this->render('_pages/dashboard/tables/enrollments-dept-wise') ?>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="panel panel-info">
                <div class="panel-heading">
                    Registration Summary - Program - Class Level Wise
                </div>
                <div class="panel-body">
                    <?= $this->render('_pages/dashboard/tables/enrollments-program-clslvl-wise') ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    Semester Status Summary (Active Academic Year)
                </div>
                <div class="panel-body">
                    <?= $this->render('_pages/dashboard/tables/semester-status-summery') ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    Semester Status Summary (All Academic Year) : Regular Admissions
                </div>
                <div class="panel-body">
                    <?= $this->render('_pages/dashboard/tables/semester-status-summery-acy') ?>
                </div>
            </div>
        </div>
    </div>
</div>