<?php $timesheetservice = new TimesheetService();
                       $dept=$timesheetservice->getdepartmentonemployeeid($empName['empId']); ?>
<dl class="search-params">
    <dt><?php echo " ".__("Employee Name/Id")."  ";?></dt>
    
    <dd><?php echo $empName['empName'];?></dd>
   
</dl>
<dl class="search-params">
<dt><?php echo " ".__("Department")."  ";?></dt>
    
    <dd><?php echo $dept;?></dd>
   
</dl>


