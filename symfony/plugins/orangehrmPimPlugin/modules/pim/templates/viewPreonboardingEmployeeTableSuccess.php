<!-- <div><h1>hello world</h1></div> -->
<br>
<?php include_partial('global/flash_messages', array('prefix' => 'PreOnboarding')); ?>
<?php   if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == "Yes") { ?>
<div class="box"><div class="head"><h1><?php echo __('Pre On-boarding Employee List'); ?></h1></div></div>
 <?php include_component('core', 'ohrmList'); ?>
<?php }else{ ?>
    <div class="box"><div class="head"><h1><?php echo __('Pre On-boarding Employee List'); ?></h1></div></div>
    <?php include_component('core', 'ohrmList'); ?>
<?php }?>
   