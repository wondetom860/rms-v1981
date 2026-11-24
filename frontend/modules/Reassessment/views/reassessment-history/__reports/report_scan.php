<?php

if (isset($model)) {
    $commitees = $model->reassessmentCommittees;
    
} else {
    echo "<p class='bg-danger'>Invalid request</p>";
}
