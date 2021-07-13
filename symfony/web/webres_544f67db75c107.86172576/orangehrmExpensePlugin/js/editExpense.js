$(document).ready(function () {
   
    $('#travel_expense').submit(function(){
                    var rowFlag = validateRow();
                    if(!rowFlag){
                        return false;
                    }
                });


    $('#btnAddRow').on('click', function() {
        $("#extraRows").append(addRow(rows-1));
        // alert(rows);
        $('#extraRows table tr').insertBefore('#extraRows');
        $('#newRow').remove();
        rows = rows+1;
    });

        //validation of the amount field
    var validator = $("#travel_expense").validate({

        // rules: {
           
        //     'amount':{
        //         number: true, 
        //         min: 0,
        //         max: 999999999.99
        //     }
            
            
        // },
       
        // messages: {
           
        //     'amount':{
        //         number: validNumberMsg, 
        //         min: lang_negativeAmount,
        //         max: lang_tooLargeAmount
        //     }
            
           
        // }
    });
     var amount = $(".formInputM").text();
        // var expcode = primarykey.split(" ");
     $('#amount').val(amount);

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
/*$(".noAttachment").change(function(){
    if (this.checked){
        alert("Declaring no Attachment");
        //document.getElementsById('attachmentFiless').disabled=true;
        //$('.attachment').attr('disabled',this.checked);
        $(this).attr('disabled',this.checked);

   }
});*/


});
function validateRow() {
        var flag = true;
        displayMessages('reset', '');
        var errorStyle = "background-color:#FFDFDF; width: 225px;";
        var normalStyle = "background-color:#FFFFFF; width: 225px;";
        var expenseTypeElementArray = new Array();
        var index = 0;

        $('.pname').each(function(){
            console.log("pname");
            element = $(this);
            $(element).removeClass('validation-error');
            if($(element).val()==-1){
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_field);  
                flag = false;
            }
            expenseTypeElementArray[index] = $(element);
            index++;
        });

        $('.cname').each(function() {
            console.log("pname");
            element = $(this);
            $(element).removeClass('validation-error');
            if($(element).val()==""){
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_field);  
                flag = false;
            }
        });

        $('.amount').each(function(){
            var regex = /^[0-9]*(\.[0-9]{0,2})?$/;
            element = $(this);
            $(element).removeClass('validation-error');
            if($(element).val()=="" ||$(element).val()== '0' || !$(element).val().match(regex)){
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_amount_field); 
                flag = false;               
            }
            expenseTypeElementArray[index] = $(element);
            index++;          
        });

        $('#tripName').each(function(){
            element = $(this);
            $(element).removeClass('validation-error');
            if($(element).val()==""){
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_field); 
                flag = false;
            }
            expenseTypeElementArray[index] = $(element);
            index++;           
        });

        $('.currency').each(function(){
            element = $(this);
            $(element).removeClass('validation-error');
            if($(element).val()==""){
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_field);  
                flag = false;
            }
            expenseTypeElementArray[index] = $(element);
            index++;
        });
        $('.paid').each(function(){
            element = $(this);
            $(element).removeClass('validation-error');
            if($(element).val()==0){
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_field);  
                flag = false;
            }
            expenseTypeElementArray[index] = $(element);
            index++;
        });

        $('.description').each(function(){
            element = $(this);
            $(element).removeClass('validation-error');
            if($(element).val()==""){
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_field);  
                flag = false;
            }
            expenseTypeElementArray[index] = $(element);
            index++;
        });


        $('.expenseType').each(function(){
            element = $(this);
            $(element).removeClass('validation-error');
            if($(element).val()=="" ){
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_field);  
                flag = false;
            }
            expenseTypeElementArray[index] = $(element);
            index++;
        });

        /*To validate attachment file size*/
        $('.attachment').each(function(){
            element = $(this);
            if($(this).get(0).files.length != 0){
               if($(element).get(0).files[0].size > 5000000)
                {    
                    $(element).addClass('validation-error');
                    displayMessages('warning', please_check_attachment); 
                    flag = false;
                }
                expenseTypeElementArray[index] = $(element);
                index++; 
            }
        });

        if (editting == false) {
        $('.attachment').each(function(){
            element = $(this);
            if($(this).get(0).files.length == 0){
            $(element).removeClass('validation-error');
            var answer = confirm('Are you sure you want to proceed without attachments?');
            if (answer)
                {
                $(element).removeClass('validation-error');
                console.log('yes');
                }
                    else
                {
                console.log('cancel');
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_attachment); 
                flag = false;
                }
            }
        });
    }

   if(editting == true){
        $('.attachment').each(function(){
          element = $(this); 
          if($(this).get(0).defaultValue == '' && $(this).get(0).files.length == 0){
            $(element).removeClass('validation-error');
            var answer = confirm('Are you sure you want to proceed without attachments?');
            if (answer)
            {
                $(element).removeClass('validation-error');
                console.log('yes');
            }
                else
            {
                console.log('cancel');
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_attachment); 
                flag = false;
            }

          }
          //console.log('What the attachment contains',$(this));
          console.log('What the attachment contains!!',$(this).get(0).defaultValue);           
        });
    }

    // if (save == true){
    //     alert(here);
    // }
        
        $('.tdate').each(function(){
            element = $(this);
            var regEx = /^\d{4}\-(0?[1-9]|1[012])\-(0?[1-9]|[12][0-9]|3[01])$/;
            $(element).removeClass('validation-error');
            console.log('Date match', $(element).val().match(regEx));
            if(!$(element).val().match(regEx)||$(element).val()==""){
                $(element).addClass('validation-error');
                displayMessages('warning', please_fill_date_field);  
                flag = false;
            }
            expenseTypeElementArray[index] = $(element);
            index++;
        });
    return flag;
}

        /*for removing the color when filled*/
        //binds to onchange event of your input field
        $('.amount').bind('change',(function() {
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

        $('#tripName').bind('change',(function() {
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
