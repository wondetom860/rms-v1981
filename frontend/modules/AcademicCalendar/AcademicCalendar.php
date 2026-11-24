<?php

namespace frontend\modules\AcademicCalendar;

use yii\filters\VerbFilter;
// use frontend\modules\AcademicCalendar\models\AcActivePrograms;
/**
 * AcademicCalendar module definition class
 */
class AcademicCalendar extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'frontend\modules\AcademicCalendar\controllers';

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
                       'actions' => [],
                       'allow' => true,
                       'roles' => ['@'],
                   ],
               ]
           ]
        ];
    }
}
