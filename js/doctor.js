function HideShow(flage) {
  $(`#collapse-${flage}`).toggle();
}

function movementType(id, move) {
  $(`#movementType-${id}`).val(move);
  console.log($(`#movementType-${id}`).val());
  console.log(id);
}

$(document).on('submit', '#rateComment', function (e) {
  e.preventDefault();
  $.ajax({
    type: 'POST',
    url: 'php/rateComment.php',
    data: new FormData(this),
    contentType: false,
    processData: false,
    success: function (data) {
      data = JSON.parse(data);
      console.log(data[0]);
      $(`#movement-${data[0]}`).empty();
      if (data[1] == "up") {
        $(`#movement-${data[0]}`).append(`
                  <button style='color:blue' onclick='movementType( ${data[0]}  , "up")' class='fa fa-chevron-up rate'></button>
                    ${data[2]}
                  <button onclick='movementType( ${data[0]}  , "down")' class='fa fa-chevron-down rate'></button>
                    ${data[3]}
                `);
      } else {
        $(`#movement-${data[0]}`).append(`
                  <button  onclick='movementType( ${data[0]}  , "up")' class='fa fa-chevron-up rate'></button>
                  ${data[2]}
                  <button style='color:blue' onclick='movementType( ${data[0]}  , "down")' class='fa fa-chevron-down rate'></button>
                  ${data[3]}
                `);
      }
    }
  })
});

function clickImg(id, dept) {
  // Get the modal

  // Get the image and insert it inside the modal - use its "alt" text as a caption 
  var img = document.getElementById(`myImg-${id}`);
  if (dept != 'mycomment') {
    var modalImg = document.getElementById("img01");
    var modal = document.getElementById(`myModal`);
    var span = document.getElementsByClassName("close")[0];

  } else {
    var modalImg = document.getElementById("img0");
    var modal = document.getElementById(`myModalComment`);
    var span = document.getElementById("close");

  }
  var captionText = document.getElementById("caption");
  img.onclick = function () {
    modal.style.display = "block";
    modalImg.src = this.src;
    captionText.innerHTML = this.alt;
  }

  // Get the <span> element that closes the modal

  // When the user clicks on <span> (x), close the modal
  span.onclick = function () {
    modal.style.display = "none";
  }
}

function areYouSureComment(idComment, ij) {

  var modal = document.getElementById(`deleteModalComment`);

  var comment = document.getElementById(`comment-${ij}`);
  comment.onclick = function () {
    modal.style.display = "block";
  }

  var closeAlert = document.getElementById('closeAlertComment');
  var deleteComment = document.getElementById('delete_comment');

  closeAlert.onclick = function () {
    modal.style.display = "none";
  }

  deleteComment.onclick = function () {
    $('#idComment').val(idComment);
  }
}
$(document).on('submit', '#deleteComment', function (e) {
  e.preventDefault();
  $.ajax({
    type: 'POST',
    url: 'php/deleteComment.php',
    data: new FormData(this),
    contentType: false,
    processData: false,
    success: function (data) {
      location.reload();
    }
  })
});


function complaint(idPost, id) {

  var modal = document.getElementById(`complaint`);
  var closeAlert = document.getElementById('closeAlertCompaint');
  var sendCompalin = document.getElementById('sendComplaint');

  var complaint = document.getElementById(`complaint-${id}`);

  complaint.onclick = function () {
    modal.style.display = "block";
  }

  closeAlert.onclick = function () {
    modal.style.display = "none";
  }

  sendCompalin.onclick = function () {
    $('#idPostForComlaint').val(idPost);
  }
}

$(document).on('submit', '#complaintForm', function (e) {
  e.preventDefault();
  $.ajax({
    type: 'POST',
    url: 'php/complaint.php',
    data: new FormData(this),
    contentType: false,
    processData: false,
    success: function (data) {
      $('#sendComplaint').hide();
      $('#inputComplaint').hide();
      $('#sendComplaint1').hide();
      $('#inputComplaint1').hide();
      setTimeout(function () {
        location.reload()
      }, 3000);
      if (data == 'done' || data == 'تم') {
        $('#alertDone').show();
        $('#alertDone1').show();
      } else {
        $('#alertError').show();
        $('#alertError1').show();
      }
    }
  })
});

function modalLogin() {
  var close = document.getElementById("closeModal");
  var modal = document.getElementById(`commentModal`);
  modal.style.display = "block";
  close.onclick = function () {
    modal.style.display = "none";
  }
}

function modalRate() {
  var close = document.getElementById("closeModal1");
  var modal = document.getElementById(`commentModalSameUser`);
  modal.style.display = "block";
  close.onclick = function () {
    modal.style.display = "none";
  }
}

function modalAdmin() {
  var close = document.getElementById("closeModal2");
  var modal = document.getElementById(`commentModalAdmin`);
  modal.style.display = "block";
  close.onclick = function () {
    modal.style.display = "none";
  }
}

function edit(ij, comment, content, i, commentId) {
  console.log("Saeb");
  if ($(`#${comment}`).val() == null || $(`#${comment}`).val() == '') {
    $(`#comment${ij}`).hide();
    $(`#hide${ij}`).hide();
    $(`#${comment}`).val(content);
    $(`#commentUpdate-${i}`).val(commentId);
  }
}

function foo() {
  if (typeof foo.counter == 'undefined') {
    foo.counter = 0;
  }
  foo.counter++;
  return foo.counter;
}
$(document).on('submit', '#share_comment', function (e) {
  var email = $('#userEmail').val();
  e.preventDefault();
  $.ajax({
    type: 'POST',
    url: 'php/share_comment.php',
    data: new FormData(this),
    contentType: false,
    processData: false,
    success: function (data) {
      if (data != 'saeb') {
        data = JSON.parse(data);
        console.log(data);
        flage = data[0].flage;
        //   j = data[0].j;
        ij = data[0].ij;
        ijUpdate = ij + foo() + 1;
        if (data.length != 0) {
          for (i = 0; i < data.length; i++) {
            if (data[i].verify_license == '1') {
              var icon = '<i class="fa fa-check-circle"></i>';
            } else {
              var icon = ' ';
            }
            if (data[i].email == email) {
              deleteIcon = `
                              <i style='cursor:pointer' id="comment-${ijUpdate}" onclick="areYouSureComment( ${data[i].id}  , ${ijUpdate} )" class="fa fa-trash"></i>`;
              if (data[i].his_comment == 'doctor') {
                editIcon = `
                                <i onclick="edit('${ij}' , 'content_comment-${flage}' , '${data[i].content}' , '${flage}' ,'${data[i].id}' )" class="fas fa-edit"></i>
                              `;
              } else {
                editIcon = '';
              }
            } else {
              deleteIcon = '';
            }
            iRateTo = `
                        <a class="fa fa-chevron-up" onclick="modalRate()"></a>
                        0
                        <a class="fa fa-chevron-down" onclick="modalRate()"></a>
                        0
                            `;
            $(`#comm-${flage}`).append(
              ` 
                              <div id="hide${ij}">
                          <div class="bg-white p-2">
                            <div class="d-flex flex-row user-info">
                              <img class="rounded-circle" src="${data[i].img}" width="40">
                                <div class="d-flex flex-column justify-content-start ml-2">
                                  <a style='text-decoration: none' href="${data[i].his_comment}Profile.php?email=${data[i].email}">
                                    <span class="d-block font-weight-bold name">${data[i].first_name + ' ' + data[i].last_name + ' ' + icon}</span>
                                  </a>                            
                                    <span class="date text-black-50">${data[i].date_share.substring(0, 16)}</span>
                                  </div>
                                  <form method="POST" id='rateComment'>
                                    <div id='movement-${data[i].id}'>
                                      ` + iRateTo + `
                                    </div>
                                    <input type='hidden' name='commentId' value='${data[i].id}' >
                                    <input id='movementType-${data[i].id}' type='hidden' name='moveMent' value='s'>
                                  </form>
                              </div>   
                              <div style='float:right'>       
                              ` + deleteIcon
              + editIcon + `
                              </div>
                            <div class="mt-2">
                              <p class="comment-text">${data[i].content}</p>
                            </div>
                          </div>
                          </div>
                        `
            );
          }
        }
        $(`#content_comment-${flage}`).val("");
      }
    }
  })
});
