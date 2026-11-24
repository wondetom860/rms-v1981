<?php

namespace frontend\modules\SemesterEvaluation;

/**
 * SemesterEvaluation module definition class
 */
class SemesterEvalaluation extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'frontend\modules\SemesterEvaluation\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        // custom initialization code goes here
        if (!\Yii::$app->user->can('Admin')) {
            throw new \yii\web\ForbiddenHttpException("Error Processing Request", 1);
        }
    }
}
