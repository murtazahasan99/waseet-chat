// -----------------------------------------------------------error code
$(window).on("error", function (event) {
  $("#error-text").parent().addClass("error");

  $("#message-err").text(function (_, text) {
    return text + event.type + ": " + event.message + "\n";
  });

  $("#size").addClass("sr--only");
});

//   const badCode = "const s;";
//   eval(badCode);
// -----------------------------------------------------------error code


// Toggle menu
$("#toggle").on("click", function() {
    console.log("hello");
    $("body").toggleClass("show-taps");
});
$(document).on("click", function(event) {
    if ($(event.target).closest("#toggle").length === 0) {
        $("body").removeClass("show-taps");
    }
});


// archives list window
$('#archives').on('click', function() {
    $('body').addClass('show-archives');
});
$('#close-archives-part').on('click', function() {
    $('body').removeClass('show-archives');
});


// personal info window
$('#personal').on('click', function() {
    $('body').addClass('show-personal');
});
$('#close-personal-part').on('click', function() {
    $('body').removeClass('show-personal');
});

// upload icon window
// $('#upload').on('click', function() {
//     $('#uplad-img').toggleClass('show-upload');
// });


// personal all mesages window
$('#message').on('click', function() {
    $('body').toggleClass('show-message');
});
$('#message1').on('click', function() {

    $('#message-type').addClass('show-dedicated');
});
$('#message2').on('click', function() {
    $('#message-type').removeClass('show-dedicated');
});

$('#my-messages').on('click', function() {

    $('#message-type').toggleClass('show-dedicated');
});

$('#close-message').on('click', function() {
    $('body').removeClass('show-message');
});

// message img
const images = $('.the-message-img');
const closimg = $('#close-img-container');
const showimg = $('#imageshow');
const container = $('#show-img-box');

const imgsrc = images.attr('src');

function activeimage() {
    $('body').addClass('show-img');
    $("#imageshow").attr('src', $(this).attr('src'));
}
$(document).on('click', '.the-message-img', activeimage);

$(document).on('click', '#close-img-container', function() {
    $('body').removeClass('show-img');
});

$(document).on('click', '#close-note', function() {
    $('body').addClass('hide-the-note');
});

  // upload icon window
  $(document).on('click',"#upload", function() {
      $('#uplad-img').toggleClass('show-upload');
  });
