<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "user_feedback".
 *
 * @property integer $id
 * @property integer $fb_user_relId
 * @property string $fb_file_name
 * @property double $fb_rate
 * @property string $fb_created_at
 * @property integer $fb_updated_by
 * @property string $fb_updated_at
 *
 * @property User $fbUserRel
 */
class UserFeedback extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_feedback';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fb_user_relId', 'fb_updated_by'], 'integer'],
            [['fb_file_name', 'fb_rate', 'fb_created_at',], 'required'],
            [['fb_rate'], 'number'],
            [['fb_created_at', 'fb_updated_at'], 'safe'],
            [['fb_file_name'], 'string', 'max' => 255],
            [['fb_user_relId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['fb_user_relId' => 'id']],
        ];
    }

    public function saveToFile($htmltext,$fileName){
        try{
            $file = fopen('uploads/feedbacks/'.$fileName.'.txt', "w");
            fwrite($file, $htmltext);
            fclose($file);
            return true;
        }catch(\Exception $e){
            var_dump($e);
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'fb_user_relId' => Yii::t('rms', 'Fb User Rel ID'),
            'fb_file_name' => Yii::t('rms', 'Fb File Name'),
            'fb_rate' => Yii::t('rms', 'Fb Rate'),
            'fb_created_at' => Yii::t('rms', 'Fb Created At'),
            'fb_updated_by' => Yii::t('rms', 'Fb Updated By'),
            'fb_updated_at' => Yii::t('rms', 'Fb Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFbUserRel()
    {
        return $this->hasOne(User::class, ['id' => 'fb_user_relId']);
    }
}
