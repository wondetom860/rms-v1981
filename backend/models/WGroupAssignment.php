<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "w_group_assignment".
 *
 * @property integer $id
 * @property integer $gas_gset_relId
 * @property integer $gas_std_relId
 * @property string $gas_created_at
 *
 * @property WGroupSetup $gasGsetRel
 * @property Student $gasStdRel
 */
class WGroupAssignment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'w_group_assignment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gas_gset_relId', 'gas_std_relId', 'gas_created_at'], 'required'],
            [['gas_gset_relId', 'gas_std_relId'], 'integer'],
            [['gas_created_at'], 'safe'],
            [['gas_gset_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupSetup::class, 'targetAttribute' => ['gas_gset_relId' => 'id']],
            [['gas_std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['gas_std_relId' => 'id']],
        ];
    }

    public function getUnEnrolledStudents($acadYearId,$semId){
        $gas2 = Yii::$app->db->createCommand(
            "select w_group_assignment.*  from w_group_assignment 
            inner join w_group_setup on w_group_setup.id=w_group_assignment.gas_gset_relId
            inner join student on student.id=w_group_assignment.gas_std_relId
            left join semester_registration_2 on semester_registration_2.semreg_acadYear_relId=w_group_setup.gs_acadYear_relId
                AND semester_registration_2.semreg_semester_relId=w_group_setup.gs_sem_relId
                AND semester_registration_2.semreg_std_relId=w_group_assignment.gas_std_relId
            where w_group_setup.gs_acadYear_relId={$acadYearId} 
                AND w_group_setup.gs_sem_relId={$semId} 
                AND semester_registration_2.id IS NULL
            order by w_group_setup.gs_dept_relId ASC, w_group_setup.gs_prog_relId DESC, w_group_setup.gs_clslvl_relId"
        )->queryAll();
        return $gas2;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'gas_gset_relId' => Yii::t('rms', 'Group'),
            'gas_std_relId' => Yii::t('rms', 'Student Name'),
            'gas_created_at' => Yii::t('rms', 'Created At'),
        ];
    }

    public function getCatalogEntries($studentObject){
        // returns the list of catalog entries setup for the student department in which the student is assigned in so far.

        $courseCatalog = new CourseCatalog();
        $assignments = $this->find()->where(['gas_std_relId' => $studentObject->id])->all();

        $catalogs = [];
        if ($assignments && count($assignments) > 0) {
            foreach ($assignments as $keyGas) {
                $gset = $keyGas->gasGsetRel;
                // get catalog under this gsetup and merge to the set of catalogs.
                $catalogs[$gset->id][] = $courseCatalog->getCatalogCourse3($gset,$studentObject);
            }

            return $catalogs;
        }else{
            return NULL;
        }
    }

    public function getStudentGroupAssignment($acadYearObject,$semesterObject,$studentObject){
        return $this->find()
            ->innerJoin('w_group_setup','w_group_setup.id = w_group_assignment.gas_gset_relId')
            ->where([
                'gs_acadYear_relId' => $acadYearObject->id,
                'gs_sem_relId' => $semesterObject->id,
                'gas_std_relId' => $studentObject->id,
            ])
            ->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGasGsetRel()
    {
        return $this->hasOne(WGroupSetup::class, ['id' => 'gas_gset_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGasStdRel()
    {
        return $this->hasOne(Student::class, ['id' => 'gas_std_relId']);
    }
}
