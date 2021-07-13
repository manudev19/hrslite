$(document).ready(function(){
    
    $("#month").val(month);
    $("#year").val(year);
    if(employeeId != '') {
        $('#employeeRecordsForm').append($('.actionbar > .formbuttons').html());
        $('.actionbar > .formbuttons').html('');
        $('.actionbar > .formbuttons').html($('#formbuttons').html());
        $('#formbuttons').html('');
    }
    
    if(trigger){
        autoFillEmpName(employeeId);
        $("#reportForm").submit();    
    }

    $('#btView').click(function() {
            month=$("#month").val();
            year=$("#year").val();
            getRelatedAttendanceRecords(employeeId,month,year,actionRecorder); 
            $("#reportForm").submit();
    });

    $('#btnDownload').click(function() {
        $('#hdnAction').val('download');
        $('#reportForm').submit();
        $('#hdnAction').val('');
    });
    
    $("#attendance_employeeName_empName").change(function(){
        autoFill('attendance_employeeName_empName', 'attendance_employeeName_empId', employees_attendance_employeeName);
    });

    function autoFill(selector, filler, data) {
        $("#" + filler).val("");
        $.each(data, function(index, item){
            if(item.name.toLowerCase() == $("#" + selector).val().toLowerCase()) {
                $("#" + filler).val(item.id);
                return true;
            }
        });
    }
        
    function autoFillEmpName(employeeId) {
        $("#attendance_employeeName_empId").val("");
        $.each(employees_attendance_employeeName, function(index, item){
            if(item.id == employeeId) {
                $("#attendance_employeeName_empId").val(item.id);
                $("#attendance_employeeName_empName").val(item.name);
                return true;
            }
        });
    }
}); //ready

$.validator.addMethod("validEmployeeName", function(value, element) {      
    return autoFill('attendance_employeeName_empName', 'attendance_employeeName_empId', employees_attendance_employeeName);
});
function autoFill(selector, filler, data) {
    $("#" + filler).val("");
    var valid = false;
    if($("#" + selector).val() == typeForHints || $("#" + selector).val() == '') {
        valid = true;
    } else {
        $.each(data, function(index, item){
            if(item.name.toLowerCase() == $("#" + selector).val().toLowerCase()) {
                $("#" + filler).val(item.id);
                valid = true;
            }
        });
    }
    return valid;
}