$(document).ready(function () {

                // $("#extraRows").hide();
                // var rows=1;
                // alert(rows);

                $('#travel_expense').submit(function(){
                    var rowFlag = validateRow();
                    alert(rowFlag+' received');
                    if(!rowFlag){
                        return false;
                    }
                });
    /* function validateComment()
    {
        var flag = true;
        displayMessages('reset', '');
         
        var errorStyle = "background-color:#FFDFDF; width: 225px;";
        var normalStyle = "background-color:#FFFFFF; width: 225px;";

        $('.largeTextBox').each(function(){
            
            element = $(this);
            $(element).removeClass('validation-error');
            if($(element).val()==""){
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_comment);  
                flag = false;
            }
        });

    }

    $('#expenseActionFrm').submit(function(){
        // alert("here"); return 0;
        var commentFlag = validateComment();
        if(!commentFlag){
            return false;
        }
    });
    */

    $('#btnAddRow').on('click', function() {
        $("#extraRows").append(addRow(rows-1));
        // alert(rows);
        $('#extraRows table tr').insertBefore('#extraRows');
        $('#newRow').remove();
        rows = rows+1;
    });

    $("#submitRemoveRows").click(function(){
        if(!isRowsSelected()){
            _showMessage('warning', lang_noRecords);
            // alert('1');
        }
        else if(isDeleteAllRows()){
            // alert('2');
            $(".toDelete").each(function(){
                element = $(this)
                if($( element).is(':checked')){
                    var array=$(element).parent().attr('id').split("_");
                    // var array = "";
                    // alert('3');
                    var projectId=array[0];
                    var activityId=array[1];
                    var timesheetId=array[2];
                    var employeeId=array[3];
                    var token = $('#defaultList__csrf_token').val();
                    var r = $.ajax({
                        type: 'POST',
                        url: linkToDeleteRow,
                        data: "timesheetId="+timesheetId+"&activityId="+activityId+"&projectId="+projectId+"&employeeId="+employeeId+"&t="+token,
                        async: false,
                        success: function(state){
                            // alert(state);
                            status=state;
                        }
                    });
                }
            });
            if(status){
                // alert('4');
                _showMessage('success', lang_removeSuccess);
                $('form#timesheetForm').submit();
            }
            else{
                // alert('5');
                _showMessage('warning', lang_noChagesToDelete);
            }
        }
        else{
            $(".toDelete").each(function(){
                element = $(this)
                if($( element).is(':checked')){
                  console.log(element);
                    // console.log($(element).parent().attr('id',"td")); 
                    var array=$(element).parent().attr('id').split("_");
                    // alert('6');
                    // var array="";
                    if((array!="") && ($(".toDelete").size()==1)){
                        // alert('7');
                        var expenseItemId=array;
                        // var token = $('#defaultList__csrf_token').val();
                        // console.log(array);
                        var r = $.ajax({
                            type: 'POST',
                            url: linkToDeleteRow,
                            data: "expenseItemId="+expenseItemId,
                            async: false,
                            success: function(state){
                            }
                        });
                        _showMessage('success', lang_removeSuccess);
                        $(element).parent().parent().remove();
                    }
                    else if((array=="") && ($(".toDelete").size()==1)){
                         // alert('8');
                         _showMessage('warning', lang_noChagesToDelete);
                     }
                     else if((array=="") && ($(".toDelete").size()!=1)){
                        // alert('9');
                        $(".messageBalloon_warning").remove();
                        _showMessage('success', lang_removeSuccess);
                        $(element).parent().parent().remove();
                    }
                    else if((array!="") && ($(".toDelete").size()!=1)){
                        // alert('10');
                        var expenseItemId=array;
                        // var token = $('#defaultList__csrf_token').val();
                        // console.log(array);
                        var r = $.ajax({
                            type: 'POST',
                            url: linkToDeleteRow,
                            data: "expenseItemId="+expenseItemId,
                            async: false,
                            success: function(state){
                            }
                        });
                        _showMessage('success', lang_removeSuccess);
                        $(element).parent().parent().remove();
                    }
                }
            });
        }
    });

$('#customerName').change(function() {
        // console.log('here');
        var value = $(this).val();
        // alert (value);
        var r = $.ajax({
            type: 'POST',
            url: getProjectLink ,
            data: "clientId="+ value,
            success : function(msg){
                        // console.log(msg);
                        $('#projectName').html(msg);

                    }
                });
        return r;
    });
$(".noAttachment").change(function(){
    if (this.checked){
        alert("Declaring no Attachment");
        document.getElementsByClassName('attachment').disabled=true;
   }
});


});//END OF $document.ready
function validateRow() {

        // return 0;
        var flag = true;
        displayMessages('reset', '');
        // alert(editting);
        var errorStyle = "background-color:#FFDFDF; width: 225px;";
        var normalStyle = "background-color:#FFFFFF; width: 225px;";

        var expenseTypeElementArray = new Array();

        var index = 0;

        $('.pname').each(function(){

            element = $(this);
            $(element).removeClass('validation-error');
            if($(element).val()==-1){
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_field);  
                flag = false;
            }
        });

        $('.cname').each(function(){

            element = $(this);
            $(element).removeClass('validation-error');
            if($(element).val()==""){
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_field);  
                flag = false;
            }
        });

        $('.amount').each(function(){
             alert('in amount');
            element = $(this);
            $(element).removeClass('validation-error');
            if($(element).val()==""){
                alert('in smount inside if');
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_amount); 
                flag = false;
                alert(flag+' in amount');
            }
            else if($(element).val()){
                if(!(/^[0-9]+\.?[0-9]?[0-9]?$/).test($(element).val())) {
                    $(element).addClass('validation-error');
                    displayMessages('warning', validNumberMsg);
                    flag = false;
                }
            }
            
        });

         $('.currency').each(function(){

            element = $(this);
            $(element).removeClass('validation-error');
            if($(element).val()==""){
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_field);  
                flag = false;
                alert(flag+' in currency');
            }
        });
         // $('.noAttachment').each(function(){
         //    element = $(this);
         //    $(element).removeClass('validation-error');
         //    if(!$(element).checked){
         //        $(element).addClass('validation-error');
         //        displayMessages('warning', please_fill_field);  
         //        flag = false;
         //    }
         // });
         /**
         * Validation for Attachment.
         */
        /* if (editting == false) {
            $(".noAttachment").each(function(){
            if(!this.checked ){
                    
                    $('.attachment').each(function(){
                        element = $(this);
                        $(element).removeClass('validation-error');
                        if($(element).val()==""){
                            $(element).addClass('validation-error');
                            displayMessages('warning', please_fill_field);  
                            flag = false;
                        }
                      });
                }
            });
        }
        */

        $('.paid').each(function(){

            element = $(this);
            $(element).removeClass('validation-error');
            if($(element).val()==0){
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_field);  
                flag = false;
                alert(flag+' in paid');
            }
        });

        $('.description').each(function(){

            element = $(this);
            $(element).removeClass('validation-error');
            if($(element).val()==""){
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_field);  
                flag = false;
                alert(flag+' in description');
            }
            else if($(element).val()){
                // (?=.{1,50}$)[a-z]+(?:['_.\s][a-z]+)
                // /^[0-9!@#\$%\^\&*\)\(+=._- ]$/g
                if(!(/^[!@#\$%\^\&*\)\(+=._- ]$/g).test($(element).val())) { 
                    $(element).addClass('validation-error');
                    displayMessages('warning', validDescriptionMsg);
                    flag = false;
                }
            }
        });

        $('.expenseType').each(function(){

            element = $(this);
            $(element).removeClass('validation-error');
            if($(element).val()==0){
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_field);  
                flag = false;
                alert(flag+' in expenseType');
            }
        });

        $('.tdate').each(function(){

            element = $(this);
            $(element).removeClass('validation-error');
            if($(element).val()=="yyyy-mm-dd"||$(element).val()==""){
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_field);  
                flag = false;
                alert(flag+' in tdate');
            }
        });
        /*commented from here*/

        /*$('.amount').bind('change',(function() {
            var flag = validateRow();
            if(!flag) {
                $('#btnSave').attr('disabled', 'disabled');
                $('#btnSave').attr('background', 'grey')
            }
            else{
                $('#btnSave').removeAttr('disabled');
            }

        }));
        $('.currency').bind('change',(function() {
            var flag = validateRow();
            if(!flag) {
                $('#btnSave').attr('disabled', 'disabled');
                $('#btnSave').attr('background', 'grey')
            }
            else{
                $('#btnSave').removeAttr('disabled');
            }

        }));
        $('.noAttachment').bind('change',(function() {
            var flag = validateRow();
            if(!flag) {
                $('#btnSave').attr('disabled', 'disabled');
                $('#btnSave').attr('background', 'grey')
            }
            else{
                $('#btnSave').removeAttr('disabled');
            }

        }));
        $('.attachment').bind('change',(function() {
            var flag = validateRow();
            if(!flag) {
                $('#btnSave').attr('disabled', 'disabled');
                $('#btnSave').attr('background', 'grey')
            }
            else{
                $('#btnSave').removeAttr('disabled');
            }

        }));
        $('.tdate').bind('change',(function() {
            var flag = validateRow();
            if(!flag) {
                $('#btnSave').attr('disabled', 'disabled');
                $('#btnSave').attr('background', 'grey')
            }
            else{
                $('#btnSave').removeAttr('disabled');
            }

        }));
        $('.cname').bind('change',(function() {
            var flag = validateRow();
            if(!flag) {
                $('#btnSave').attr('disabled', 'disabled');
                $('#btnSave').attr('background', 'grey')
            }
            else{
                $('#btnSave').removeAttr('disabled');
            }

        }));
        $('.pname').bind('change',(function() {
            var flag = validateRow();
            if(!flag) {
                $('#btnSave').attr('disabled', 'disabled');
                $('#btnSave').attr('background', 'grey')
            }
            else{
                $('#btnSave').removeAttr('disabled');
            }

        }));
        $('.description').bind('change',(function() {
            var flag = validateRow();
            if(!flag) {
                $('#btnSave').attr('disabled', 'disabled');
                $('#btnSave').attr('background', 'grey')
            }
            else{
                $('#btnSave').removeAttr('disabled');
            }

        }));
        $('.paid').bind('change',(function() {
            var flag = validateRow();
            if(!flag) {
                $('#btnSave').attr('disabled', 'disabled');
                $('#btnSave').attr('background', 'grey')
            }
            else{
                $('#btnSave').removeAttr('disabled');
            }

        }));
        $('.expenseType').bind('change',(function() {
            var flag = validateRow();
            if(!flag) {
                $('#btnSave').attr('disabled', 'disabled');
                $('#btnSave').attr('background', 'grey')
            }
            else{
                $('#btnSave').removeAttr('disabled');
            }

        }));
*/


        expenseTypeElementArray[index] = $(element);
        index++;
        alert(flag+' return');
        return flag;
         
    }

    function addRow(num) {
        var r = $.ajax({
            type: 'GET',
            url: link ,
            data: "num="+num,
            async: false
        }).responseText;
        return r;
    }

    function isRowsSelected(){
    // alert('11');
    var count=0;
    var errFlag=false;
    //alert($(".toDelete").size());
    $(".toDelete").each(function(){
        // alert('12');
        element = $(this)
        if($( element).is(':checked')){
            count=count+1;
        }
    });
    if(count==0){
        errFlag=true;
    }
    return !errFlag;
}

function isDeleteAllRows(){
    // alert('13');
    var count=0;
    $(".toDelete").each(function(){
        // alert('14');
        element = $(this)
        if($( element).is(':checked')){
            count=count+1;
        }
    });
    if($(".toDelete").size()==count){
        return true;
    }
    else{
        return false;
    }
}


function displayMessages(messageType, message) {
    // alert('inside display');
    $('#msgDiv').remove();
    if (messageType != 'reset') {
        // alert('inside displAY IF');
        $divClass = 'message '+messageType;
        $msgDivContent = "<div id='msgDiv' class=' " + $divClass + "' >" + message + 
        "<a class='messageCloseButton' href='#'>"+closeText+"</a>" + "</div>";
        $('#validationMsg').append($msgDivContent);
    }
   // $('#msgDiv').fadeOut($msgDelayTime, function(){
   //    $('#msgDiv').remove();
   // });
}


function _showMessage(messageType, message) {  
    _clearMessage();
    $('#validationMsg').append('<div class="message ' + messageType + '" id="divMessageBar" generated="true">'+ message + 
        "<a class='messageCloseButton' href='#'>"+closeText+"</a>" +  '</div>');
}

function _clearMessage() {
    $('#validationMsg div[generated="true"]').remove();
}

function _showMessage(messageType, message) {  
    _clearMessage();
    $('#validationMsg').append('<div class="message ' + messageType + '" id="divMessageBar" generated="true">'+ message + 
        "<a class='messageCloseButton' href='#'>"+closeText+"</a>" +  '</div>');
}

function _clearMessage() {
    $('#validationMsg div[generated="true"]').remove();
}