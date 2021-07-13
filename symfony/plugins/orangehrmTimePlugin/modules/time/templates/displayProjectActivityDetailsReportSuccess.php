<?php
   $breadcrumb['name'] = " Project Reports";
   $breadcrumb['link'] = "/symfony/web/index.php/time/displayProjectReportCriteria?reportId=1";
   $breadcrumbnew['name']='Project Activity Details Report';
   $breadcrumbnew['link'] = null; 
   $breadcrumb=array($breadcrumb,$breadcrumbnew);
   include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb));  ?>
<?php

if ($projectReportPermissions->canRead()) {
    include_component('core', 'ohrmList', $parmetersForListComponent);
}
?>

