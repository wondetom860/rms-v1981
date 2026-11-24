<?php

namespace frontend\modules\AcademicCalendar\controllers;

use yii\web\Controller;
use yii\filters\VerbFilter;
use \frontend\modules\AcademicCalendar\models\AcActivePrograms;
use \backend\models\CourseOffering2;

/**
 * Default controller for the `AcademicCalendar` module
 */
class DefaultController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],

            'access' => [
               'class' => \yii\filters\AccessControl::class,
               // We will override the default rule config with the new AccessRule class
               'ruleConfig' => [
                   'class' => \yii\filters\AccessRule::class,
               ],
               'rules' => [
                   [
                       'actions' => ['index','delete','change-status','get-report-print-backlog'],
                       'allow' => true,
                       'roles' => ['Admin'],
                   ],
                   [
                       'actions' => ['index','change-status','get-report-print-backlog'],
                       'allow' => true,
                       'roles' => ['Registrar'],
                   ],[
                       'actions' => ['index'],
                       'allow' => true,
                       'roles' => ['DepartmentHead'],
                   ],
               ]
           ]
        ];
    }

    /**
     * Renders the index view for the module
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        AcActivePrograms::linkGsets();
        CourseOffering2::updateAcEntry();
        \common\models\User::cleanDuplicateHoDAccount();
        
        if (\Yii::$app->user->can('DepartmentHead')) {
            return $this->redirect('/AcademicCalendar/ac-active-programs');
        }
        // 
        return $this->render('index');
    }
}
