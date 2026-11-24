<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "cocat_detail".
 *
 * @property integer $id
 * @property integer $cocatDet_cocat_relId
 * @property integer $cocatDet_course_relId
 * @property integer $cocatDet_created_by
 * @property string $cocatDet_created_at
 * @property integer $cocatDet_updated_by
 * @property string $cocat|Det_updated_at
 *
 * @property CourseCatalog $cocatDetCocatRel
 * @property Course $cocatDetCourseRel
 * @property User $cocatDetCreatedBy
 * @property User $cocatDetUpdatedBy
 */
class CocatDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cocat_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'cocatDet_cocat_relId', 'cocatDet_course_relId', 'cocatDet_created_by', 'cocatDet_created_at', 'cocatDet_updated_by', 'cocat|Det_updated_at'], 'required'],
            [['id', 'cocatDet_cocat_relId', 'cocatDet_course_relId', 'cocatDet_created_by', 'cocatDet_updated_by'], 'integer'],
            [['cocatDet_created_at', 'cocat|Det_updated_at'], 'safe'],
            [['cocatDet_cocat_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseCatalog::className(), 'targetAttribute' => ['cocatDet_cocat_relId' => 'id']],
            [['cocatDet_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['cocatDet_course_relId' => 'id']],
            [['cocatDet_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['cocatDet_created_by' => 'id']],
            [['cocatDet_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['cocatDet_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'cocatDet_cocat_relId' => Yii::t('rms', 'Cocat Det Cocat Rel ID'),
            'cocatDet_course_relId' => Yii::t('rms', 'Cocat Det Course Rel ID'),
            'cocatDet_created_by' => Yii::t('rms', 'Cocat Det Created By'),
            'cocatDet_created_at' => Yii::t('rms', 'Cocat Det Created At'),
            'cocatDet_updated_by' => Yii::t('rms', 'Cocat Det Updated By'),
            'cocat|Det_updated_at' => Yii::t('rms', 'Cocat| Det Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCocatDetCocatRel()
    {
        return $this->hasOne(CourseCatalog::className(), ['id' => 'cocatDet_cocat_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCocatDetCourseRel()
    {
        return $this->hasOne(Course::className(), ['id' => 'cocatDet_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCocatDetCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'cocatDet_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCocatDetUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'cocatDet_updated_by']);
    }
}
