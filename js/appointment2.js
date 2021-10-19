let arrAppointment;

$(function () {
    $('#description').inputmask('Regex', {
        regex: "(?:[\\w\\d]+(\\s)*){1,5}",
        clearIncomplete: true
    });

    $("#start_time").inputmask("hh:mm", {
        placeholder: "hh:mm (24h)",
        alias: "datetime",
        clearIncomplete: true,
        oncomplete: function(){
            $("#end_time").focus();
    }});

    $("#end_time").inputmask("hh:mm", {
        placeholder: "hh:mm (24h)",
        alias: "datetime",
        clearIncomplete: true,
        oncomplete: function(){
            compare();
            $("#submit").focus();
    }});

    $(".date-input").inputmask("dd/mm/yyyy", {
        placeholder: "dd/mm/yyyy",
        alias: "datetime",
        clearIncomplete: true
    });
    
});

let today = new Date();
let currentMonth = today.getMonth();
let currentYear = today.getFullYear();

let months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

showCalendar(currentMonth, currentYear);


function showCalendar(month, year) {
    let firstDay = (new Date(year, month)).getDay();
    let daysInMonth = new Date(year, month+1, 0).getDate();

    let tbl = document.getElementById("days");

    tbl.innerHTML = "";

    $("#month").text(months[month]);
    $("#month").data("val", month);
    $("#year").text(year);

    let date = 1;

    for (let i = 0; i < 6; i++) {
        let row = document.createElement("tr");
        row.className = `week week_${i}`;

        for (let j = 0; j < 7; j++) {
            if (i === 0 && j < firstDay) {
                let cell = document.createElement("td");
                let cellText = document.createTextNode("");
                cell.classList.add("inactive");
                cell.classList.add("disabled");
                cell.classList.add("bg-secondary");
                cell.setAttribute('data-day', date);
                cell.appendChild(cellText);
                row.appendChild(cell);
            } else if (date > daysInMonth) {
                break;
            } else {
                let cell = document.createElement("td");
                let cellText = document.createTextNode(date);
                if (date === today.getDate() && year === today.getFullYear() && month === today.getMonth()) {
                    $(cell).addClass("text-white active bg-primary today text-center font-weight-bold");
                    $(cell).attr('data-day', date);
                } else if (date < today.getDate() && year <= today.getFullYear() && month <= today.getMonth()){
                    $(cell).addClass("inactive disabled text-white bg-light text-muted text-center font-weight-light");
                    $(cell).attr('data-day', date);
                    $(cell).attr('disabled', 'disabled');
                } else if (date >= today.getDate() && year >= today.getFullYear() && month >= today.getMonth()) {
                    $(cell).addClass("active text-dark bg-white text-center font-weight-bold");
                    $(cell).attr('data-day', date);
                } else {
                    $(cell).addClass("text-center text-secondary");
                }
                cell.appendChild(cellText);
                row.appendChild(cell);
                date++;
            }
        }
        tbl.appendChild(row);
    }

}

$("#days td.active").on("click", function () {
    $('#date').val($(this).text() + "/" + ($('#month').data('val') + 1) + "/" + $('#year').text());
    if (is_empty() == true) {
        $("#submit").prop('disabled', true);
    } else {
        $("#submit").prop('disabled', false);
    }
    if ($("#description").val() == null || $("#description").val() == '') {
        $("#description").focus();
    } else {
        $("#submit").focus();
    }
});

function compare() {
    var startTime = Date.parse(get_Date($("#start_time").val()));
    var endTime = Date.parse(get_Date( $("#end_time").val()));

    if (startTime > endTime && startTime == endTime) {
        clear_input();
    }
}

function get_Date(time, arrDate = false) {
    if (arrDate == false) {
        var arrDate = GetDateInput();
    }
    var date = new Date(arrDate[2], arrDate[1]-1, arrDate[0], 0, 0, 0, 0);
    var _t = time.split(":");
    date.setHours(_t[0], _t[1], 0, 0);
    return date;
}
function GetDateInput() {
    var date = $("#date").val();
    return date.split("/");
}

function clear_input() {
    $("#date").val('');
    $("#description").val('');
    $("#start_time").val('');
    $("#end_time").val('');
    $("#dept").val(0);
    $("#doctor").val(0);
    $(".list").empty();


}

function delete_appointment(id){
        $.ajax({
            url:"php/doctor.php",
            method: 'POST',
            data:'appointmentId='+id,
            success: function(data){
                 $("#error").html(data);
            },
            complete:function(){
                loadTaple();
              }
            
        });
        clear_input();

};

// on click an appointment
$(document).on('submit','#form_create_appointment', function(e) {
    e.preventDefault();
    $.ajax({
        type: 'POST',
        url:'php/appointment2.php',
        data: new FormData(this),
        contentType:false,
        processData:false,
        success: function(data) {
            $("#error").html(data);
        },
        complete:function(){
            loadTaple();
            
        }
    });
    clear_input();

});

// on load //
$(document).ready(function(){
    $('#appointment_list').ready(function(){
        var userEmail = $("#userEmail").val();
        $.ajax({
            url:"php/doctor.php",
            method: 'POST',
            data:'userEmail='+userEmail,
            success: function(data){
                
                 data = JSON.parse(data);
                 if(data.length != 0){
                    for (let i = 0; i < data.length; i++) {
                        const element = data[i];
                        //  getDoctorName(element.doctor);
                        $("#appointment_list").append(
                            `
                            <tr class='myList'>
                                <td class="text-center align-middle">${element.date_booking}</td>
                                <td class="text-center align-middle">${element.message}</td>+
                                <td  class="text-center align-middle">${element.first_name + ' ' + element.last_name}</td>+
                                <td class="text-center align-middle">${element.sTime.substr(0,5)}</td>
                                <td class="text-center align-middle">
                                  <button class="btn btn-danger btn-sm " title="Delete" onclick="delete_appointment(${element.id})"><i class="fa fa-trash"></i></button>
                              </td>
                            </tr>
                            `
                        );
                    }
                 }
            }
        });
        
    })
    var doctorEmail = $("#doctorEmail").val();
        $.ajax({
          method: 'POST',
          url : 'php/doctor.php',
          data:'doctorEmail='+doctorEmail,
        }).done(function(data){
          data = JSON.parse(data);
          $('.list').empty();
           if (data.length !== 0) {
            for (let i = 0; i < data.length; i++) {
                const element = data[i];
                $("#appointment_list_doctor").append(
                  `
                    <tr class="list">
                        <td class="text-center align-middle">${element.date_booking}</td>
                        <td class="text-center align-middle">${element.sTime.substr(0,5)}</td>
                    </tr>
                    `
                ); 
            }
          }
        });
    });



    
    function loadTaple(){
        var userEmail = $("#userEmail").val();
        $.ajax({
            url:"php/doctor.php",
            method: 'POST',
            data:'userEmail='+userEmail,
            success: function(data){
                $('.myList').empty();
                 data = JSON.parse(data);
                 if(data.length != 0){
                    for (let i = 0; i < data.length; i++) {
                        const element = data[i];
                        $("#appointment_list").append(
                            `
                            <tr class='myList'>
                                <td class="text-center align-middle">${element.date_booking}</td>
                                <td class="text-center align-middle">${element.message}</td>+
                                <td  class="text-center align-middle">${element.first_name + ' ' + element.last_name}</td>+
                                <td class="text-center align-middle">${element.sTime.substr(0,5)}</td>
                                <td class="text-center align-middle">
                                  <button class="btn btn-danger btn-sm " title="Delete" onclick="delete_appointment(${element.id})"><i class="fa fa-trash"></i></button>
                              </td>
                            </tr>
                            `
                        );
                        
                    }
                 }
            }
        })
    var doctorEmail = $("#doctorEmail").val();
        $.ajax({
          method: 'POST',
          url : 'php/doctor.php',
          data:'doctorEmail='+doctorEmail,
        }).done(function(data){
          data = JSON.parse(data);
          $('.list').empty();
           if (data.length !== 0) {
            for (let i = 0; i < data.length; i++) {
                const element = data[i];
                $("#appointment_list_doctor").append(
                  `
                    <tr class="list">
                        <td class="text-center align-middle">${element.date_booking}</td>
                        <td class="text-center align-middle">${element.sTime.substr(0,5)}</td>
                    </tr>
                    `
                );
            }

          }
        });
        clear_input();

    }