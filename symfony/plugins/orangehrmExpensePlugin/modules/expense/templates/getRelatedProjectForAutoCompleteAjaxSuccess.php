
<?php $i = 0; ?>
<option value="-1">
    <?php echo '-- ' . __("Select") . ' --'; ?>
</option>
<?php foreach ($projectList as $project): ?>
        <option value="<?php echo $project->getProjectId(); ?>">
    <?php echo $project->getprojectName();
        $i++; ?>
    </option>
<?php endforeach; ?>



 <!-- public function __getProjectList() {

        $list = array();
        $projectList = $this->getTimesheetService()->getProjectNameList();

        foreach ($projectList as $project) {
             $list[''] = __("Select Your Project");
             $list[$project['projectId']] = $project['projectName'];
        }
        return $list;

    }
 -->