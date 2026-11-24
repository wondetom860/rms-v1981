<?php

namespace backend\models;

use frontend\modules\AcStEva\models\AseEvaluationControlSetting;
use Yii;

/**
 * This is the model class for table "faculty_course_assignment".
 *
 * @property int $id
 * @property int $coff2det_relId
 * @property string $assign_as
 * @property int $inst_relId
 * @property int|null $remark
 * @property int $category
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int $data
 *
 * @property CourseOffering2Detail $coff2detRel
 * @property User $createdBy
 * @property Instructor $instRel
 * @property User $updatedBy
 * @property AseEvaluationControlSetting[] $controlSettings
 */
class FacultyCourseAssignment extends \yii\db\ActiveRecord
{

    public $course; //course code
    public $academicYear;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'faculty_course_assignment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['coff2det_relId', 'assign_as', 'inst_relId', 'created_at', 'created_by', 'data'], 'required'],
            [['coff2det_relId', 'inst_relId', 'created_at', 'created_by', 'updated_at', 'updated_by', 'academicYear', 'category'], 'integer'],
            [['assign_as', 'data', 'course', 'remark'], 'string'],
            [['coff2det_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseOffering2Detail::class, 'targetAttribute' => ['coff2det_relId' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['inst_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Instructor::class, 'targetAttribute' => ['inst_relId' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'coff2det_relId' => Yii::t('rms', 'Course Offering'),
            'assign_as' => Yii::t('rms', 'Assigned as'),
            'inst_relId' => Yii::t('rms', 'Faculty Name'),
            'academicYear' => Yii::t('rms', 'Academic Year'),
            'course' => Yii::t('rms', 'Course Code'),
            'remark' => Yii::t('rms', 'Remark'),
            'category' => Yii::t('rms', 'Modifiable'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Update At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
        ];
    }

    public function evaluationPrintable()
    {
        if (count($this->controlSettings) == 0) {
            return '';
        }
        foreach ($this->controlSettings as $controlSetting) {
            if ($controlSetting->deptPlanRel->headerRel->force_above_half_evaluation == 1) {
                $ff = $controlSetting->aboveHalfEvaluated();
                if (!$ff[0]) {
                    return "<span class='label bg-warning' style='font-size: 8pt; color: red; cursor: pointer; margin: 0px 3px;' title='Above 50% evaluation is requiered, which is NOT satisfied by ({$controlSetting->deptPlanRel->criteriaBundleRel->code})'>{$ff[1]}</span>";
                }
            }
        }

        return true;
    }

    public function getAssignedsId()
    {
        // $assigneds = $this->coff2detRel->facultyAssignments;
        $ids = [];
        foreach ($this->coff2detRel->facultyAssignments as $faculty) {
            $ids[] = $faculty->inst_relId;
        }
        return $ids;
    }

    public function getData()
    {
        $this->data = md5(serialize((object)[
            'assign_as' => $this->assign_as,
            'category' => $this->category,
            'coff2det_relId' => intval($this->coff2det_relId),
            'inst_relId' => intval($this->inst_relId),
            'created_by' => intval($this->created_by),
            'updated_by' => intval($this->updated_by),
        ]));
    }

    public function exists($id = null)
    {
        return $id ?
            self::find()->where([
                'coff2det_relId' => $this->coff2det_relId,
                'assign_as' => $this->assign_as,
                'inst_relId' => $this->inst_relId,
            ])->andWhere(" id <> $id ")->count() > 0
            :
            self::find()->where([
                'coff2det_relId' => $this->coff2det_relId,
                'assign_as' => $this->assign_as,
                'inst_relId' => $this->inst_relId,
            ])->count() > 0;
    }

    /**
     * Gets query for [[Coff2detRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2detRel()
    {
        return $this->hasOne(CourseOffering2Detail::class, ['id' => 'coff2det_relId']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[InstRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInstRel()
    {
        return $this->hasOne(Instructor::class, ['id' => 'inst_relId']);
    }


    /**
     * Gets query for [[controlSettings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getControlSettings()
    {
        return $this->hasMany(AseEvaluationControlSetting::class, ['faculty_assignment_relId' => 'id']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
