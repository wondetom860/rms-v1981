<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "graduation_requirment_control".
 *
 * @property integer $id
 * @property integer $gr_program_relId
 * @property double $gr_maj
 * @property double $gr_min
 * @property string $gr_created_at
 * @property string $gr_created_by
 * @property string $gr_updated_at
 * @property integer $gr_updated_by
 *
 * @property Program $grProgramRel
 */
class GraduationRequirmentControl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'graduation_requirment_control';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gr_program_relId', 'gr_maj', 'gr_min', 'gr_created_at', 'gr_created_by', 'gr_updated_at', 'gr_updated_by'], 'required'],
            [['gr_program_relId', 'gr_updated_by'], 'integer'],
            [['gr_maj', 'gr_min'], 'number'],
            [['gr_created_at', 'gr_created_by', 'gr_updated_at'], 'safe'],
            [['gr_program_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::className(), 'targetAttribute' => ['gr_program_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'gr_program_relId' => Yii::t('rms', 'Program'),
            'gr_maj' => Yii::t('rms', 'Major'),
            'gr_min' => Yii::t('rms', 'Minor'),
            'gr_created_at' => Yii::t('rms', 'Gr Created At'),
            'gr_created_by' => Yii::t('rms', 'Gr Created By'),
            'gr_updated_at' => Yii::t('rms', 'Gr Updated At'),
            'gr_updated_by' => Yii::t('rms', 'Gr Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrProgramRel()
    {
        return $this->hasOne(Program::className(), ['id' => 'gr_program_relId']);
    }
}
