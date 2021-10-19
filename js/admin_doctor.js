$(document).on('submit', '#deleteUser', function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '../php/deleteDoctor.php',
                data: new FormData(this),
                contentType: false,
                processData: false,
                success: function(data) {
                }
            })
        });


        function deleteDoctor(idDoctor) {
            var modal = document.getElementById(`deleteModal`);
            var delIcon = document.getElementById(`doctor-${idDoctor}`);

            delIcon.onclick = function() {
                id = idDoctor;
                modal.style.display = "block";
            }

            var closeAlert = document.getElementById(`closeAlert`);
            var deleteUser = document.getElementById(`delDoctor`);

            closeAlert.onclick = function() {
                modal.style.display = "none";
            }

            deleteUser.onclick = function() {
                $(`#idDoctor`).val(id);
                modal.style.display = "none";
                $(`#row-${id}`).hide();
            }
        }

        $(document).ready(function() {
            $("#myInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#table tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });

        function clickImg(id) {
            // Get the modal

            // Get the image and insert it inside the modal - use its "alt" text as a caption 
            var img = document.getElementById(`img-${id}`);

            var modalImg = document.getElementById("img");
            var modal = document.getElementById(`myModal1`);
            var span = document.getElementsByClassName("close")[0];

            img.onclick = function() {
                modal.style.display = "block";
                modalImg.src = this.src;
            }

            span.onclick = function() {
                modal.style.display = "none";
            }
        }