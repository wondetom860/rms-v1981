  <?php 
  // use dosamigos\datepicker\DatePicker;

$this->params['courseTitle']= $courseTitle; 
//$this->params['findData']= $findData; 
$this->params['groupName']= $groupName;
$this->params['studentName']= $studentName;
$this->params['InstructorName']= $InstructorName;
$this->params['courseTitle']= $courseTitle;
$this->params['numberOfClasses']= $numberOfClasses;
$this->params['NumberOfabsents']= $NumberOfabsents;
$this->params['NumberOfabsentsInPercent']= $NumberOfabsentsInPercent;
$this->params['NumberOfPresents']= $NumberOfPresents;
$this->params['NumberOfPresentsInPercent']= $NumberOfPresentsInPercent;
$this->params['startDate']= $startDate;
$this->params['endDate']= $endDate;
$this->params['classDates']= $classDates;
$this->params['studentAttendanceStatusValue']= $studentAttendanceStatusValue;
$this->params['count']= $count;

  ?>
  <table class="table table-striped table-bordered">
    <thead>
        <tr>
        <!--<th colspan="3"> <p style="font-weight: bold;"> Attendance Weaky </p></th> -->
        </tr>
        <tr >
          <td colspan="3"><p> From <u><?php echo $startDate;?> </u>To<u> <?php echo $endDate.'  ';?> </u> <?php echo 'Number of Classes :'.$numberOfClasses; ?></p></td>
        </tr>
       <tr>
            <th>Instructor Name</th>
            <th>Course Name</th>
            <th>Group</th>
        </tr>
        <tr>
        	 <td><?= 
             $InstructorName;
             	?>
             </td>
        	<td><?php echo $courseTitle;
        	//} ?></td>
        	<td><?=
             $groupName; 
            ?></td>
        </tr>
    </thead>
   </table> 
     <table class="table table-striped table-bordered">
       <tr>
            <th>SN</th>
            <th>Student Name</th>
            <?php for($i=0;$i<count($classDates);$i++) {?>
            <th><?= $classDates[$i] ?></th>
            <?php } ?>
            <th>Numper of absents</th>
            <th>Absents in % </th>
            <th>Numper of presents</th>
            <th>Presents in % </th> 
        </tr>
        <tr>
        	<td><?php 
        	  	for ($i=1;$i<=$count;$i++) {
        			echo $i."<br>";
        		}
        		?>
        	</td>
        	<td><?php for ($k=1;$k<count($studentName);$k++) {
                  echo $studentName[$k]."<br>";
                   } 
        		?>
        	</td style='text-align: center;'>
        		 <?php 
             for($i=0;$i<count($studentAttendanceStatusValue);$i++){
              echo '<td>';
              for($j=1;$j<count($studentAttendanceStatusValue[$i]);$j++){
                    echo $studentAttendanceStatusValue[$i][$j].'<br>' ;
                     }
                echo '</td>';
        	      	}
                ?>
        	
            <td style='text-align: center;'>
             <?php 
             for($j=0;$j<count($NumberOfabsents);$j++){
               echo $NumberOfabsents[$j].'<br>';
                 }
                ?>
          </td>
            <td style='text-align: center;'>
              <?php 
             for($j=0;$j<count($NumberOfabsentsInPercent);$j++){
               echo round($NumberOfabsentsInPercent[$j],1).'%'.'<br>';
                 }
                ?>
            </td>
           <td style='text-align: center;'> 
              <?php 
             for($j=1;$j<count($NumberOfPresents);$j++){
              echo round($NumberOfPresents[$j],1).'<br>';
                 }
                ?>
          </td>
          <td style='text-align: center;'> 
              <?php 
             for($j=0;$j<count($NumberOfPresentsInPercent);$j++){
              echo round($NumberOfPresentsInPercent[$j],1).'%'.'<br>';
                 }
                ?>
          </td> 
         
        </tr>
      
   </table>