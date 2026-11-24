<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nationality".
 *
 * @property integer $id
 * @property string $name
 */
class Nationality extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nationality';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'name' => Yii::t('rms', 'Nationality Name'),
        ];
    }
}
