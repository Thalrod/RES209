
const addEventform = $("#addEventform");
const editEventform = $("#editEventform");
const deleteEventform = $("#deleteEventform");
const addGroupform = $("#addGroupform");
const editGroupform = $("#editGroupform");
const deleteGroupform = $("#deleteGroupform");
const addAgendaform = $("#addAgendaform");
const deleteAgendaform = $("#deleteAgendaform");

var eventsArray = [];
var groupsArray = [];
var agendasArray = [];
var usersArray = [];
var members = [];
//la date par défaut est celle du jour actuel (au format DD/MM/YYYY)
var date = getDate();

var last_agenda;

getLast_Agenda();
reloadGroups();
reloadAgendas();
reloadEvents();






$(document).click(function (event) {
    //on test si le click est sur un élément ayant la classe "cell" ou si c'est un de ses enfants*
    if ($(event.target).hasClass("cell") || $(event.target).parents(".cell").length > 0) {
        //on recupère la cellule ou son parent si c'est un enfant
        var cell = $(event.target).hasClass("cell") ? $(event.target) : $(event.target).parents(".cell");

        //on enlève la classe "hover" de toutes les cellules
        //on enlève l'attribut "href" de toutes les ancres des cellules
        $(".cell").removeClass("hover");
        $(".cell").children("a").removeAttr("href");
        //on ajoute la classe "hover" à la cellule sélectionnée
        //on ajoute l'attribut "href" à l'ancre de la cellule sélectionnée
        cell.addClass("hover");
        cell.children("a").attr("href", "#event");

        //on récupère la date de la cellule
        date = getDate();

        //on affiche les events de la date sélectionnée
        showEvents(date);
    }

    else if ($(event.target).attr("id") == "addEvent" || $(event.target).parents("#addEvent").length > 0) {
        $.ajax({
            type: 'POST',
            url: "./api/agenda/checkPermission",
            data: "",
            dataType: 'json',
            async: false
        })
            .done(function () {
                    var panel = $("#addEventPanel");
                    $("#wizard").css("display", "flex");
                    $(".panel").css("display", "none");
                    $("body").css("overflow", "hidden");
                    panel.css("display", "flex");



                    var splitted = date.split("-");
                    $("#addEventTitle").attr("placeholder", "Titre");
                    $("#addEventDescription").attr("placeholder", "Ajouter une description");
                    $("#addEventColor").attr("value", "#89d49a");
                    $("#addEventDate").text(splitted.reverse().join("/"));
            });


    }
    else if ($(event.target).hasClass("deleteEvent") || $(event.target).parents(".deleteEvent").length > 0) {
        let eventID = $(event.target).parents(".event").attr("data-id");
        $.ajax({
            type: 'POST',
            url: "./api/event/checkPermission",
            data: "id=" + eventID,
            dataType: 'json',
            async: false
        })
            .done(function () {
                let panel = $("#deleteEventPanel");
                $("#wizard").css("display", "flex");
                $(".panel").css("display", "none");
                $("body").css("overflow", "hidden");
                panel.css("display", "flex");

                let eventInfo = eventsArray.find(event => event.id == eventID);

                let start = eventInfo.startts.split(" ")[1].split(":");
                start = start[0] + "h " + start[1];
                let end = eventInfo.endts.split(" ")[1].split(":");
                end = end[0] + "h " + end[1];

                $("#deleteEventTitle").text(eventInfo.title);
                $("#deleteEventDate").text(eventInfo.startts.split(" ")[0]);
                $("#deleteEventStart").text(start);
                $("#deleteEventEnd").text(end);
                $("#deleteEventId").val(eventInfo.id);
            })
            .fail(function () {
                btn = $(event.target).hasClass("deleteIcon") ? $(event.target) : $(event.target).parents(".deleteIcon");
                playKeyframe(btn, "shake");
            });








    }
    else if ($(event.target).hasClass("editEvent") || $(event.target).parents(".editEvent").length > 0) {

        let eventID = $(event.target).parents(".event").attr("data-id");

        $.ajax({
            type: 'POST',
            url: "./api/event/checkPermission",
            data: "id=" + eventID,
            dataType: 'json',
            async: false
        })
            .done(function () {
                event = eventsArray.find(event => event.id == eventID);
                let panel = $("#editEventPanel");
                $("#wizard").css("display", "flex");
                $(".panel").css("display", "none");
                $("body").css("overflow", "hidden");
                panel.css("display", "flex");


                let splitted = date.split("-");


                $("#editEventTitle").attr("placeholder", "Titre");
                $("#editEventDescription").attr("placeholder", "Ajouter une description");

                $("#editEventId").val(event.id);
                $("#editEventTitle").attr("value", event.title);
                $("#editEventDescription").text(event.description);
                $("#editEventStartts").val(event.startts.split(" ")[1].slice(0, -3));
                $("#editEventEndts").val(event.endts.split(" ")[1].slice(0, -3));
                $("#editEventColor").val(event.color);
                $("#editEventDate").text(splitted.reverse().join("/"));


            })
            .fail(function () {
                btn = $(event.target).hasClass("editIcon") ? $(event.target) : $(event.target).parents(".editIcon");
                playKeyframe(btn, "shake");

            });

    }
    else if ($(event.target).attr("id") == "addAgendaBtn" || $(event.target).parents("#addAgendaBtn").length > 0) {
        let panel = $("#addAgendaPanel");
        $("#wizard").css("display", "flex");
        $(".panel").css("display", "none");
        $("body").css("overflow", "hidden");
        panel.css("display", "flex");
        $("#addAgendaGroup option").remove();

        $("#addAgendaName").attr("placeholder", "Nom");
        let accountID = $("#accountID").val();
        groupsArray.forEach(group => {
            $("#addAgendaGroup").append(`<option value="${group.id}">${group.name}</option>`);
        });
    }
    else if ($(event.target).hasClass("deleteAgenda") || $(event.target).parents(".deleteAgenda").length > 0) {
        let agendaID = $(event.target).parents(".object").attr("data-id");
        $.ajax({
            type: 'POST',
            url: "./api/agenda/checkPermission",
            data: "id=" + agendaID,
            dataType: 'json',
            async: false
        })
            .done(function () {
                let panel = $("#deleteAgendaPanel");
                $("#wizard").css("display", "flex");
                $(".panel").css("display", "none");
                $("body").css("overflow", "hidden");
                panel.css("display", "flex");

                agenda = agendasArray.find(agenda => agenda.id == agendaID);

                $("#deleteAgendaName").text(agenda.name);
                $("#deleteAgendaId").val(agenda.id);

            })
            .fail(function () {
                btn = $(event.target).hasClass("deleteIcon") ? $(event.target) : $(event.target).parents(".deleteIcon");
                playKeyframe(btn, "shake");
            });
    }
    else if ($(event.target).parents("#select-agenda > ul").length > 0) {
        let agendaID = $(event.target).parents("#select-agenda > ul > .object").attr("data-id");
        if (agendaID != last_agenda && agendaID !== undefined) {
            $.ajax({
                type: 'POST',
                url: "./api/agenda/load",
                data: "id=" + agendaID,
                dataType: 'json',
                async: false
            })
                .done(function () {
                    getLast_Agenda();
                    reloadGroups();
                    reloadAgendas();
                    reloadEvents();
                });


        }
    }

    else if ($(event.target).attr("id") == "addGroupBtn" || $(event.target).parents("#addGroupBtn").length > 0) {
        let panel = $("#addGroupPanel");
        $("#wizard").css("display", "flex");
        $(".panel").css("display", "none");
        $("body").css("overflow", "hidden");
        panel.css("display", "flex");

        $("#addGroupName").attr("placeholder", "Nom");

    }
    else if ($(event.target).hasClass("viewGroup") || $(event.target).parents(".viewGroup").length > 0) {
        let panel = $("#viewGroupPanel");
        $("#wizard").css("display", "flex");
        $(".panel").css("display", "none");
        $("body").css("overflow", "hidden");
        $("#viewGroupMembers li").remove();
        panel.css("display", "flex");


        groupID = $(event.target).parents(".object").attr("data-id");
        group = groupsArray.find(group => group.id == groupID);
      
        $("#viewGroupName").text(group.name);
        group.members.forEach(member => {
            member = JSON.parse(member);
            crown = group.owner_id == member.id ? "👑" : "";
            $("#viewGroupMembers").append("<li>" + member.last_name + ' ' + member.first_name +" "+ crown +"</li>");
        });
    }
    else if ($(event.target).hasClass("editGroup") || $(event.target).parents(".editGroup").length > 0) {

        groupID = $(event.target).parents(".object").attr("data-id");
        $.ajax({
            type: 'POST',
            url: "./api/group/checkPermission",
            data: "id=" + groupID,
            dataType: 'json',
            async: false
        })
            .done(function () {
                $("#editGroupform fieldset div").remove();
                group = groupsArray.find(group => group.id == groupID);
                members = [];
                group.members.forEach(member => {
                    members.push(JSON.parse(member));
                });

                let panel = $("#editGroupPanel");
                $("#wizard").css("display", "flex");
                $(".panel").css("display", "none");
                $("body").css("overflow", "hidden");
                panel.css("display", "flex");

                $('#editGroupName').attr("placeholder", "Nom");
                $('#editGroupName').val(group.name);
                $('#editGroupId').val(group.id);


                usersArray.forEach(user => {
                    if (members.find(member => member.id == user.id) != undefined) {
                        check = "checked";
                    }
                    else {
                        check = "";
                    }

                    $("#editGroupform fieldset").append(
                        '<div class="d-flex flex-rows align-items-center">' +
                        '<input ' + check + ' type="checkbox" id="' + user.id + '" name="members[]" value="' + user.id + '">' +
                        '<label class="ms-2" for="' + user.id + '">' + user.last_name + ' ' + user.first_name + '</label>' +
                        '</div>'
                    );
                });
            })
            .fail(function () {
                btn = $(event.target).hasClass("editIcon") ? $(event.target) : $(event.target).parents(".editIcon");
                playKeyframe(btn, "shake");

            });
    }
    else if ($(event.target).hasClass("deleteGroup") || $(event.target).parents(".deleteGroup").length > 0) {
        let groupID = $(event.target).parents(".object").attr("data-id");
        $.ajax({
            type: 'POST',
            url: "./api/group/checkPermission",
            data: "id=" + groupID,
            dataType: 'json',
            async: false
        })
            .done(function () {
                let panel = $("#deleteGroupPanel");
                $("#wizard").css("display", "flex");
                $(".panel").css("display", "none");
                $("body").css("overflow", "hidden");
                panel.css("display", "flex");

                group = groupsArray.find(group => group.id == groupID);

                $("#deleteGroupName").text(group.name);
                $("#deleteGroupId").val(group.id);

            })
            .fail(function () {
                btn = $(event.target).hasClass("deleteIcon") ? $(event.target) : $(event.target).parents(".deleteIcon");
                playKeyframe(btn, "shake");
            });

    }

    else if ($(event.target).attr("id") == "closeWizard") {
        $("body").css("overflow", "auto");
        $("#wizard").css("display", "none");
    }

    else if ($(event.target).attr("id") == "agendaBtn" || $(event.target).parents("#agendaBtn").length > 0) {
        $(".select-btn").parents("#select-agenda").toggleClass("active");
    }

    else if ($(event.target).attr("id") == "groupBtn" || $(event.target).parents("#groupBtn").length > 0) {
        $(".select-btn").parents("#select-group").toggleClass("active");
    }
    else if ($(event.target).attr("id") == "accountBtn" || $(event.target).parents("#accountBtn").length > 0) {
        $(".select-btn").parents("#account").toggleClass("active");
    }
    else if ($(event.target).attr("id") == "logout" || $(event.target).parents("#logout").length > 0) {
        document.location = "./auth/logout";
    }

});

$(document).on('change', function (event) {
    if ($(event.target).attr("id") == "yes") {
        $('#addAgendaGroupField').css("display", "flex");
    }
    else if ($(event.target).attr("id") == "no") {
        $('#addAgendaGroupField').css("display", "none");
    }
});

function getEvents() {
    range = getRange();
    var xhr = $.ajax({
        type: 'POST',
        url: "./api/agenda/get/events",
        data: "start=" + range[0] + '&end=' + range[1],
        dataType: 'json',
        async: false
    });
    xhr.done(function () {

        var events = JSON.parse(xhr.responseText.replace("/\\/g", ""))
        events.forEach(event => {
            var event = JSON.parse(event.replace("/\\/g", ""));
            // on ajoute l'event à la liste des events
            eventsArray.push(event);

        });

    });
}

function getGroups() {
    groupsArray = [];
    var xhr = $.ajax({
        type: 'POST',
        url: "./api/group/get/groups",
        data: "",
        dataType: 'json',
        async: false
    });
    xhr.done(function () {
        var groups = JSON.parse(xhr.responseText.replace("/\\/g", ""))
        groups.forEach(group => {
            var group = JSON.parse(group.replace("/\\/g", ""));
            groupsArray.push(group);

        });
    });
}

function getAgendas() {
    agendasArray = [];
    var xhr = $.ajax({
        type: 'POST',
        url: "./api/agenda/get/agendas",
        data: "",
        dataType: 'json',
        async: false
    });
    xhr.done(function () {
        var agendas = JSON.parse(xhr.responseText.replace("/\\/g", ""))
        agendas.forEach(agenda => {
            var agenda = JSON.parse(agenda.replace("/\\/g", ""));
            agendasArray.push(agenda);

        });
    });
}

function getLast_Agenda() {
    var xhr = $.ajax({
        type: 'POST',
        url: "./api/agenda/get/last_agenda",
        data: "",
        dataType: 'json',
        async: false
    });
    xhr.done(function () {
        last_agenda = xhr.responseText;
    });
}

function showEvents(date) {

    //on enlève tous les events de la page
    $(".event").remove();

    //on récupère la liste des events de la date sélectionnée
    events = []
    eventsArray.forEach(event => {
        if (event.startts.split(" ")[0] == date) {
            events.push(event);
        }
    });
    if (events.length == 0) {
        // si il n'y a pas d'event on affiche un message
        $('#events-wrapper').html("<h4>Aucun événement à cette date</h4>");
    }
    else {
        $('#events-wrapper').html("");
        var firstEvent = true;
        //si il y en a, on affiche les events de la date sélectionnée
        events.sort(function (a, b) {
            return new Date(a.startts) - new Date(b.startts); // ordonne les events par date de début 
        });
        events.forEach(event => {

            var edate = event.startts.split(" ")[0];

            if (edate == date) {


                var start = event.startts.split(" ")[1].slice(0, 5);
                var end = event.endts.split(" ")[1].slice(0, 5);

                var eventID = ""

                if (firstEvent) {
                    eventID = 'id="event"';
                }
                $("#events-wrapper").append(
                    '<div class="event object w-100 px-4 py-2 d-flex flex-row rounded-3 position-relative my-1 "' + eventID + ' data-id="' + event.id + '"  style="background-color: ' + event.color + ';">' +
                    '<div class="hours d-flex flex-column px-1 me-1">' +
                    '<span class="startts">' + start + '</span>' +
                    '<span class="endts">' + end + '</span>' +
                    '</div>' +
                    '<div class="info d-flex flex-column px-1 ms-2">' +
                    '<span class="title">' + event.title + '</span>' +
                    '<span class="description">' + event.description + '</span>' +
                    '</div>' +
                    '<button type="button" class="btn position-absolute top-0 end-0 rounded-circle deleteIcon deleteEvent" >' +
                    '<i class="fa-solid fa-trash"></i>' +
                    '</button>' +
                    '<button type="button" class="btn position-absolute top-0 rounded-circle editIcon editEvent">' +
                    '<i class="fa-solid fa-pen-to-square"></i>' +
                    '</button>' +
                    '</div>'

                );
                firstEvent = false;
            }


        });

    }

}

function showGroups() {
    $("#select-group .object").remove();
    groupsArray.forEach(group => {
        $("#select-group .options").append(
            '<li class="option object" data-id="' + group.id + '">' +
            '<span class="option-text text-break pe-1">' + group.name + '</span>' +
            '<div class="d-flex flex-row ">' +
            '<button type="button" class="deleteIcon deleteGroup btn position-absolute top-0 end-0 rounded-circle">' +
            '<i class="fa-solid fa-trash"></i>' +
            '</button>' +
            '<button type="button" class="editIcon editGroup btn position-absolute top-0 rounded-circle ">' +
            '<i class="fa-solid fa-pen-to-square"></i>' +
            '</button>' +
            '<button type="button" class="viewIcon viewGroup btn position-absolute top-0 rounded-circle ">' +
            '<i class="fa-solid fa-eye"></i>' +
            '</button>' +
            '</div>' +
            '</li>'

        );

    });

}

function showAgendas() {

    $("#select-agenda .object").remove();
    agendasArray.forEach(agenda => {
        let name = agenda.name;
        if (agenda.id == last_agenda) {
            name = "<u>" + agenda.name + "</u>";
        } 
        console.log(agenda.id)
        $("#select-agenda .options").append(
            '<li class="option object" data-id="' + agenda.id + '">' +
            '<span class="option-text w-75 text-break pe-1">' + name + '</span>' +
            '<div class="d-flex flex-rows w-25">' +
            '<button type="button" class="deleteIcon deleteAgenda btn position-absolute top-0 end-0 rounded-circle">' +
            '<i class="fa-solid fa-trash"></i>' +
            '</button>' +
            '</div>'

        );

    });
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function reloadEvents() {
    eventsArray = [];
    getEvents();
    showEvents(date);
}

function reloadGroups() {
    getUsers();
    groupsArray = [];
    getGroups();
    showGroups();
    let title = $('title').text()
    let agendaName = $("#select-agenda .options .option u").text();
    $('title').text(title.split(" | ")[0] + " | " + agendaName);
}

function reloadAgendas() {
    agendasArray = [];
    getAgendas();
    showAgendas();
}

function getUsers() {
    usersArray = [];
    var xhr = $.ajax({
        type: 'POST',
        url: "./api/user/get/users",
        data: "",
        dataType: 'json',
        async: false
    });
    xhr.done(function () {
        var users = JSON.parse(xhr.responseText.replace("/\\/g", ""))
        users.forEach(user => {
            user = JSON.parse(user);
            usersArray.push(user);

        });

    });
}

function getDate() {


    if ($('.hover').attr("id")) {
        return $('.hover').attr("id");
    }
    else if ($('.today').attr("id")) {
        return $('.today').attr("id");
    }
    else {
        return $('.cell')[0].id;
    }


}

function getRange() {

    return [$(".cell")[0].id + ' 00:00:00', $(".cell")[$(".cell").length - 1].id + ' 23:59:59'];


}

function playKeyframe(element, name) {
    element.addClass(name); // on ajoute la classe prédéfinie pour l'animation
    element.on("animationend", function () { // on attend la fin de l'animation
        element.removeClass(name); // on supprime la classe
        element.off("animationend"); // on désactive l'écouteur d'événement (pour éviter les bugs)
    });


}

addEventform.on('submit', function (e) {

    $("small").html("&nbsp;");
    $("#addEventToAgenda").attr("disabled", true);
    $("#addEventToAgenda").toggleClass("p-2");
    $("#addEventToAgenda").text('');
    $("#addEventToAgenda").html('<i class="fas fa-sync fa-spin fa-2x"></i>');


    e.preventDefault();

    var xhr = $.ajax({
        type: 'POST',
        url: addEventform.attr("action"),
        data: $(this).serialize() + "&date=" + date,
        dataType: 'json',
        async: false
    });
    xhr.done(function () {
        $("body").css("overflow", "auto");
        $("#wizard").css("display", "none");
        reloadEvents();
    });
    xhr.fail(function () {

        var error = JSON.parse(xhr.responseText);
        Object.entries(error).forEach(([key, value]) => {
            $("#" + key + "Help").text(value);
        });
    });
    xhr.always(function () {
        $("#addEventToAgenda").text("Ajouter");
        $("#addEventToAgenda").attr("disabled", false);
    });

});

deleteEventform.on('submit', function (e) {

    $("small").html("&nbsp;");
    $("#deleteEvent").attr("disabled", true);
    $("#deleteEvent").toggleClass("p-2");
    $("#deleteEvent").text('');
    $("#deleteEvent").html('<i class="fas fa-sync fa-spin fa-2x"></i>');


    e.preventDefault();

    var xhr = $.ajax({
        type: 'POST',
        url: deleteEventform.attr("action"),
        data: $(this).serialize(),
        dataType: 'json',
        async: false
    });
    xhr.done(function () {

        $("body").css("overflow", "auto");
        $("#wizard").css("display", "none");
        reloadEvents();
    });
    xhr.fail(function () {

        var error = JSON.parse(xhr.responseText);
        Object.entries(error).forEach(([key, value]) => {
            $("#" + key + "Help").text(value);
        });
    });
    xhr.always(function () {
        $("#deleteEvent").attr("disabled", false);
        $("#deleteEvent").text("Supprimer");

    });


});

editEventform.on('submit', function (e) {

    $("small").html("&nbsp;");
    $("#editEvent").toggleClass("p-2");

    $("#editEvent").attr("disabled", true);
    $("#editEvent").text('');
    $("#editEvent").html('<i class="fas fa-sync fa-spin fa-2x"></i>');

    e.preventDefault();


    var xhr = $.ajax({
        type: 'POST',
        url: editEventform.attr("action"),
        data: $(this).serialize() + "&date=" + date,
        dataType: 'json',
        async: false
    });
    xhr.done(function () {

        $("body").css("overflow", "auto");
        $("#wizard").css("display", "none");
        reloadEvents();

    });
    xhr.fail(function () {

        var error = JSON.parse(xhr.responseText);
        Object.entries(error).forEach(([key, value]) => {
            $("#" + key + "editHelp").text(value);
        });

    });
    xhr.always(function () {
        $("#editEvent").text("Modifier");
        $("#editEvent").attr("disabled", false);
    });
});

addGroupform.on('submit', function (e) {
    $("small").html("&nbsp;");
    $("#addAccountToGroup").attr("disabled", true);
    $("#addAccountToGroup").toggleClass("p-2");
    $("#addAccountToGroup").text('');
    $("#addAccountToGroup").html('<i class="fas fa-sync fa-spin fa-2x"></i>');


    e.preventDefault();

    let members = "members=" + $("#addGroupMembers").val().join(",");

    var xhr = $.ajax({
        type: 'POST',
        url: addGroupform.attr("action"),
        data: $(this).serialize(),
        dataType: 'json',
        async: false
    });
    xhr.done(function () {
        id = xhr.responseText;
        var xhr2 = $.ajax({
            type: 'POST',
            url: "./api/group/add",
            data: members + "&id=" + id,
            dataType: 'json',
            async: false
        });
        xhr.done(function () {
            $("body").css("overflow", "auto");
            $("#wizard").css("display", "none");
            reloadGroups();
        });
        xhr.always(function () {
            $("#addAccountToGroup").text("Ajouter");
            $("#addAccountToGroup").attr("disabled", false);
        });
    });
    xhr.fail(function () {

        var error = JSON.parse(xhr.responseText);
        Object.entries(error).forEach(([key, value]) => {
            $("#" + key + "Help").text(value);
        });
    });
    xhr.always(function () {
        $("#addAccountToGroup").text("Ajouter");
        $("#addAccountToGroup").attr("disabled", false);
    });
});

editGroupform.on('submit', function (e) {
    $("small").html("&nbsp;");
    $("#editGroupBtn").attr("disabled", true);
    $("#editGroupBtn").toggleClass("p-2");
    $("#editGroupBtn").text('');
    $("#editGroupBtn").html('<i class="fas fa-sync fa-spin fa-2x"></i>');
    e.preventDefault();
    var xhr = $.ajax({
        type: 'POST',
        url: editGroupform.attr("action"),
        data: $(this).serialize(),
        dataType: 'json',
        async: false
    })
        .done(function () {

            $("body").css("overflow", "auto");
            $("#wizard").css("display", "none");

            reloadGroups();
        }).fail(function (text) {

            var error = JSON.parse(text.responseText);
            Object.entries(error).forEach(([key, value]) => {
                $("#" + key + "editHelp").text(value);
            });
        }).always(function () {

            $("#editGroupBtn").text("Modifier");
            $("#editGroupBtn").attr("disabled", false);
        });


});
deleteGroupform.on('submit', function (e) {
    $("small").html("&nbsp;");
    $("#deleteGroup").attr("disabled", true);
    $("#deleteGroup").toggleClass("p-2");
    $("#deleteGroup").text('');
    $("#deleteGroup").html('<i class="fas fa-sync fa-spin fa-2x"></i>');


    e.preventDefault();

    var xhr = $.ajax({
        type: 'POST',
        url: deleteGroupform.attr("action"),
        data: $(this).serialize(),
        dataType: 'json',
        async: false
    });
    xhr.done(function () {

        $("body").css("overflow", "auto");
        $("#wizard").css("display", "none");
        reloadGroups();
    });
    xhr.fail(function () {

        var error = JSON.parse(xhr.responseText);
        Object.entries(error).forEach(([key, value]) => {
            $("#" + key + "Help").text(value);
        });
    });
    xhr.always(function () {
        $("#deleteGroup").attr("disabled", false);
        $("#deleteGroup").text("Supprimer");

    });
});
addAgendaform.on('submit', function (e) {
    $("small").html("&nbsp;");
    $("#addAccountToGroup").attr("disabled", true);
    $("#addAccountToGroup").toggleClass("p-2");
    $("#addAccountToGroup").text('');
    $("#addAccountToGroup").html('<i class="fas fa-sync fa-spin fa-2x"></i>');

    e.preventDefault();

    var xhr = $.ajax({
        type: 'POST',
        url: addAgendaform.attr("action"),
        data: $(this).serialize(),
        dataType: 'json',
        async: false
    });
    xhr.done(function () {
        $("body").css("overflow", "auto");
        $("#wizard").css("display", "none");
        getLast_Agenda();
        reloadAgendas();
    });
    xhr.fail(function () {

        var error = JSON.parse(xhr.responseText);
        Object.entries(error).forEach(([key, value]) => {
            $("#" + key + "Help").text(value);
        });
    });
    xhr.always(function () {
        $("#addAccountToGroup").text("Ajouter");
        $("#addAccountToGroup").attr("disabled", false);
    });
});

deleteAgendaform.on('submit', function (e) {
    $("small").html("&nbsp;");
    $("#deleteAgenda").attr("disabled", true);
    $("#deleteAgenda").toggleClass("p-2");
    $("#deleteAgenda").text('');
    $("#deleteAgenda").html('<i class="fas fa-sync fa-spin fa-2x"></i>');


    e.preventDefault();

    var xhr = $.ajax({
        type: 'POST',
        url: deleteAgendaform.attr("action"),
        data: $(this).serialize(),
        dataType: 'json',
        async: false
    });
    xhr.done(function () {

        $("body").css("overflow", "auto");
        $("#wizard").css("display", "none");
        reloadAgendas();
    });
    xhr.fail(function () {

        var error = JSON.parse(xhr.responseText);
        Object.entries(error).forEach(([key, value]) => {
            $("#" + key + "Help").text(value);
        });
    });
    xhr.always(function () {
        $("#deleteAgenda").attr("disabled", false);
        $("#deleteAgenda").text("Supprimer");

    });
});