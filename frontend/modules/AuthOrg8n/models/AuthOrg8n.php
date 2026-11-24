<?php

namespace frontend\modules\AuthOrg8n\models;

use Yii;
use common\models\AuthOrg8n as BaseAuthOrg8n;

class AuthOrg8n extends BaseAuthOrg8n
{

    // public function beforeSave($insert) {
    //     if ($file = UploadedFile::getInstance($this, 'logo')) {
    //         $this->logo = file_get_contents($file->tempName);
    //     }
    //     return parent::beforeSave($insert);
    // }

    public function getAction(){
        return true;
        if (\Yii::$app->user->can('Admin')) {
            $licenseRecord = $this->find()->all();

            if ($licenseRecord && count($licenseRecord) > 0) {
                if (count($licenseRecord) > 1) {
                    \Yii::$app->session->setFlash('message' , "Duplicate License Entry Found, Please contact Developers...");
                    throw new \yii\web\ForbiddenHttpException("Error Processing Request", 1);
                }

                $licenseRecord =$licenseRecord[0];
                // var_dump($licenseRecord);die();

                if (is_object($licenseRecord)) {
                    if (is_null($licenseRecord->lic_entry)) {
                        throw new \yii\web\ForbiddenHttpException("This Application is NOT licensed for your Organization. Pleasse complete your Institutin Information by clicking <a href='/AuthOrg8n/auth-org8n/create' target='_blank'> HERE</a>", 1);
                    }

                    if ($licenseRecord->licensed()) {
                        // return True;
                        return true;
                    }else{
                        throw new \yii\web\ForbiddenHttpException("This Application is NOT licensed for your Organization. Please contact developers", 1);
                    }
                }
            }

            throw new \yii\web\ForbiddenHttpException("This Application is NOT licensed for your Organization. Pleasse complete your Institutin Information by clicking <a href='/AuthOrg8n/auth-org8n/create' target='_blank'> HERE</a>", 1);
        }

        throw new \yii\web\ForbiddenHttpException("You are NOT allowed to Access this Module!", 1);
    }
}
