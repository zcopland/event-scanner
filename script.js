/* variables */
var idTextfield = document.getElementById('studentID');
var studentID;
var operation;
var idFormat = $('#idFormat').val();
var school = $('#school').val();
var email = $('#email').val();
var org = $('#org').val();
var RED = '#FF0000';
var GREEN = '#00FF00';
var NORMAL = '#CAEBF2';
var paused = false;
var eventStart = false;
var counter = 0;

$('#resumeEvent').hide();
$('#stopEvent').hide();
$('#pauseEvent').hide();
$('#count-h4').hide();
$('#codes-list').hide();

$('#users-list').hide();
//toggling the employee list
$('#showUsers').click(function() {
    $('#users-list').toggle(1000);
});

/* this will process form w/o reloading page */
function process() {
    studentID = $('#studentID').val();
    operation = $('#operation').val();
    $('#bannedList').hide();
    $('#status').hide();
    if (checkString(studentID)) {
        switch (operation) {
            case 'Check':
                if (checkLength()) {
                    /* use AJAX every time someone checks an ID */
                    $.ajax({type: "POST", url: "process-search.php", data: {
                        studentID: studentID,
                        operation: operation,
                        school: school,
                        org: org
                    }, success: function(result){
                        if (result) {
                            //banned
                            changeBGColor(RED);
                            $('#status').empty()
                            $('#status').append('Student is BANNED.');
                            $('#status').show();
                        } else {
                            //not banned
                            changeBGColor(GREEN);
                            $('#status').empty()
                            $('#status').append('Student is allowed.');
                            $('#status').show();
                            if (paused == false && eventStart == true) {
                                var title = $('#eventTitle').val();
                                $.ajax({type: "POST", url: "event-db/updateCounter.php", data: {
                                    title: title,
                                    school: school,
                                    org: org
                                }, success: function(result){
                                    if ($.isNumeric(result)) {
                                        //update counter
                                        counter = result;
                                        $('#counter').html(counter);
                                    } else {
                                        $('#modal-text').html(result);
                                        $("#myModal").modal();
                                    }
                                }});
                            }
                        }
                    }});
                }
                break;
            case 'Ban':
                if (checkLength()) {
                    /* use AJAX every time someone checks an ID */
                    $.ajax({type: "POST", url: "process-search.php", data: {
                        studentID: studentID,
                        operation: operation,
                        school: school,
                        org: org
                    }, success: function(result){
                        if (result == true) {
                            //student has been banned
                            changeBGColor(NORMAL);
                            $('#status').empty()
                            $('#status').append('Student has been banned.');
                            $('#status').show();
                        } else {
                            //student is already banned
                            changeBGColor(NORMAL);
                            $('#status').empty()
                            $('#status').append('Student is already banned.');
                            $('#status').show();
                        }
                    }});
                }
                break;
            case 'UnBan':
                if (checkLength()) {
                    /* use AJAX every time someone checks an ID */
                    $.ajax({type: "POST", url: "process-search.php", data: {
                        studentID: studentID,
                        operation: operation,
                        school: school,
                        org: org
                    }, success: function(result){
                        if (result == true) {
                            //student was removed from list
                            changeBGColor(NORMAL);
                            $('#status').empty()
                            $('#status').append('Student has been removed from list.');
                            $('#status').show();
                        } else {
                            //student was not banned
                            changeBGColor(NORMAL);
                            $('#status').empty()
                            $('#status').append('Student was not banned.');
                            $('#status').show();
                        }
                    }});
                }
                break;
            case 'Banned Students':
                /* use AJAX every time someone checks an ID */
                    $.ajax({type: "POST", url: "process-search.php", data: {
                        studentID: studentID,
                        operation: operation,
                        school: school,
                        org: org
                    }, success: function(result){
                        changeBGColor(NORMAL);
                        $('#modal-text').html(result);
                        $("#myModal").modal();
                    }});
                break;
            default:
                break;
        }
    }
}

/* check the length of ID */
function checkLength() {
    studentID = $('#studentID').val();
    len = studentID.length;
    if (len == idFormat) {
        return true;
    } else {
        changeBGColor(NORMAL);
        $('#modal-text').html('Check length of ID!');
        $("#myModal").modal();
        return false;
    }
}

/* change background color */
function changeBGColor(color) {
    document.body.style.background = color;
}

/* setting the textfield to be selected */
idTextfield.select();

/* create event button */
$('#createEvent').click(function() {
    var title = $('#eventTitle').val();
    if (title.length >= 5 && checkString(title) == true) {
        $.ajax({type: "POST", url: "event-db/createEvent.php", data: {
            title: title,
            school: school,
            org: org
        }, success: function(result){
            if (result == true) {
                $('#count-h4').show();
                $('#createEvent').hide();
                $('#stopEvent').show();
                $('#pauseEvent').show();
                $('#listEvents').hide();
                $('#eventTitle').prop('disabled', true);
                $('#counter').html(counter);
                eventStart = true;
            } else {
                $('#modal-text').html(result);
                $("#myModal").modal();
                $('#eventTitle').prop('disabled', false); 
            }
        }});
    } else if (title.length < 5) {
        $('#modal-text').html('Please double check event title!');
        $("#myModal").modal();
    }
});
/* Pause event button */
$('#pauseEvent').click(function() {
    $('#resumeEvent').show();
    $(this).hide();
    paused = true;
});
/* Resume event button */
$('#resumeEvent').click(function() {
    $('#pauseEvent').show();
    $(this).hide();
    paused = false;
});
/* Stop event button */
$('#stopEvent').click(function() {
    var title = $('#eventTitle').val();
    $.ajax({type: "POST", url: "event-db/stopEvent.php", data: {
        title: title,
        school: school,
        org: org
    }, success: function(result){
        if (result == true) {
            $('#count-h4').hide();
            $('#createEvent').show();
            $('#stopEvent').hide();
            $('#eventTitle').prop('disabled', false);
            $('#counter').html('');
            $('#eventTitle').val('');
            $('#pauseEvent').hide();
            $('#listEvents').show();
            eventStart = false;
        } else {
            $('#modal-text').html(result);
            $("#myModal").modal();
            $('#eventTitle').prop('disabled', true); 
        }
    }});
});
$('#listEvents').click(function() {
    var title = $('#eventTitle').val();
    $.ajax({type: "POST", url: "event-db/listEvents.php", data: {
        school: school,
        org: org
    }, success: function(result){
        if (result != false) {
            $('#modal-text').html(result);
            loadChart();
            $("#myModal").modal();
        } else {
            $('#modal-text').html('There are no events to list!');
            $("#myModal").modal();
        }
    }});
});

function checkString(str) {
    var test = /^[a-zA-Z0-9-_\/ ]*$/;
    var result = false;
    if (test.test(str) == true) {
        result = true;
    }
    if (result) {
        return true;
    } else {
        $('#modal-text').html('Please remove special characters from title!');
        $("#myModal").modal();
        return false;
    }
}

$('#showCodes').click(function() {
    $('#codes-list').toggle(1000);
});

/* Preventing the form from submitting when
    enter key is pressed                    */
$(document).keypress(function(event) {
    if (event.which == '13') {
        event.preventDefault();
    }
});

/* detecting which option is selected */
function getValue(sel) {
    idTextfield.select();
    if (sel.value == 'Banned Students') {
        $('#studentID').val('');
        $('#studentID').prop('disabled', true); 
    }
    else { $('#studentID').prop('disabled', false); }
}

function chartTable(result) {
    if (result == 'chart') {
        $('#chartDiv').show(500);
        $('#events-list').hide(500);
        $('#chart-btn').prop('disabled', true);
        $('#table-btn').prop('disabled', false);
    } else if (result == 'table') {
        $('#chartDiv').hide(500);
        $('#events-list').show(500);
        $('#chart-btn').prop('disabled', false);
        $('#table-btn').prop('disabled', true);
    }
}