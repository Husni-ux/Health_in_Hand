function hideRow(id) {
    $(`#row-${id}`).hide();
}


function openImg(id) {
    // Get the modal

    // Get the image and insert it inside the modal - use its "alt" text as a caption 
    var img = document.getElementById(`myImg-${id}`);
    var modalImg = document.getElementById("img01");
    var modal = document.getElementById(`myModal`);
    var span = document.getElementsByClassName("close")[0];

    img.onclick = function () {
        modal.style.display = "block";
        modalImg.src = this.src;
    }

    // Get the <span> element that closes the modal

    // When the user clicks on <span> (x), close the modal
    span.onclick = function () {
        modal.style.display = "none";
    }
}
$(document).on('submit', '#giveVerify', function (e) {
    e.preventDefault();
    $.ajax({
        type: 'POST',
        url: '../php/addListVerify.php',
        data: new FormData(this),
        contentType: false,
        processData: false,
        success: function (data) {
        }
    })
});
$(document).on('submit', '#removeVerify', function (e) {
    e.preventDefault();
    $.ajax({
        type: 'POST',
        url: '../php/removeListVerify.php',
        data: new FormData(this),
        contentType: false,
        processData: false,
        success: function (data) {
        }
    })
});
$(document).ready(function () {
    $("#myInput").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $("#table tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});