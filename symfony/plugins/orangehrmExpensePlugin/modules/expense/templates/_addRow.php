<?php
// var_dump("addRow.php");exit;
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2$num$num6 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  $num211$num-13$num1, USA
 */
?>

<?php 
	 // echo javascript_include_tag(plugin_web_path('orangehrmExpensePlugin', 'js/editExpense'));
?>

<table id="newRow">
    <tr>
        <td id =""> <?php echo $form['initialRows'][$num]['toDelete'] -> render();?></td>
        <td> <?php echo $form['initialRows'][$num]['Date'] -> render(); ?></td>
        <td> <?php echo $form['initialRows'][$num]['expense_type']-> render();?></td>
        <td> <?php echo $form['initialRows'][$num]['message']-> render();?></td>
        <td> <?php echo $form['initialRows'][$num]['paid_by_company']-> render();?></td>
        <td class="file"> <?php echo $form['initialRows'][$num]['attachment']-> render();?> <!-- <?php //echo $form['initialRows'][$num]['noAttachment']-> render();?> --></td>
        <td> <?php echo $form['initialRows'][$num]['amount']->render();?></td>
        <td> <?php echo $form['initialRows'][$num]['currency']->render();?></td>

    </tr>  
    
</table>
<script type="text/javascript">
   $(document).ready(function () {
    $("#initialRows_<?php echo $num ?>_noAttachment").change(function(){
        if (this.checked){
            alert("Declaring no Attachment");

        }
    });
   });
</script>