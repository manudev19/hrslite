<?php 
$breadcrumb[]['name'] = 'Employee Reports';
$breadcrumb[]['link']=null;
include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb)); ?> 
<?php

if ($employeeReportsPermissions->canRead()) {
    include_component('core', 'ohrmList', $parmetersForListComponent);
}
?>

