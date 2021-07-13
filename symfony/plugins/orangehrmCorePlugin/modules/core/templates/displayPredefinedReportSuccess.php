
<?php
$breadcrumb[]['name'] = 'Reports';
$breadcrumb[]['link']=null;
include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb)); 
if ($reportPermissions->canRead()) {

    include_component('core', 'ohrmList', $parmetersForListComponent);
}
?>



