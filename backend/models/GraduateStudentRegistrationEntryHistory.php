<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "graduate_student_registration_entry_history".
 *
 * @property int $id
 * @property string $gphr_acadYear
 * @property string $gphr_semester
 * @property int $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string $data
 */
class GraduateStudentRegistrationEntryHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'graduate_student_registration_entry_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gphr_acadYear', 'gphr_semester', 'created_by', 'created_at', 'data'], 'required'],
            [['created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['data'], 'string'],
            [['gphr_acadYear'], 'string', 'max' => 10],
            [['gphr_semester'], 'string', 'max' => 3],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'gphr_acadYear' => Yii::t('rms', 'Acad. Year'),
            'gphr_semester' => Yii::t('rms', 'Semester'),
        ];
    }

    public function getData($modelObj){
        $mainData = (object)[
            'gphr_acadYear'=>$modelObj->gphr_acadYear,
            'gphr_semester'=>$modelObj->gphr_semester,
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered($modelObj){
        return $this->getData($modelObj) == $modelObj->data;
    }
}
