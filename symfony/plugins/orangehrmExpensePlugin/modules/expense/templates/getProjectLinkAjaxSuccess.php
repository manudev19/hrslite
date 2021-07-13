<?php $i = 0; ?>
<option value="-1">
    <?php echo '-- ' . __("Select") . ' --'; ?>
</option>
<?php foreach ($activityList as $i=> $activity): ?>
        <option value="<?php echo $activity['project_id']; ?>">
    <?php echo $activity['name'];
        $i++; ?>
    </option>
<?php endforeach; ?>
