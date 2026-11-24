<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "graduation_eligiblity".
 *
 * @property integer $id
 * @property integer $ge_adm_relId
 * @property integer $ge_prog_relId
 * @property integer $ge_num_of_F
 * @property integer $ge_min_crdthr
 * @property double $ge_min_CGPA
 * @property double $ge_min_CGPA_major
 * @property string $ge_created_at
 * @property integer $ge_created_by
 * @property string $ge_updated_at
 * @property integer $ge_updated_by
 *
 * @property Admission $geAdmRel
 * @property User $geCreatedBy
 * @property Program $geProgRel
 * @property User $geUpdatedBy
 */
class GraduationEligiblity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'graduation_eligiblity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ge_min_crdthr', 'ge_min_CGPA','ge_num_of_F','ge_created_at', 'ge_created_by'], 'required'],
            [['ge_adm_relId', 'ge_prog_relId', 'ge_num_of_F', 'ge_min_crdthr', 'ge_created_by', 'ge_updated_by'], 'integer'],
            [['ge_min_CGPA', 'ge_min_CGPA_major'], 'number'],
            [['ge_created_at', 'ge_updated_at'], 'safe'],
            [['ge_adm_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\Admission::className(), 'targetAttribute' => ['ge_adm_relId' => 'id']],
            [['ge_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User1::className(), 'targetAttribute' => ['ge_created_by' => 'id']],
            [['ge_prog_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\Program::className(), 'targetAttribute' => ['ge_prog_relId' => 'id']],
            [['ge_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User1::className(), 'targetAttribute' => ['ge_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ge_adm_relId' => Yii::t('rms', 'Admission'),
            'ge_prog_relId' => Yii::t('rms', 'Program'),
            'ge_num_of_F' => Yii::t('rms', '# F'),
            'ge_min_crdthr' => Yii::t('rms', 'Min. Crdthr'),
            'ge_min_CGPA' => Yii::t('rms', 'Min.  CGPA'),
            'ge_min_CGPA_major' => Yii::t('rms', 'Min  CGPA Major'),
            'ge_created_at' => Yii::t('rms', 'Created At'),
            'ge_created_by' => Yii::t('rms', 'Created By'),
            'ge_updated_at' => Yii::t('rms', 'Updated At'),
            'ge_updated_by' => Yii::t('rms', 'Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGeAdmRel()
    {
        return $this->hasOne(\backend\models\Admission::className(), ['id' => 'ge_adm_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGeCreatedBy()
    {
        return $this->hasOne(\common\models\User1::className(), ['id' => 'ge_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGeProgRel()
    {
        return $this->hasOne(\backend\models\Program::className(), ['id' => 'ge_prog_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGeUpdatedBy()
    {
        return $this->hasOne(\common\models\User1::className(), ['id' => 'ge_updated_by']);
    }
}
