<?php

namespace frontend\modules\ClassSchedule\models;

use Yii;
use \backend\models\User;
use \backend\models\CourseOffering2Detail;
use \backend\models\WGroupSetup;

/**
 * This is the model class for table "cs_class_schedule_detail".
 *
 * @property int $id
 * @property int $gset_relId
 * @property int $class_time_distrib_relId
 * @property int $coff2det_relId
 * @property int $room_relId
 * @property string $class_type => ['lecture','Tutorial','Laboratory']
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property CsClassTimeDistribution $classTimeDistribRel
 * @property CourseOffering2Detail $coff2detRel
 * @property User $createdBy
 * @property WGroupSetup $gsetRel
 * @property User $updatedBy
 * @property CsAvailableClassRoom $roomRel
 */
class CsClassScheduleDetail extends \yii\db\ActiveRecord
{

    public $coff2detObj = null;
    public $coff2detTableName = null;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cs_class_schedule_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gset_relId', 'class_time_distrib_relId', 'coff2det_relId', 'room_relId', 'created_at', 'created_by', 'data'], 'required'],
            [['gset_relId', 'class_time_distrib_relId', 'coff2det_relId', 'room_relId', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['data','class_type'], 'string'],
            [['class_time_distrib_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CsClassTimeDistribution::class, 'targetAttribute' => ['class_time_distrib_relId' => 'id']],
            [['coff2det_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseOffering2Detail::class, 'targetAttribute' => ['coff2det_relId' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['gset_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupSetup::class, 'targetAttribute' => ['gset_relId' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['room_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CsAvailableClassRoom::class, 'targetAttribute' => ['room_relId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'gset_relId' => Yii::t('rms', 'Group'),
            'class_time_distrib_relId' => Yii::t('rms', 'Class Time Distribution'),
            'coff2det_relId' => Yii::t('rms', 'Course Offering'),
            'room_relId' => Yii::t('rms', 'Class Room'),
            'class_type' => Yii::t('rms', 'Class type'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData(){
        $mainData = (object)[
            'class_type'=>$this->class_type,
            'room_relId'=>intval($this->room_relId),
            'coff2det_relId'=>intval($this->coff2det_relId),
            'class_time_distrib_relId'=>intval($this->class_time_distrib_relId),
            'gset_relId'=>intval($this->gset_relId),
            'created_by'=>intval($this->created_by),
            'updated_by'=>intval($this->updated_by),
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered(){
        return $this->getData() == $this->data;
    }

    public function scheduleReport($coffdetModels,$gsetModels){
        if (isset($coffdetModels) && count($coffdetModels) > 0) {
            $coff2detIds = [];
            foreach ($coffdetModels as $keyCoff2det) {
                $coff2detIds[] = $keyCoff2det->id;
            }

            return $this->find()->where(['coff2det_relId' => $coff2detIds])->all();
        }

        if (isset($gsetModels) && count($gsetModels) > 0) {
            $gsetIds = [];
            foreach ($gsetModels as $gset) {
                $gsetIds[] = $gset->id;
            }
            return $this->find()->where(['gset_relId' => $gsetIds])->all();
        }
    }

    // getScheduleReport($coffdetModel->id,$keyDist->id,$type)
    public function getScheduleReport($coffdetModel,$keyDist,$type,$gsetModel=null){
        if (!($coffdetModel) && !$gsetModel) {
            return null;
        }
        // $coffdetModel = CourseOffering2Detail::findOne($coffdetId);
        if ($type == 1) {//report per group
            $gsetModel = $gsetModel ? $gsetModel : $coffdetModel->coff2detCoff2Rel->coff2GroupRel;
            return $this->find()->where([
                'gset_relId' => $gsetModel->id,
                'class_time_distrib_relId' => $keyDist->id,
                // 'coff2det_relId' => $coffdetModel->id,
            ])->all();
        }elseif ($type == 2) {//report per instructor
            return $this->find()    
                ->innerJoin('course_offering2_detail','course_offering2_detail.id = '.self::tableName().'.coff2det_relId')
                ->where([
                    'coff2det_inst_relId' => $coffdetModel->coff2det_inst_relId,
                    'class_time_distrib_relId' => $keyDist->id,
                    'coff2det_relId' => $coffdetModel->id,
                ])
                ->all();
        }
    }

    public function getSchedule($coffdetId,$distId,$roomId){//checking for class,instructor,group clash
        $coffdetObj = $this->coff2detObj ? $this->coff2detObj : $this->__getCoff2detOb($coffdetId);

        // We don't support schuduling for uknown Course_Instrutor
        if (is_null($coffdetObj->coff2det_inst_relId)) {
            return "Course Instructor is NOT known yet. Please assign course instructor for {$coffdetObj->coff2detCourseRel->course_title} before scheduling.";
        }
        // returns integer or a null depaending on existane and non-existsance of entry
        /*[
            0=> class room clash
            1=> instructor class
            2=> group class
            null=>free, no clash exsists
        ]*/

        // var_dump($this->class_time_distrib_relId);die();

        $coffTableName = $this->__getCofftableNmae();

        return $this->find()
            ->innerJoin($coffTableName,$coffTableName.'.id='.self::tableName().'.coff2det_relId')
            ->innerJoin('instructor','instructor.id='.$coffTableName.'.coff2det_inst_relId')
            ->innerJoin('w_group_setup','w_group_setup.id='.self::tableName().'.gset_relId')
            ->where([
                    'OR',
                    [ 'coff2det_inst_relId' => $coffdetObj->coff2det_inst_relId ],
                    [ 'gset_relId' => $coffdetObj->coff2detCoff2Rel->coff2_group_relId ],
                    [ 'room_relId' => $roomId ],
            ])
            ->andWhere([
                'class_time_distrib_relId' => $distId,
                'gs_acadYear_relId' => $coffdetObj->coff2detCoff2Rel->coff2_acadYear_relId,
                'gs_sem_relId' => $coffdetObj->coff2detCoff2Rel->coff2_sem_relId,
            ])
            ->all();
    }

    public function getCoff2detObj($coffdetId){
        return $this->coff2detObj ? $this->coff2detObj : $this->__getCoff2detOb($coffdetId);
    }

    public function getSkeleton(){
        return CsClassTimeDistribution::find()->all();
    }

    public function getAcademicWorkDay(){
        return CsAcademicWorkDay::find()->all();
    }

    private function __getCoff2detOb($coffdetId){//not repeateldy access db at each call of {$this} object.
        if (is_null($this->coff2detObj)) {
            $this->coff2detObj = CourseOffering2Detail::findOne($coffdetId);
        }
        return $this->coff2detObj;
    }

    private function __getCofftableNmae(){
        if (is_null($this->coff2detTableName)) {
            $this->coff2detTableName = CourseOffering2Detail::tableName();
        }
        return $this->coff2detTableName;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClassTimeDistribRel()
    {
        return $this->hasOne(CsClassTimeDistribution::className(), ['id' => 'class_time_distrib_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2detRel()
    {
        return $this->hasOne(CourseOffering2Detail::className(), ['id' => 'coff2det_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGsetRel()
    {
        return $this->hasOne(WGroupSetup::className(), ['id' => 'gset_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomRel()
    {
        return $this->hasOne(CsAvailableClassRoom::className(), ['id' => 'room_relId']);
    }
}
