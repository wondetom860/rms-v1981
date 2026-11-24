<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "semester_registration_controld".
 *
 * @property integer $id
 * @property integer $src_adm_relId
 * @property integer $src_prog_relId
 * @property integer $src_sem_relId
 * @property integer $src_dept_relId
 * @property integer $src_clslvl_relId
 * @property double $src_max_crdthr
 * @property double $src_min_crdthr
 * @property string $src_remark
 * @property integer $src_created_by
 * @property string $src_created_at
 * @property string $src_updated_at
 * @property integer $src_updated_by
 *
 * @property Admission $srcAdmRel
 * @property Program $srcProgRel
 * @property Semester $srcSemRel
 * @property ClassLevel $srcClslvlRel
 * @property StudentSemesterStatusName $srcSssnRel
 * @property User $srcCreatedBy
 * @property User $srcUpdatedBy
 */
class SemesterRegistrationControld extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'semester_registration_controld';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['src_adm_relId', 'src_prog_relId', 'src_max_crdthr', 'src_min_crdthr', 'src_created_by', 'src_created_at', 'src_updated_at'], 'required'],
            [['src_adm_relId', 'src_prog_relId', 'src_sem_relId', 'src_clslvl_relId', 'src_created_by', 'src_updated_by'], 'integer'],
            [['src_max_crdthr', 'src_min_crdthr'], 'number'],
            [['src_remark'], 'string'],
            [['src_created_at', 'src_updated_at'], 'safe'],
            [['src_adm_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['src_adm_relId' => 'id']],
            [['src_prog_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['src_prog_relId' => 'id']],
            [['src_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['src_sem_relId' => 'id']],
            [['src_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['src_clslvl_relId' => 'id']],
            [['src_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['src_created_by' => 'id']],
            [['src_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['src_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'src_adm_relId' => Yii::t('rms', 'Admission'),
            'src_prog_relId' => Yii::t('rms', 'Program'),
            'src_sem_relId' => Yii::t('rms', 'Semester'),
            'src_clslvl_relId' => Yii::t('rms', 'Class Level'),
            'src_max_crdthr' => Yii::t('rms', 'Max Crdthr'),
            'src_min_crdthr' => Yii::t('rms', 'Min Crdthr'),
            'src_remark' => Yii::t('rms', 'Remark'),
            'src_created_by' => Yii::t('rms', 'Src Created By'),
            'src_created_at' => Yii::t('rms', 'Src Created At'),
            'src_updated_at' => Yii::t('rms', 'Src Updated At'),
            'src_updated_by' => Yii::t('rms', 'Src Updated By'),
        ];
    }

    public static function getLoadInterval($gset,$totalCrdt){
        // check if AC exists

        $existsd = \backend\models\SemesterRegistrationControld::find()
            ->where([
                'src_adm_relId'=>$gset->gs_adm_relId,
                'src_prog_relId'=>$gset->gs_prog_relId,
            ])
            ->andWhere(['OR',['src_sem_relId'=>$gset->gs_sem_relId],['src_sem_relId'=>NULL]])
            ->andWhere(['OR',['src_clslvl_relId'=>$gset->gs_clslvl_relId],['src_clslvl_relId'=>NULL]])
            ->one();

            if ($existsd) {
                // check if total credit hour is over maximun
                if ( $totalCrdt > $existsd->src_max_crdthr) {
                    return 'Over Loaded - AC';
                }else if ($totalCrdt < $existsd->src_min_crdthr) {
                    return 'Under Load  - AC';
                }else{
                    return 'Normal Load';
                }
            }else{
                return 'Load Control Not configured yet.';
            }
        // $exists = static::getLoadIntervalAC($gset);

        // if ($exists && count($exists) > 0) {
            
        //     // check if total credit hour is over maximun
        //     if ( $totalCrdt > $exists->src_max_crdthr) {
        //         return 'Over Loaded - AC';
        //     }else if ($totalCrdt < $exists->src_min_crdthr) {
        //         return 'Under Load - AC';
        //     }else{
        //         return 'Normal Load - AC';
        //     }
        // }else{
        //     $existsd = \backend\models\SemesterRegistrationControld::find()
        //     ->where([
        //         'src_adm_relId'=>$gset->gs_adm_relId,
        //         'src_prog_relId'=>$gset->gs_prog_relId,
        //     ])
        //     ->andWhere(['OR',['src_sem_relId'=>$gset->gs_sem_relId],['src_sem_relId'=>NULL]])
        //     ->andWhere(['OR',['src_clslvl_relId'=>$gset->gs_clslvl_relId],['src_clslvl_relId'=>NULL]])
        //     ->one();

        //     if ($existsd && count($existsd) > 0) {
        //         // check if total credit hour is over maximun
        //         if ( $totalCrdt > $existsd->src_max_crdthr) {
        //             return 'Over Loaded';
        //         }else if ($totalCrdt < $existsd->src_min_crdthr) {
        //             return 'Under Load';
        //         }else{
        //             return 'Normal Load';
        //         }
        //     }else{
        //         return 'Load Control Not configured yet.';
        //     }
        // }
    }

    protected static function getLoadIntervalAC($gsetObject){

        $ccc = \backend\models\SemesterRegistrationControlac::find()
            ->where([
                'src_acadYear_relId'=>$gsetObject->gs_acadYear_relId,
                'src_sem_relId'=>$gsetObject->gs_sem_relId,
                'src_adm_relId'=>$gsetObject->gs_adm_relId,
                'src_prog_relId'=>$gsetObject->gs_prog_relId,
                'src_clslvl_relId'=>$gsetObject->gs_clslvl_relId,
            ])->one();
        return $ccc;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrcAdmRel()
    {
        return $this->hasOne(Admission::class, ['id' => 'src_adm_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrcProgRel()
    {
        return $this->hasOne(Program::class, ['id' => 'src_prog_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrcSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'src_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrcClslvlRel()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'src_clslvl_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrcCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'src_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSrcUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'src_updated_by']);
    }
}
