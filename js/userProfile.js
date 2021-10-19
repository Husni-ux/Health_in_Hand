$('#mycomment').hide();

function HideShow(flage) {
  $(`#collapse-${flage}`).toggle();
}

function clickToShow(dept) {
  $('#mycomment').hide();
  $('#mypost').hide();
  $(`#${dept}`).show();
}

function movementType(id, move) {
  $(`#movementType-${id}`).val(move);
  $(`#movementType1-${id}`).val(move);
}

$(document).on('submit', '#rateComment', function (e) {
  e.preventDefault();
  console.log('saeb');
  $.ajax({
    type: 'POST',
    url: 'php/rateComment.php',
    data: new FormData(this),
    contentType: false,
    processData: false,
    success: function (data) {
      data = JSON.parse(data);
      console.log(data);
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

$(document).on('submit', '#deletePost', function (e) {
  e.preventDefault();
  $.ajax({
    type: 'POST',
    url: 'php/deletePost.php',
    data: new FormData(this),
    contentType: false,
    processData: false,
    success: function (data) {
      location.reload();
    }
  })
});

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
      setTimeout(function () { location.reload() }, 2000);
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
function clickImg(id, dept) {
  // Get the modal

  // Get the image and insert it inside the modal - use its "alt" text as a caption 
  var img = document.getElementById(`myImg-${id}`);
  if (dept != 'mycomment') {
    var modalImg = document.getElementById("img01");
    var modal = document.getElementById(`myModal`);
    var span = document.getElementsByClassName("close")[0];
  }
  else {
    var modalImg = document.getElementById("img0");
    var modal = document.getElementById(`myModalComment`);
    var span = document.getElementById("close");
  }

  img.onclick = function () {
    modal.style.display = "block";
    modalImg.src = this.src;
  }

  span.onclick = function () {
    modal.style.display = "none";
  }
}

function areYouSure(id, dept) {

  var modal = document.getElementById(`deleteModal`);
  var post = document.getElementById(`post-${id}`);

  post.onclick = function () {
    modal.style.display = "block";
  }

  // Get the <span> element that closes the modal
  var span = document.getElementsByClassName("close")[1];
  var closeAlert = document.getElementById('closeAlert');
  var deletePost = document.getElementById('deletePost');

  // When the user clicks on <span> (x), close the modal
  span.onclick = function () {
    modal.style.display = "none";
  }

  closeAlert.onclick = function () {
    modal.style.display = "none";
  }

  deletePost.onclick = function () {
    $('#idPost').val(id);
  }
}


var flage;

function areYouSureComment(idComment, dept, ij) {
  if (dept == 'post') {
    var modal = document.getElementById(`deleteModalComment`);
    var closeAlert = document.getElementById('closeAlertComment');
    var deleteComment = document.getElementById('delete_comment');
  } else {
    var modal = document.getElementById(`deleteModalCommentDeptComment`);
    var closeAlert = document.getElementById('closeAlertComment1');
    var deleteComment = document.getElementById('delete_comment1');
  }

  var comment = document.getElementById(`comment-${ij}`);

  comment.onclick = function () {
    modal.style.display = "block";
  }

  closeAlert.onclick = function () {
    modal.style.display = "none";
  }

  deleteComment.onclick = function () {
    if (dept == 'post')
      $('#idComment').val(idComment);
    else {
      $('#idComment1').val(idComment);
    }
  }
}

function complaint(idPost, dept, id) {

  if (dept == 'post') {
    var modal = document.getElementById(`complaint`);
    var closeAlert = document.getElementById('closeAlertCompaint');
    var sendCompalin = document.getElementById('sendComplaint');
  } else {
    var modal = document.getElementById(`complaintDeptComment`);
    var closeAlert = document.getElementById('closeAlertCompaint1');
    var sendCompalin = document.getElementById('sendComplaint1');
  }
  var complaint = document.getElementById(`complaint-${id}`);
  complaint.onclick = function () {
    modal.style.display = "block";
  }

  closeAlert.onclick = function () {
    modal.style.display = "none";
  }

  sendCompalin.onclick = function () {
    if (dept == 'post') {
      $('#idPostForComlaint').val(idPost);
    } else {
      $('#idPostForComlaint1').val(idPost);
    }
  }
}

function modalLogin() {
  var close = document.getElementById("closeModal");
  var modal = document.getElementById(`commentModal`);
  modal.style.display = "block";
  close.onclick = function () {
    modal.style.display = "none";
  }
}


$(document).on('submit', '#rateComment1', function (e) {
  e.preventDefault();
  console.log('saeb');
  $.ajax({
    type: 'POST',
    url: 'php/rateComment.php',
    data: new FormData(this),
    contentType: false,
    processData: false,
    success: function (data) {
      data = JSON.parse(data);
      console.log(data);
      $(`#movement1-${data[0]}`).empty();
      if (data[1] == "up") {
        $(`#movement1-${data[0]}`).append(`
              <button style='color:blue' onclick='movementType( ${data[0]}  , "up")' class='fa fa-chevron-up'></button>
                ${data[2]}
              <button onclick='movementType( ${data[0]}  , "down")' class='fa fa-chevron-down'></button>
                ${data[3]}
            `);
      } else {
        $(`#movement1-${data[0]}`).append(`
              <button  onclick='movementType( ${data[0]}  , "up")' class='fa fa-chevron-up rate'></button>
              ${data[2]}
              <button style='color:blue' onclick='movementType( ${data[0]}  , "down")' class='fa fa-chevron-down rate'></button>
              ${data[3]}
            `);
      }
    }
  })
});

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
  if ($(`#${comment}`).val() == null || $(`#${comment}`).val() == '') {
    $(`#comment${ij}`).hide();
    $(`#hide${ij}`).hide();
    $(`#${comment}`).val(content);
    $(`#commentUpdate-${i}`).val(commentId);
    console.log($(`#commentUpdate-${i}`).val());
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
        dept = data[0].dept;
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
                                <i style='cursor:pointer' id="comment-${ijUpdate}" onclick="areYouSureComment( ${data[i].id} , '${dept}' , ${ijUpdate} )" class="fa fa-trash"></i>`;
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
                          ` + deleteIcon +
              editIcon + `
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
