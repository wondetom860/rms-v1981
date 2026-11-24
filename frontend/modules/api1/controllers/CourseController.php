<?php

namespace frontend\modules\api1\controllers;

use frontend\modules\api1\resource\Course;

// use frontend\modules\LORSQUA\models\LoqPositionDetail;
// Course
use yii\data\ActiveDataProvider;

// use yii\rest\ActiveController;

class CourseController extends ActiveController
{
    public $modelClass = \frontend\modules\api1\resource\Course::class;

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    public function prepareDataProvider()
    {
        // there must be post_id in either fields ot extra fields list, otherwise ActiveDataProvider will not be populated.
        return new ActiveDataProvider([
            'query' => \Yii::$app->request->get('id') ?
                $this->modelClass::find()
                ->andWhere([
                    'id' => \Yii::$app->request->get('id'),
                ])
                :
                $this->modelClass::find()
        ]);
    }
}
