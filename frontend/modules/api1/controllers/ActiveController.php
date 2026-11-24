<?php

namespace frontend\modules\api1\Controllers;

use yii\web\ForbiddenHttpException;

class ActiveController extends \yii\rest\ActiveController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors["authenticator"]['only'] = ['create', 'update', 'delete', 'index', 'view'];
        $behaviors["authenticator"]['authMethods'] = [
            \yii\filters\auth\HttpBearerAuth::class,
        ];

        return $behaviors;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if (in_array($action, ['update', 'delete']) && $model->created_by !== \Yii::$app->user->id) {
            throw new ForbiddenHttpException('You do not have permission to change this post');
        }
    }
}
