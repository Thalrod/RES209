<?php


class ControllerApi extends Controller
{
    /*
    /api
    /api/agenda
    /api/agenda/checkPermission

    /api/agenda/get
    /api/agenda/get/agenda
    /api/agenda/get/agendas
    /api/agenda/get/events
    /api/agenda/get/last_agenda

    /api/agenda/create
    /api/agenda/delete
    /api/agenda/load
    /api/event
    /api/group
    /api/user

    */

    public function __construct(array $url)
    {
        $this->_url = $url;
        $errors = [];

        if (isset($url[1]) && !empty($url[1])) {

            switch ($url[1]) {
                case 'agenda':
                    if (isset($url[2]) && !empty($url[2])) {
                        $agendaManager = new AgendaManager();
                        $groupManager = new GroupManager();
                        switch ($url[2]) {
                            case 'checkPermission':
                                if (isset($_SESSION['accountid'], $_SESSION['last_agenda']) && !empty($_SESSION['accountid']) && !empty($_SESSION['last_agenda'])) {

                                    if (isset($_POST['id'])  && !empty($_POST['id'])) {
                                        $agenda = $agendaManager->getAgendaByID($_POST['id']);
                                    } else if ($_SESSION['last_agenda'] > 0) {
                                        $agenda = $agendaManager->getAgendaByID($_SESSION['last_agenda']);
                                    }

                                    if (isset($agenda)) {
                                        $canEdit = $agendaManager->checkPermission($agenda->getOwner_ID(), $_SESSION['accountid']);
                                        if ($canEdit) {
                                            $this->sendJsonResponse(200, 'ok');
                                        } else {
                                            // 401 unauthorized n'est pas pris en charge par ajax pour la gestion d'erreur donc je met 400
                                            $this->sendJsonResponse(400, 'no');
                                        }
                                    } else {
                                        $this->sendJsonResponse(400, 'no');
                                    }
                                } else {
                                    $this->sendJsonResponse(400, "Invalid arguments");
                                }
                                break;

                            case 'get':
                                if (isset($url[3]) && !empty($url[3])) {
                                    switch ($url[3]) {
                                        case 'agenda':
                                            if (isset($_POST['id']) && !empty($_POST['id'])) {
                                                $agenda = $agendaManager->getAgendaByID($_POST['id']);
                                                $this->sendJsonResponse(200, $agenda->toJson());
                                            } else {
                                                $this->sendJsonResponse(400, 'Missing parameters');
                                            }
                                            break;

                                        case 'agendas':
                                            if (isset($_SESSION['accountid']) && !empty($_SESSION['accountid'])) {
                                                $agendaArray = $_SESSION['accountid'] == 10 ? $agendaManager->getAgendas() : $agendaManager->getAgendasByAccountID($_SESSION['accountid']);

                                                $agendas = [];
                                                foreach ($agendaArray as $agenda) {
                                                    $agendas[] = $agenda->toJson();
                                                }
                                                $this->sendJsonResponse(200, $agendas);
                                            } else {
                                                $this->sendJsonResponse(400, 'Missing parameters');
                                            }
                                            break;
                                        case 'events':
                                            if (isset($_SESSION['last_agenda'], $_POST['start'], $_POST['end']) && !empty($_SESSION['last_agenda']) && !empty($_POST['start']) && !empty($_POST['end'])) {
                                                $eventArray = $agendaManager->getEventsByAgendaID($_SESSION['last_agenda'], [$_POST['start'], $_POST['end']]);
                                                $events = [];
                                                foreach ($eventArray as $event) {
                                                    $events[] = $event->toJson();
                                                }
                                                $this->sendJsonResponse(200, $events);
                                            } else {
                                                $this->sendJsonResponse(400, 'Missing parameters');
                                            }
                                            break;
                                        case 'last_agenda':
                                            if (isset($_SESSION['last_agenda']) && !empty($_SESSION['last_agenda'])) {
                                                $this->sendJsonResponse(200, $_SESSION['last_agenda']);
                                            } else {
                                                $this->sendJsonResponse(400, 'Missing parameters');
                                            }
                                            break;
                                        default:
                                            $this->sendJsonResponse(400, 'Invalid request');
                                            break;
                                    }
                                } else {
                                    $this->goto("home");
                                }
                                break;
                            case 'create':

                                if (isset($_POST['name'], $_SESSION['accountid'], $_POST['hasGroup']) && !empty($_POST['name']) && !empty($_SESSION['accountid']) && !empty($_POST['hasGroup'])) {
                                    $agenda = new Agenda(array(
                                        'name' => $_POST['name'],
                                        'owner_id' => $_SESSION['accountid']
                                    ));

                                    if (count($agenda->getErrors()) == 0) {
                                        // si il n'y a pas d'erreur



                                        if ($_POST['hasGroup'] == "yes") {
                                            if (isset($_POST['groupid']) && !empty($_POST['groupid'])) {

                                                $group = $groupManager->getGroupByID($_POST['groupid']);

                                                if ($group && $groupManager->checkPermission($group->getOwner_ID(), $_SESSION['accountid'])) {

                                                    $agendaManager->createGroupAgenda($group->getId(), $agenda);
                                                    
                                                    $this->sendJsonResponse(200, ["status" => "ok"]);

                                                } else {
                                                    $this->sendJsonResponse(400, 'Groupe Invalide');
                                                }
                                               
                                            } else {
                                                $this->sendJsonResponse(400, 'Missing parameters');
                                            }
                                        } else {
                                            $agendaManager->createPersonnalAgenda($agenda);
                                            $this->sendJsonResponse(200, ["status" => "ok"]);
                                        }
                                    } else {
                                        $this->sendJsonResponse(400, $agenda->getErrors());
                                    }
                                } else {
                                    $this->sendJsonResponse(400, 'Missing parameters');
                                }

                                // sinon tu retournes erreur
                                break;
                            case 'delete':
                                // on recup avec la l'url /api/agenda/delete/
                                // et en post on a l'id de l'agenda
                                // tu check les vars
                                // sinon tu retournes erreur

                                if (isset($_POST['id']) && !empty($_POST['id'])) {
                                    $agenda = $agendaManager->getAgendaByID($_POST['id']);

                                    if ($agendaManager->checkPermission($agenda->getOwner_id(), $_SESSION['accountid'])) {

                                        if ($agenda->getId() == $_SESSION['last_agenda']) {
                                            $agendaManager->updateLast_Agenda(-1);
                                        }


                                        $agendaManager->deleteAgenda($agenda->getId());



                                        $this->sendJsonResponse(200, 'Agenda deleted');
                                    } else {
                                        $this->sendJsonResponse(400, 'You don\'t have the permission to delete this agenda');
                                    }
                                } else {
                                    $this->sendJsonResponse(400, 'Missing parameters ');
                                }

                                break;
                            case 'load':
                                if (isset($_POST['id']) && !empty($_POST['id'])) {

                                    $agenda = $agendaManager->getAgendaByID($_POST['id']);
                                    $agendaManager->updateLast_Agenda($agenda->getId());

                                    $this->sendJsonResponse(200, 'Agenda loaded');
                                } else {
                                    $this->sendJsonResponse(400, 'Missing parameters ');
                                }

                                break;
                            default:
                                $this->sendJsonResponse(400, 'Invalid request');
                                break;
                        }
                    } else {
                        $this->goto('home');
                    }
                    break;
                case 'event':
                    if (isset($url[2]) && !empty($url[2])) {
                        $eventManager = new EventManager();
                        $agendaManager = new AgendaManager();
                        switch ($url[2]) {
                            case 'create':
                                if (isset($_POST['title'], $_POST['description'], $_POST['startts'], $_POST['endts'], $_SESSION['accountid'], $_POST['color'], $_POST['date'])) {
                                    $agenda = $agendaManager->getAgendaByID($_SESSION['last_agenda']);
                                    if (!$agendaManager->checkPermission($agenda->getOwner_ID(), $_SESSION['accountid'])) {
                                        $this->sendJsonResponse(400, 'Vous ne pouvez pas créer un évènement dans cette agenda');
                                    }
                                    $event = new Event(array(
                                        'title' => $_POST['title'],
                                        'description' => $_POST['description'],
                                        'startts' => $_POST['date'] . " " . $_POST['startts'] . ":00",
                                        'endts' => $_POST['date'] . " " . $_POST['endts'] . ":00",
                                        'owner_id' => $_SESSION['accountid'],
                                        'export' => date('Y-m-d H:i:s'),
                                        'color' => $_POST['color'],
                                    ));

                                    if (count($event->getErrors()) == 0) {
                                        // si il n'y a pas d'erreur et si l'utilisateur a le droit de créer un évènement
                                        // on recheck les permissions parce que le 1er check se fait en js et l'utilisateur peut modifier le fichier js comme il veut
                                        $eventManager->createEvent($event, $_SESSION['last_agenda']);

                                        $this->sendJsonResponse(200, ["status" => "ok"]);
                                    } else {
                                        $this->sendJsonResponse(400, $event->getErrors());
                                    }
                                } else {
                                    //check which fields are empty
                                    $this->sendJsonResponse(400, 'Missing parameters');
                                }



                                break;
                            case 'get':
                                if (isset($_POST['id']) && !empty($_POST['id'])) {
                                    $event = $eventManager->getEventByID($_POST['id']);
                                    $this->sendJsonResponse(200, $event);
                                } else {
                                    $this->sendJsonResponse(400, 'Missing parameters');
                                }

                                break;
                            case 'update':
                                if (isset($_POST['id'], $_POST['title'], $_POST['description'], $_POST['startts'], $_POST['endts'], $_SESSION['accountid'], $_POST['color'], $_POST['date'])) {
                                    $event = new Event(array(
                                        'id' => $_POST['id'],
                                        'title' => $_POST['title'],
                                        'description' => $_POST['description'],
                                        'startts' => $_POST['date'] . " " . $_POST['startts'] . ":00",
                                        'endts' => $_POST['date'] . " " . $_POST['endts'] . ":00",
                                        'owner_id' => $_SESSION['accountid'],
                                        'export' => date('Y-m-d H:i:s'),
                                        'color' => $_POST['color'],
                                    ));

                                    if (count($event->getErrors()) == 0 && $eventManager->checkPermission($event->getOwner_id(), $_SESSION['accountid'])) {
                                        // si il n'y a pas d'erreur et si l'utilisateur a le droit de créer un évènement
                                        // on recheck les permissions parce que le 1er check se fait en js et l'utilisateur peut modifier le fichier js comme il veut

                                        $eventManager->updateEvent($event);

                                        $this->sendJsonResponse(200, ["status" => "ok"]);
                                    } else {
                                        $this->sendJsonResponse(400, $event->getErrors());
                                    }
                                } else {
                                    //check which fields are empty
                                    $this->sendJsonResponse(400, 'Missing parameters');
                                }
                                break;
                            case 'delete':
                                if (isset($_POST['id']) && !empty($_POST['id'])) {
                                    $event = $eventManager->getEventByID($_POST['id']);

                                    if ($eventManager->checkPermission($event->getOwner_id(), $_SESSION['accountid'])) {
                                        $eventManager->deleteEvent($event);
                                        $this->sendJsonResponse(200, 'Event deleted');
                                    } else {
                                        $this->sendJsonResponse(400, 'You don\'t have the permission to delete this event');
                                    }
                                } else {
                                    $this->sendJsonResponse(400, 'Missing parameters ');
                                }
                                break;

                            case 'checkPermission':
                                if (isset($_SESSION['accountid'], $_POST['id']) && !empty($_SESSION['accountid']) && !empty($_POST['id'])) {
                                    $event = $eventManager->getEventByID($_POST['id']);

                                    $canEdit = $eventManager->checkPermission($event->getOwner_id(), $_SESSION['accountid']);;
                                    if ($canEdit) {
                                        $this->sendJsonResponse(200, 'ok');
                                    } else {
                                        // 401 unauthorized n'est pas pris en charge par ajax pour la gestion d'erreur donc je met 400
                                        $this->sendJsonResponse(400, 'no');
                                    }
                                } else {
                                    $this->sendJsonResponse(400, ["Invalid arguments" => "You must be logged in to access this page"]);
                                }
                                break;
                            default:
                                $this->sendJsonResponse(400, 'Invalid request');
                                break;
                        }
                    } else {
                        $this->goto('home');
                    }
                    break;

                case 'group':
                    if (isset($url[2]) && !empty($url[2])) {
                        $groupManager = new GroupManager();
                        switch ($url[2]) {

                            case 'checkPermission':
                                // api/group/checkPermission
                                if (isset($_SESSION['accountid'], $_POST['id']) && !empty($_SESSION['accountid']) && !empty($_POST['id'])) {
                                    $group = $groupManager->getGroupByID($_POST['id']);
 
                                    $canEdit = $groupManager->checkPermission($group->getOwner_ID(), $_SESSION['accountid']);
                                    if ($canEdit) {
                                        $this->sendJsonResponse(200, 'ok');
                                    } else {
                                        // 401 unauthorized n'est pas pris en charge par ajax pour la gestion d'erreur donc je met 400
                                        $this->sendJsonResponse(400, 'no');
                                    }
                                } else {
                                    $this->sendJsonResponse(400, "Invalid arguments");
                                }
                                break;
                            case 'get':
                                // api/group/get
                                if (isset($url[3]) && !empty($url[3])) {
                                    switch ($url[3]) {
                                        case 'group':
                                            if (isset($_POST['id']) && !empty($_POST['id'])) {
                                                $group = $groupManager->getGroupByID($_POST['id']);
                                                $this->sendJsonResponse(200, $group[0]->toJson());
                                            } else {
                                                $this->sendJsonResponse(400, 'Missing parameters');
                                            }
                                            break;

                                        case 'groups':
                                            if (isset($_SESSION['accountid']) && !empty($_SESSION['accountid'])) {
                                                $groupsArray = $_SESSION['accountid'] == 10 ? $groupManager->getGroups() : $groupManager->getGroupsByAccountID($_SESSION['accountid']);
                                                $groups = [];
                                                foreach ($groupsArray as $group) {
                                                    $groups[] = $group->toJson();
                                                }
                                                $this->sendJsonResponse(200, $groups);
                                            } else {
                                                $this->sendJsonResponse(400, 'Missing parameters');
                                            }
                                            break;

                                        default:
                                            $this->sendJsonResponse(400, 'Invalid request');
                                            break;
                                    }
                                } else {
                                    $this->goto("home");
                                }
                                break;
                            case 'create':
                                if (isset($_POST['name'], $_SESSION['accountid']) && !empty($_SESSION['accountid'])) {
                                    $group = new Group(array(
                                        'name' => $_POST['name'],
                                        'owner_id' => $_SESSION['accountid']
                                    ));

                                    if (count($group->getErrors()) == 0 && $groupManager->checkPermission($group->getOwner_id(), $_SESSION['accountid'])) {
                                        $groupID = $groupManager->createGroup($group);
                                        $this->sendJsonResponse(200, $groupID);
                                    } else {
                                        $this->sendJsonResponse(400, $group->getErrors());
                                    }
                                } else {
                                    //check which fields are empty
                                    $this->sendJsonResponse(400, 'Missing parameters');
                                }
                                break;
                            case 'delete':
                                // api/group/delete
                                // et en post on a l'id de l'group
                                // tu check les vars
                                // sinon tu retournes erreur

                                if (isset($_POST['id']) && !empty($_POST['id'])) {
                                    $group = $groupManager->getGroupByID($_POST['id']);

                                    if ($groupManager->checkPermission($group->getOwner_id(), $_SESSION['accountid'])) {


                                        $groupManager->deleteGroup($group->getId());



                                        $this->sendJsonResponse(200, 'Group deleted');
                                    } else {
                                        $this->sendJsonResponse(400, 'You don\'t have the permission to delete this group');
                                    }
                                } else {
                                    $this->sendJsonResponse(400, 'Missing parameters ');
                                }

                                break;
                            case "add":
                                // api/group/add
                                if (isset($_POST['members'], $_POST['id']) && !empty($_POST['members']) && !empty($_POST['id'])) {
                                    $group = $groupManager->getGroupByID($_POST['id']);
                                    $members = explode(',', $_POST['members']);
                                    if ($groupManager->checkPermission($group->getOwner_id(), $_SESSION['accountid'])) {
                                        $groupManager->addMembers($group->getId(), $members);
                                        $this->sendJsonResponse(200, ["status" => "ok"]);
                                    } else {
                                        $this->sendJsonResponse(400, 'You don\'t have the permission to add members to this group');
                                    }
                                } else {
                                    $this->sendJsonResponse(400, 'Missing parameters');
                                }
                                break;

                            case "edit":
                                // api/group/edit
                                if (isset($_POST['members'], $_POST['id'], $_POST['name']) && !empty($_POST['members']) && !empty($_POST['id']) && !empty($_POST['name'])) {
                                    $group = $groupManager->getGroupByID($_POST['id']);

                                    if ($groupManager->checkPermission($group->getOwner_id(), $_SESSION['accountid'])) {
                                        $group->setMembers($_POST['members']);
                                        $group->setName($_POST['name']);
                                        if (count($group->getErrors()) > 0) {
                                            $this->sendJsonResponse(400, $group->getErrors());
                                        } else {

                                            if (sizeof($_POST['members']) == 0 || !in_array($group->getOwner_id(), $_POST['members'])) {
                                                $groupManager->deleteGroup($group->getId());
                                                $this->sendJsonResponse(200, ["status" => "ok"]);
                                            } else {
                                                $groupManager->editGroup($group);
                                                $this->sendJsonResponse(200, ["status" => "ok"]);
                                            }
                                        }
                                    } else {
                                        $this->sendJsonResponse(400, 'You don\'t have the permission to edit this group');
                                    }
                                } else {
                                    $this->sendJsonResponse(400, 'Missing parameters');
                                }
                                break;
                            default:
                                $this->sendJsonResponse(400, 'Invalid request');
                                break;
                        }
                    } else {
                        $this->goto('home');
                    }
                    break;
                case 'user':
                    if (isset($url[2]) && !empty($url[2])) { 

                        switch ($url[2]) {
                            case 'get':
                                if (isset($url[3]) && !empty($url[3])) {
                                    $accountManager  = new AccountManager();
                                    switch ($url[3]) {
                                        case 'users':
                                            if (isset($_SESSION['accountid']) && !empty($_SESSION['accountid'])) {
                                                $accountsArray = $accountManager->getAccounts();
                                                $accounts = [];
                                                foreach ($accountsArray as $account) {
                                                    //10 c'est l'id du compte de l'admin comme ça on ne le voit pas dans les utilisateue
                                                    if($account->getId() != 10){
                                                        $accounts[] = $account->toJson();
                                                    }
                                                }
                                                $this->sendJsonResponse(200, $accounts);
                                            } else {
                                                $this->sendJsonResponse(400, 'Missing parameters');
                                            }
                                            break;

                                        default:
                                            $this->sendJsonResponse(400, 'Invalid request');
                                            break;
                                    }
                                }
                                break;
                            default:
                                $this->sendJsonResponse(400, 'Invalid request');
                                break;
                        }
                    } else {
                        $this->goto('home');
                    }
                    break;
                default:
                    $this->goto('home');
                    break;
            }
        } else {
            $this->goto('home');
        }
    }
}
