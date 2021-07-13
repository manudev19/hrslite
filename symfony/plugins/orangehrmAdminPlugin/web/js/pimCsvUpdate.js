$(document).ready(function() {
    
    $("#frmPimCsvUpdate").validate({
        rules: {
            'pimCsvUpdate[csvFile]' : {
                required:true
            }

        },
        messages: {
            'pimCsvUpdate[csvFile]' : {
                required:lang_csvRequired
            }

        }
    });
   
    $('#btnSave').click(function() {
        
        if ($('#frmPimCsvUpdate').valid()) {
            $('#btnSave').attr('disabled', 'disabled');
            $("#btnSave").val(lang_processing);
        }

        $('#frmPimCsvUpdate').submit();
    });  
});