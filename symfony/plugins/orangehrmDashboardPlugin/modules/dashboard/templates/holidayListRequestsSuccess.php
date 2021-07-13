<?php $recCount = count($holidayList);  ?>
<div id="task-list-group-panel-container" class="" style="height:100%; ">
    <div id="task-list-group-panel-menu_holder" class="task-list-group-panel-menu_holder" style="height:85%; overflow-x: hidden; overflow-y: auto;">
        <table class="table hover">
            <tbody>
                <?php
                    if ($recCount > 0) { 
                        $count = 0; 
                        foreach ($holidayList as $holiday){ ?>
                           
                             <tr class="<?php echo ($count & 1) ? 'even' : 'odd' ?>">
                                <td>
                                <?php echo $holiday ?>
                                </td>
                            </tr>
                        <?php 
                            $count++;
                        }
                    
                    
                    }else { 
                    ?>

                    <tr class="odd"><td><?php echo __(DashboardService::NO_REC_MESSAGE); ?></td></tr>

                <?php } ?>

            </tbody>  
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // hover color change effect
        $("#task-list-group-panel-slider li").hover(function() {
            $(this).animate({opacity: 0.90}, 100, function(){ 
                $(this).animate({opacity: 1}, 0);
            } );
        });     
    });

</script>