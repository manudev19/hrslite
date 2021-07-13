<?php 
$breadcrumb[]['name'] = 'Attendance Summary';
$breadcrumb[]['link']=null;
include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb)); ?>
<?php

if ($attendancePermissions->canRead()) {
    include_component('core', 'ohrmList', $parmetersForListComponent);
}
?>