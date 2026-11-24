<?php

namespace frontend\modules\api1\resource;

use backend\models\Department as DepartmentModel;


class Department extends DepartmentModel
{
    public function fields()
    {
        return [
            "DEPT_ID" => 'id',
            "DEPT_CODE" => 'dept_code',
            "DEPT_NAME" => 'dept_name',
            "DEPT_LOCATION" => 'dept_location',
            "DEPT_PHONE" => 'dept_phone',
            "DEPT_ID_PREFIX" => 'dept_id_prefix',
            'status' => function ($model) {
                return $model->status == 1 ? 'Active' : 'Inactive';
            },
            'dept_head' => function ($model) {
                $hod = $model->getHod()->andWhere(['dh_status' => 1])->one();
                return $hod ? $hod->dhInstRel->getFullName() : null;
            },
        ];
    }


    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPositionHeadRel()
    // {
    //     return $this->hasOne(Department::class, ['id' => 'position_head_relId']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getLoqPositionDetails()
    // {
    //     return $this->hasMany(Department::class, ['position_head_relId' => 'id']);
    // }
}
