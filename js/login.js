var getHttpRequest = function () {
    var httpRequest = false;
    if (window.XMLHttpRequest) {
        httpRequest = new XMLHttpRequest();
        if (httpRequest.overrideMimeType) {
            httpRequest.overrideMimeType('text/xml');
        }
    } else if (window.ActiveXObject) {
        try {
            httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
            }
        }
    }
    if (!httpRequest) {
        alert('Impossible de créer une instance XMLHTTP');
        return false;
    }
    return httpRequest;
};

var form = $("#form");

form.on('submit', function (e) {
    $("span").text("");
    $("#login").attr("disabled", true);
    $("#login").toggleClass("p-2");
    $("#login").text('');
    $("#login").html('<i class="fas fa-sync fa-spin fa-2x"></i>');

    e.preventDefault();
    var xhr = $.ajax({
        type: 'POST',
        url: form.attr("action"),
        data: $(this).serialize(),
        dataType: 'json',
    });
    xhr.done(function () {
        var response = JSON.parse(xhr.responseText);
        $("#login").attr("disabled", true);
        $(location).attr('href', response.redirect);
    });
    xhr.fail(function () {
        $("#login").attr("disabled", false);
        var error = JSON.parse(xhr.responseText);
        Object.entries(error).forEach(([key, value]) => {
            $("#" + key + "Help").text(value);
        });
    });
    xhr.always(function () {
        
        $("#login").text("S'inscrire");
    });

});
