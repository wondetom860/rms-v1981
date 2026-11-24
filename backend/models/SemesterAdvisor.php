<?php

namespace backend\models;

use Yii;
use common\components\CommonControllerFunctions As CCF;

/**
 * This is the model class for table "semester_advisor".
 *
 * @property integer $id
 * @property integer $sa_acadYear_relId
 * @property integer $sa_sem_relId
 * @property integer $sa_adm_relId
 * @property integer $sa_prog_relId
 * @property integer $sa_dept_relId
 * @property integer $sa_clslvl_relId
 * @property integer $sa_spec_relId
 * @property integer $sa_inst_relId
 * @property integer $sa_created_by
 * @property string $sa_created_at
 * @property integer $sa_updated_by
 * @property string $sa_updated_at
 *
 * @property SchoolYear $saAcadYearRel
 * @property ClassLevel $saClslvlRel
 * @property User $saCreatedBy
 * @property Department $saDeptRel
 * @property Instructor $saInstRel
 * @property Semester $saSemRel
 * @property Specialization $saSpecRel
 * @property User $saUpdatedBy
 */
class SemesterAdvisor extends \yii\db\ActiveRecord
{

    public $acadYearId;
    public $semId;
    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'semester_advisor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sa_acadYear_relId', 'sa_sem_relId', 'sa_dept_relId', 'sa_clslvl_relId', 'sa_inst_relId', 'sa_created_by', 'sa_created_at'], 'required'],
            [['sa_acadYear_relId', 'sa_sem_relId', 'sa_dept_relId', 'sa_clslvl_relId', 'sa_spec_relId', 'sa_inst_relId', 'sa_created_by', 'sa_updated_by'], 'integer'],
            [['sa_created_at', 'sa_updated_at'], 'safe'],
            [['sa_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['sa_acadYear_relId' => 'id']],

            [['sa_adm_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['sa_adm_relId' => 'id']],
            [['sa_prog_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['sa_prog_relId' => 'id']],

            [['sa_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['sa_clslvl_relId' => 'id']],
            [['sa_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['sa_created_by' => 'id']],
            [['sa_dept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['sa_dept_relId' => 'id']],
            [['sa_inst_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Instructor::class, 'targetAttribute' => ['sa_inst_relId' => 'id']],
            [['sa_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['sa_sem_relId' => 'id']],
            [['sa_spec_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Specialization::class, 'targetAttribute' => ['sa_spec_relId' => 'id']],
            [['sa_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['sa_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'sa_acadYear_relId' => Yii::t('rms', 'Sa Acad Year Rel ID'),
            'sa_sem_relId' => Yii::t('rms', 'Sa Sem Rel ID'),
            'sa_dept_relId' => Yii::t('rms', 'Sa Dept Rel ID'),
            'sa_adm_relId' => Yii::t('rms', 'Admission'),
            'sa_prog_relId' => Yii::t('rms', 'Program'),
            'sa_clslvl_relId' => Yii::t('rms', 'Sa Clslvl Rel ID'),
            'sa_spec_relId' => Yii::t('rms', 'Sa Spec Rel ID'),
            'sa_inst_relId' => Yii::t('rms', 'Sa Inst Rel ID'),
            'sa_created_by' => Yii::t('rms', 'Sa Created By'),
            'sa_created_at' => Yii::t('rms', 'Sa Created At'),
            'sa_updated_by' => Yii::t('rms', 'Sa Updated By'),
            'sa_updated_at' => Yii::t('rms', 'Sa Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'sa_acadYear_relId']);
    }

    public function getSemesterAdvisor($gsetObject){
        if ($gsetObject) {
            return $this->find()
                ->where([
                    'sa_acadYear_relId'=>$gsetObject->gs_acadYear_relId,
                    'sa_sem_relId'=>$gsetObject->gs_sem_relId,
                    'sa_dept_relId'=>$gsetObject->gs_dept_relId,
                    'sa_clslvl_relId'=>$gsetObject->gs_clslvl_relId,
                    'sa_adm_relId'=>$gsetObject->gs_adm_relId,
                    'sa_prog_relId'=>$gsetObject->gs_prog_relId,
                ])
                ->andWhere(['OR',['sa_spec_relId'=>$gsetObject->gs_spec_relId],['sa_spec_relId'=>NULL]])
                ->all();
        }
        return $gsetObject;
    }

    public function getHodNotification(){
        if (\Yii::$app->user->can('DepartmentHead')) {

            $this->__constructor();

            // alert only for this semester and academic year.
            $instDept = \backend\models\Instructor::findOne(['inst_user_relId'=>\Yii::$app->user->identity->id])->inst_dep_relId;

            // get gsets for his dept.
            $gsets = WGroupSetup::find()
                ->where(['gs_acadYear_relId'=>$this->acadYearId,'gs_sem_relId'=>$this->semId,])
                ->andWhere(['gs_dept_relId'=>$instDept])
                ->groupBy('gs_dept_relId,gs_adm_relId,gs_clslvl_relId')
                ->all();

            if ($gsets && count($gsets) > 0) {
                foreach ($gsets as $key) {
                    $exists = $this->getSemesterAdvisor($key);
                    if ($exists && count($exists) > 0) {
                        continue;
                    }else{
                        return 1;
                    }
                }
            }
            return 0;
        }
    }

    public function getRegistrarNotification(){
        if (\Yii::$app->user->can('Registrar')) {

            $this->__constructor();
            // $instDept = \backend\models\Instructor::findOne(['inst_user_relId'=>\Yii::$app->user->identity->id])->inst_dep_relId;

            return $this->find()
                ->where([
                    'sa_acadYear_relId'=>$this->acadYearId,
                    'sa_sem_relId'=>$this->semId,
                ])
                ->andWhere([
                    'sa_inst_relId'=>NULL,
                ])
                ->count();
        }
    }

    public function getAdvisingGsetIds($instId){
        $isAdvisor = $this->find()
            ->where([
                'sa_inst_relId'=>$instId,
            ])
            ->all();
        $gsetIds = [];
        if ($isAdvisor && count($isAdvisor) > 0) {
            foreach ($isAdvisor as $key) {
                $gsets = $this->getGsets($key);
                if ($gsets && count($gsets) > 0) {
                    foreach ($gsets as $keyGs) {
                        $gsetIds[] = $keyGs->id;
                    }
                }
            }
        }

        return $gsetIds;
    }

    public function getAdvisingGsets($acadYearId,$semId,$admId,$progId,$instId){
        $isAdvisor = $this->find()
            ->where([
                'sa_acadYear_relId'=>$acadYearId,
                'sa_sem_relId'=>$semId,
            ])
            ->andWhere([
                'sa_inst_relId'=>$instId,
            ])
            ->all();
        $gsetIds = [];
        if ($isAdvisor && count($isAdvisor) > 0) {
            foreach ($isAdvisor as $key) {
                $gsets = $this->getGsets($key);
                if ($gsets && count($gsets) > 0) {
                    foreach ($gsets as $keyGs) {
                        $gsetIds[] = $keyGs->id;
                    }
                }
            }
        }

        return $gsetIds;
    }

    public function getGsets($key){
        $gset = \backend\models\WGroupSetup::find()
            ->where([
                'gs_acadYear_relId'=>$key->sa_acadYear_relId,
                'gs_sem_relId'=>$key->sa_sem_relId,
                'gs_adm_relId'=>$key->sa_adm_relId,
                'gs_prog_relId'=>$key->sa_prog_relId,
                'gs_clslvl_relId'=>$key->sa_clslvl_relId,
                'gs_dept_relId'=>$key->sa_dept_relId,
            ]);
        if (!is_null($key->sa_spec_relId)) {
            $gset = $gset->andWhere(['gs_spec_relId'=>$key->sa_spec_relId]);
        }
        return $gset->all();
    }
    public function isAdvisor($intId){
        $this->__constructor();
        return $this->find()
            ->where([
                'sa_acadYear_relId'=>$this->acadYearId,
                'sa_sem_relId'=>$this->semId,
            ])
            ->andWhere([
                'sa_inst_relId'=>$intId,
            ])
            ->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaClslvlRel()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'sa_clslvl_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'sa_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaDeptRel()
    {
        return $this->hasOne(Department::class, ['id' => 'sa_dept_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaInstRel()
    {
        return $this->hasOne(Instructor::class, ['id' => 'sa_inst_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'sa_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaSpecRel()
    {
        return $this->hasOne(Specialization::class, ['id' => 'sa_spec_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'sa_updated_by']);
    }
}
