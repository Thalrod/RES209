<?php


$projectName = "R&T Agenda";
$agenda = "Agenda";
$agendas = "Agendas";
$group = "Groupe";
$groups = "Groupes";
$account = "Compte";
$logout = "Déconnexion";
$currentTheme = "dark";
$nextTheme = "light";
$fullname = $data["lastname"] . " " . $data["firstname"];
$title = "Nom";
$add = "Ajouter";
$edit = "Modifier";
$delete = "Supprimer";
$description = "Description";
$date = "Date";
$start = "Début";
$end = "Fin";
$color = "Couleur";
$addAgenda = "Ajouter un agenda ";
$addGroup = "Ajouter un groupe ";
$members = "Membres";
$hasGroup = "A t'il un groupe ?";
$yes = "Oui";
$no = "Non";


$accountManager = new AccountManager();
$agendaManager = new AgendaManager();
$accounts = $accountManager->getAccounts();
//sort account by last name
usort($accounts, function ($a, $b) {
    return strcmp($a->getLast_name(), $b->getLast_name());
});


$suffix = ($_SESSION['last_agenda'] > 0) ? $agendaManager->getAgendaByID($_SESSION['last_agenda'])->getName() : "Home";
$this->_t = $projectName . " | " . $suffix;


?>

<body>

    <div id='viewport' class="h-100">
        <div id='sidebar' class="h-100">
            <div class='sb-h'>
                <div id='bg' class='img bg-wrap text-center pt-4' style='background-image: url(img/default_bg.jpg)'>
                    <div class='user-logo'>
                        <div id='logo' class='img' style='background-image: url(img/default_logo.jpg)'></div>
                        <h4><?= $fullname; ?></h4>
                    </div>
                </div>
                <a class='py-2' href='home'><?= $projectName; ?></a>
            </div class='sb-h'>
            <ul class='nav'>
                <li id="select-agenda" class="menu">
                    <div id="agendaBtn" class="select-btn py-2 px-4">
                        <i class='fa-solid fa-calendar me-2'></i>
                        <span class="sBtn-text"><?= $agendas ?></span>
                        <i class="fa-solid fa-caret-down end-0"></i>
                    </div>
                    <ul class="options">
                        <li class="option" id="addAgenda">
                            <button type="button" id="addAgendaBtn" class="option-btn w-100 h-100">
                                <i class="fa-solid fa-plus "></i>
                            </button>

                        </li>
                    </ul>
                </li>
                <li id="select-group" class="menu">
                    <div id="groupBtn" class="select-btn py-2 px-4">
                        <i class='fa-solid fa-user-group me-2'></i>
                        <span class="sBtn-text"><?= $groups ?></span>
                        <i class="fa-solid fa-caret-down end-0"></i>
                    </div>
                    <ul class="options">
                        <li class="option" id="addGroup">
                            <button type="button" id="addGroupBtn" class="option-btn w-100 h-100">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </li>
                    </ul>
                </li>
                <li id='account' class="menu">
                    <ul class="options">
                        <li class="option">
                            <button type="button" id="logout" class="option-btn w-100 h-100">
                                <i class="fa-solid fa-arrow-right-from-bracket start-0"></i>
                                <span class="option-text"><?= $logout ?></span>
                            </button>

                        </li>
                    </ul>
                    <div id="accountBtn" class="select-btn py-2 px-4 ">
                        <i class='fa-solid fa-user me-2'></i>
                        <?= $account ?>
                        <i class='fa-solid fa-caret-down end-0'></i>
                    </div>


                </li>
            </ul>
        </div>
        <div id='content' class="h-100 float-end">
            <nav class='navbar px-3'>
                <div class='container-fluid'>
                    <ul class='nav navbar-nav navbar-right flex-row align-items-center position-relative w-100'>
                        <?php
                            if($_SESSION["accountid"] == 10){
                            print('<li style="color: rgb(255, 0, 0);">Vous êtes administrateur !</li>');
                             print('<li class="mx-2 position-absolute top-0 end-0"><a href="#">'.$fullname.'</a></li>');
                            } else {
                             print('<li class="mx-2"><a href="/home"> '.$fullname.'</a></li>');
                            }
                            ?>
                        

                    </ul>
                </div>
            </nav>
            <main class='wrapper px-5 pt-5 d-flex flex-column'>
                <div id='calendar'>
                    <?php
                    $dateComponents = getdate();
                    if (isset($_POST['month']) && isset($_POST['year'])) {
                        $current_month = $_POST['month'];
                        $current_year = $_POST['year'];
                    } else {
                        $current_month = $dateComponents['mon'];
                        $current_year = $dateComponents['year'];
                    }

                    $agenda =  new Calendar($current_month, $current_year);
                    $agenda->renderCalendar();
                    ?>
                </div>

                <div class="d-flex justify-content-center my-4">
                    <button type="button" class="btn btn-primary" id="addEvent">Ajouter un évènement</button>
                </div>


                <div class='d-flex m-4 rounded-3 h-100' id='preview'>
                    <div id="events-wrapper" class="p-4 w-100 h-100 d-flex flex-column justify-content-center text-center">
                    </div>
                </div>

            </main>
        </div>
    </div>
    <div id="wizard">
        <div id="addEventPanel" class="panel">
            <div class="w-100 position-relative">
                <div class="text-center">
                    <h1>Ajouter un évènement</h1>
                    <h2>pour le: <span style="color: #E9EAED;" id="addEventDate"></span></h2>
                </div>
                <button type="button" class="btn position-absolute top-0 end-0" id="closeWizard">X</button>
            </div>

            <form id="addEventform" class="w-100 h-100 px-4 d-flex flex-column align-items-center justify-content-around " action="./api/event/create" , method="POST">
                <div class="form-group h-75 w-100 d-flex flex-row">
                    <div class="form-group w-50 d-flex flex-column">
                        <div class="form-group px-2">
                            <label for="addEventTitle"><?= $title ?></label>
                            <input type="text" class="form-control" id="addEventTitle" name="title" placeholder="">
                            <small id="titleHelp" class="text-danger">&nbsp;</small>
                        </div>
                        <div class="form-group h-75 p-2">
                            <label for="addEventDescription"><?= $description ?></label>
                            <textarea type="text" class="h-100 form-control" id="addEventDescription" name="description" placeholder=""></textarea>
                            <small id="descriptionHelp" class="text-danger">&nbsp;</small>
                        </div>
                    </div>
                    <div class="form-group w-50 px-2">
                        <div class="form-group px-2">
                            <label for="addEventStartts"><?= $start ?></label>
                            <input type="time" class="form-control" id="addEventStartts" name="startts" placeholder="">
                            <small id="starttsHelp" class="text-danger">&nbsp;</small>
                        </div>
                        <div class="form-group p-2">
                            <label for="addEventEndts"><?= $end ?></label>
                            <input type="time" class="form-control" id="addEventEndts" name="endts" placeholder="">
                            <small id="endtsHelp" class="text-danger">&nbsp;</small>
                        </div>

                        <div class="form-group h-25 px-2">
                            <label for="addEventColor"><?= $color ?></label>
                            <input type="color" class="h-100 form-control" id="addEventColor" name="color">
                            <small id="colorHelp" class="text-danger">&nbsp;</small>
                        </div>
                    </div>
                </div>
                <button type="submit" class="w-50 btn btn-primary my-2" id="addEventToAgenda" value="submit"><?= $add ?></button>
            </form>
        </div>
        <div id="deleteEventPanel" class="panel">
            <div class="w-100 h-100 p-4 d-flex flex-column align-items-center justify-content-between position-relative">

                <div class="text-center">
                    <h2>Êtes vous sur de vouloir supprimer cet évènement ?</h2>
                </div>
                <h2><span style="color: #E9EAED;" id="deleteEventTitle"></span></h2>
                <h2 class="w-75">le: <span style="color: #E9EAED;" id="deleteEventDate"></span></h2>
                <h2 class="w-75">à: <span style="color: #E9EAED;" id="deleteEventStart"></span></h2>
                <h2 class="w-75">jusqu'à: <span style="color: #E9EAED;" id="deleteEventEnd"></span></h2>
                <button type="button" class="btn position-absolute top-0 end-0" id="closeWizard">X</button>

                <form id="deleteEventform" class="w-50" action="./api/event/delete">
                    <input type="text" class="form-control d-none" id="deleteEventId" value="" name="id">
                    <button type="submit" class="w-100 btn btn-primary my-2" id="deleteEvent" value="submit"><?= $delete ?></button>
                </form>
            </div>
        </div>
        <div id="editEventPanel" class="panel">
            <div class="w-100 position-relative">
                <div class="text-center">
                    <h1>Modifier un évènement</h1>
                    <h2>le: <span style="color: #E9EAED;" id="editEventdate"></span></h2>
                </div>
                <button type="button" class="btn position-absolute top-0 end-0" id="closeWizard">X</button>
            </div>
            <form id="editEventform" class="w-100 h-100 px-4 d-flex flex-column align-items-center justify-content-around " action="./api/event/update" , method="POST">
                <div class="form-group h-75 w-100 d-flex flex-row">
                    <div class="form-group w-50 d-flex flex-column">
                        <input type="text" class="form-control" id="editEventId" name="id" style="display: none;">
                        <div class="form-group px-2">
                            <label for="editEventTitle"><?= $title ?></label>
                            <input type="text" class="form-control" id="editEventTitle" name="title" placeholder="">
                            <small id="titleeditHelp" class="text-danger">&nbsp;</small>
                        </div>
                        <div class="form-group h-75 p-2">
                            <label for="editEventDescription"><?= $description ?></label>
                            <textarea type="text" class="h-100 form-control" id="editEventDescription" name="description" placeholder=""></textarea>
                            <small id="descriptioneditHelp" class="text-danger">&nbsp;</small>
                        </div>
                    </div>
                    <div class="form-group w-50 px-2">
                        <div class="form-group px-2">
                            <label for="editEventStartts"><?= $start ?></label>
                            <input type="time" class="form-control" id="editEventStartts" name="startts" placeholder="">
                            <small id="starttseditHelp" class="text-danger">&nbsp;</small>
                        </div>
                        <div class="form-group p-2">
                            <label for="editEventEndts"><?= $end ?></label>
                            <input type="time" class="form-control" id="editEventEndts" name="endts" placeholder="">
                            <small id="endtseditHelp" class="text-danger">&nbsp;</small>
                        </div>

                        <div class="form-group h-25 px-2">
                            <label for="editEventColor"><?= $color ?></label>
                            <input type="color" class="h-100 form-control" id="editEventColor" name="color">
                            <small id="coloreditHelp" class="text-danger">&nbsp;</small>
                        </div>
                    </div>
                </div>
                <button type="submit" class="w-50 btn btn-primary my-2" id="editEvent" value="submit"><?= $edit ?></button>
            </form>
        </div>

        <div id="addGroupPanel" class="panel">
            <div class="w-100 position-relative">
                <div class="text-center">
                    <h1><?= $addGroup ?></h1>
                </div>
                <button type="button" class="btn position-absolute top-0 end-0" id="closeWizard">X</button>
            </div>

            <form id="addGroupform" class="w-100 h-100 px-4 d-flex flex-column align-items-center justify-content-around " action="./api/group/create" , method="POST">
                <div class="form-group h-75 w-100 d-flex flex-row">
                    <div class="form-group w-50 d-flex flex-column justify-content-center">
                        <div class="form-group px-2">
                            <label for="addGroupName"><?= $title ?></label>
                            <input type="text" class="form-control" id="addGroupName" name="name" placeholder="">
                            <small id="nameHelp" class="text-danger">&nbsp;</small>
                        </div>
                    </div>
                    <div class="form-group w-50 px-2">
                        <div class="form-group px-2 h-100">
                            <span><?= $members ?></span>
                            <select class="form-control h-100" id="addGroupMembers" multiple>
                                <?php foreach ($accounts as $account) {
                                    if ($account->getId() != $_SESSION['accountid'] && $account->getId() != 10) {
                                        echo '<option value="' . $account->getId() . '">' . $account->getLast_name() . ' ' . $account->getFirst_name() . '</option>';
                                    }
                                } ?>
                            </select>
                            <small id="usersHelp" class="text-danger">&nbsp;</small>

                        </div>
                    </div>
                </div>


                <button type="submit" class="btn w-50 btn btn-primary my-2" id="addAccountToGroup" value="submit"><?= $add ?></button>
            </form>
        </div>
        <div id="deleteGroupPanel" class="panel">
            <div class="w-100 h-100 p-4 d-flex flex-column align-items-center justify-content-between position-relative">

                <div class="text-center">
                    <h2>Êtes vous sur de vouloir supprimer ce groupe ?</h2>
                </div>
                <h2><span style="color: #E9EAED;" id="deleteGroupName"></span></h2>
                <button type="button" class="btn position-absolute top-0 end-0" id="closeWizard">X</button>

                <form id="deleteGroupform" class="w-50" action="./api/group/delete">
                    <input type="text" class="form-control d-none" id="deleteGroupId" value="" name="id">
                    <button type="submit" class="w-100 btn btn-primary my-2" id="deleteGroup" value="submit"><?= $delete ?></button>
                </form>
            </div>
        </div>
        <div id="editGroupPanel" class="panel">
            <div class="w-100 position-relative">
                <div class="text-center">
                    <h1><?= $addGroup ?></h1>
                </div>
                <button type="button" class="btn position-absolute top-0 end-0" id="closeWizard">X</button>
            </div>

            <form id="editGroupform" class="w-100 h-100 px-4 d-flex flex-column align-items-center justify-content-around " action="./api/group/edit" , method="POST">
                <div class="form-group h-75 w-100 d-flex flex-row">
                    <div class="form-group w-50 d-flex flex-column justify-content-center">
                        <div class="form-group px-2">
                            <label for="editGroupName"><?= $title ?></label>
                            <input type="text" class="form-control" id="editGroupName" name="name" placeholder="">
                            <small id="nameeditHelp" class="text-danger">&nbsp;</small>
                        </div>
                    </div>
                    <div class="form-group w-50 px-2">
                        <div class="form-group px-2 h-100">
                            <input type="text" id="editGroupId" style="display: none;" value="" name="id">
                            <fieldset class='d-flex flex-column'>
                                <legend><?= $members ?></legend>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn w-50 btn btn-primary my-2" id="editGroupBtn" value="submit"><?= $edit ?></button>
            </form>
        </div>

        <div id="viewGroupPanel" class="panel">
            <div class="w-100 h-100 p-4 d-flex flex-column align-items-center justify-content-between position-relative">

                <div class="text-center">
                    <h2 id="viewGroupName" style="color: #E9EAED;"></h2>
                </div>
                <ul id="viewGroupMembers" class="w-50 "></ul>
                <button type="button" class="btn position-absolute top-0 end-0" id="closeWizard">X</button>
            </div>

        </div>
        <div id="addAgendaPanel" class="panel">
            <div class="w-100 position-relative">
                <div class="text-center">
                    <h1>Créer un agenda</h1>
                </div>
                <button type="button" class="btn position-absolute top-0 end-0" id="closeWizard">X</button>
            </div>
            <form id="addAgendaform" class="w-100 h-100 px-4 d-flex flex-column align-items-center justify-content-around " action="./api/agenda/create" , method="POST">
                <div class="form-group h-75 w-100 d-flex flex-row">
                    <div class="form-group w-100 px-2">
                        <div class="form-group px-2">
                            <label for="addAgendaName"><?= $title ?></label>
                            <input type="text" class="form-control" id="addAgendaName" name="name" placeholder="">
                            <small id="nameHelp" class="text-danger">&nbsp;</small>
                            <fieldset class="d-flex flex-column">
                                <legend><?= $hasGroup ?></legend>
                                <div>
                                    <div class="d-flex flex-rows ">
                                        <div class="me-3">
                                            <input type="radio" id="yes" value="yes" name="hasGroup">
                                            <label for="yes"><?= $yes ?></label>
                                        </div>
                                        <div class="ms-3">
                                            <input type="radio" id="no" value="no" name="hasGroup" checked>
                                            <label for="no"><?= $no ?></label>
                                        </div>
                                    </div>
                                    <div id="addAgendaGroupField" class="flex-column justify-content-center aligns-items-center w-100 mt-2">
                                        <label for="addAgendaGroup">Groupe(s): </label>
                                        <select name="groupid" id="addAgendaGroup">
                                        <option value="default">Veuillez chosir un groupe:</option>
                                        </select>

                                    </div>

                                </div>

                            </fieldset>
                        </div>
                    </div>
                </div>
                <button type="submit" class="w-50 btn btn-primary my-2" id="addAgenda" value="submit"><?= $add ?></button>
            </form>
        </div>
        <div id="deleteAgendaPanel" class="panel">
            <div class="w-100 h-100 p-4 d-flex flex-column align-items-center justify-content-between position-relative">

                <div class="text-center">
                    <h2>Êtes vous sur de vouloir supprimer cet agenda ?</h2>
                </div>
                <h2><span style="color: #E9EAED;" id="deleteAgendaName"></span></h2>
                <button type="button" class="btn position-absolute top-0 end-0" id="closeWizard">X</button>

                <form id="deleteAgendaform" class="w-50" action="./api/agenda/delete">
                    <input type="text" class="form-control d-none" id="deleteAgendaId" value="" name="id">
                    <button type="submit" class="w-100 btn btn-primary my-2" id="deleteAgenda" value="submit"><?= $delete ?></button>
                </form>
            </div>
        </div>
    </div>
</body>