let holder = $("#avatar-holder");
function readURL(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            holder
                .attr('src', e.target.result)
                .width(120)
                .height(120);
        };
        reader.readAsDataURL(input.files[0]);
    }
}
// Clicking on existing profile picture to upload and change avatar.
holder.on('click', function () {
    $('[id*=imageFile]').click().on('change', function(event) {
        let inputImage = event.currentTarget;
        readURL(inputImage);
    });
});