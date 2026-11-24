<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "attendancedetail".
 *
 * @property integer $id
 * @property integer $ad_attendance_relId
 * @property integer $ad_student_relId
 * @property integer $ad_status
 * @property string $ad_remark
 *
 * @property Student $adStudentRel
 * @property Attendance $adAttendanceRel
 */
class Attendancedetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'attendancedetail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ad_attendance_relId', 'ad_student_relId', 'ad_status'], 'integer'],
            [['ad_remark'], 'string'],
            [['ad_student_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['ad_student_relId' => 'id']],
            [['ad_attendance_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Attendance::className(), 'targetAttribute' => ['ad_attendance_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ad_attendance_relId' => Yii::t('rms', 'Ad Attendance Rel ID'),
            'ad_student_relId' => Yii::t('rms', 'Ad Student Rel ID'),
            'ad_status' => Yii::t('rms', 'Ad Status'),
            'ad_remark' => Yii::t('rms', 'Ad Remark'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdStudentRel()
    {
        return $this->hasOne(Student::className(), ['id' => 'ad_student_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdAttendanceRel()
    {
        return $this->hasOne(Attendance::className(), ['id' => 'ad_attendance_relId']);
    }
}
