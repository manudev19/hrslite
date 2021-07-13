<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
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
 * Boston, MA  02110-1301, USA
 *
 */
?>

<?php $breadcrumb[]['name'] = 'Employee Info';
$breadcrumb[]['link'] = null;
include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb)); ?>

<div class="box">
    <div class="head">
        <h1><?php echo __("Employee Information"); ?></h1>
    </div>
    <div class="inner">
        <form action="" id="employeeSearchForm" name="employeeSearchForm" class="spinner_form" method="post">
            <?php echo $form->renderHiddenFields(); ?>
            <fieldset>
                <ol>
                    <li>
                        <?php echo $form['employeeName']->renderLabel(__('Employee Name/Id') . ' <em>*</em>'); ?>
                        <?php echo $form['employeeName']->render(); ?>
                        <?php echo $form['employeeName']->renderError(); ?>
                    </li>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                <p>
                    <input type="button" class="searchbutton" id="btnView" value="<?php echo __('View') ?>" />
                </p>
            </fieldset>
        </form>
    </div>
</div>

<?php include_partial('core/spinner_common_file'); ?>

<script type="text/javascript">
    var lang_processing = '<?php echo __(CommonMessages::LABEL_PROCESSING);?>';
    var employees = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?>;
    var employeeInfo_EmployeeNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var employeeInfo_ValidEmployee = '<?php echo __(ValidationMessages::INVALID); ?>';
    var search_url = "<?php echo url_for("pim/viewEmployee"); ?>";

    $(document).ready(function() {


        $('#btnView').click(function() {
            $('#employeeSearchForm').submit();

        });


        $("#employeeName_empName").autocomplete(employees, {
            formatItem: function(item) {
                return $('<div/>').text(item.name).html();
            },
            formatResult: function(item) {
                return item.name
            },
            matchContains: true
        }).result(function(event, item) {
            $("#employeeName_empName").valid();
        });



        $("#employeeSearchForm").validate({
            rules: {
                'employeeName[empName]': {

                    required: true,
                    no_default_value: function() {
                        return {
                            defaults: $('#employeeName_empName').data('typeHint')
                        }
                    },
                    validEmployeeName: true,
                    onkeyup: false
                }
            },

            messages: {
                'employeeName[empName]': {

                    required: employeeInfo_EmployeeNameRequired,
                    no_default_value: employeeInfo_EmployeeNameRequired,
                    validEmployeeName: employeeInfo_ValidEmployee
                }
            }

        });

        $.validator.addMethod("validEmployeeName", function(value, element) {

            return autoFill('employeeName_empName', 'employeeName_empId', employees);
        });
    });

    function autoFill(selector, filler, data) {
        $("#" + filler).val("");
        var valid = false;
        $.each(data, function(index, item) {
            if (item.name.toLowerCase() == $("#" + selector).val().toLowerCase()) {
                $("#" + filler).val(item.id);
                valid = true;
            }
        });
        return valid;
    }
</script>