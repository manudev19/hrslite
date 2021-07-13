<?php 
$breadcrumb[]['name'] = 'Project Reports';
$breadcrumb[]['link']=null;
include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb)); ?> 
<?php

if ($projectReportPermissions->canRead()) {
    include_component('core', 'ohrmList', $parmetersForListComponent);
}
?>

