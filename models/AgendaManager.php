<?php

class AgendaManager extends Model
{





    public function checkPermission(int $agendaOwnerID, int $accountID)
    {   
        // 10 est l'id du compte de l'admin
        if ($agendaOwnerID == $accountID || $accountID == 10 ) {
            return true;
        }
        return false;
    }


    public function createPersonnalAgenda(Agenda $agenda)
    {

        $agenda = $this->createAgenda($agenda);
        $this->addAgendaToAccount($agenda->getOwner_ID(), $agenda->getId());
        $this->updateLast_Agenda($agenda->getId());
    }

    public function createGroupAgenda(int $groupId, Agenda $agenda)
    {
        $groupManager = new GroupManager();
        $agenda = $this->createAgenda($agenda);
        $groupManager->addGrouptoAgenda($groupId, $agenda->getId());
       
        $this->updateLast_Agenda($agenda->getId());
        
    }

    private function createAgenda(Agenda $agenda)
    {

        $this->table = "AGENDAS";
        $agendaID = $this->save(
            [
                "selectors" => [
                    "owner_id" => $agenda->getOwner_ID(),
                    "name" => $agenda->getName()
                ],
                
                "RETURN" => ["id"]
            ]
        )["id"];

        $agenda->setId($agendaID);
        return $agenda;
    }

    public function addAgendaToAccount(int $accountID, int $agendaID)
    {
        $this->table = "ACCOUNT_TO_AGENDAS";
        $this->save(
            [
                "selectors" => [
                    "account_id" => $accountID,
                    "agenda_id" => $agendaID
                ]
            ]
        );
    }


    public function getAgendas()
    {
        $this->table = "AGENDAS";
        $agendasArray = $this->find();
        $agendas = [];
        foreach ($agendasArray as $agenda) {
            array_push($agendas, new Agenda($agenda));
        }
        return $agendas;

    }
    public function getAgendaByID(int $id)
    {

        $this->table = "AGENDAS";
        $agenda = $this->find(
            [   
                "conditions" => [
                    "AGENDAS.id  =" => $id
                ]
            ]
        );
        $agenda = new Agenda($agenda[0]);
        return $agenda;

    }

    public function getPersonnalAgendasByAccountID(int $accountId)
    {
        $this->table = "AGENDAS";
        $agendasArray = $this->find(
            [
                "selectors" => [
                    "AGENDAS.*",
                ],
                "innerJoin" => [
                    [
                        "table" => "ACCOUNT_TO_AGENDAS",
                        "compared" => "ACCOUNT_TO_AGENDAS.agenda_id ",
                        "operator" => "=",
                        "referring" => "AGENDAS.id"
                    ]
                ],
                "conditions" => [
                    "ACCOUNT_TO_AGENDAS.account_id  =" => $accountId
                ]
            ]
        );

        $agendas = [];
        foreach ($agendasArray as $agenda) {
            array_push($agendas, new Agenda($agenda));
        }
        return $agendas;
    }

    public function getGroupsAgendasByAccountId(int $accountId)
    {
        $this->table = "AGENDAS";
        $groupAgendas = $this->find(
            [
                "selectors" => [
                    "AGENDAS.*",
                ],
                "innerJoin" => [
                    [
                        "table" => "GROUP_TO_AGENDAS",
                        "compared" => "GROUP_TO_AGENDAS.agenda_id",
                        "operator" => "=",
                        "referring" => "AGENDAS.id"
                    ],
                    [
                        "table" => "ACCOUNT_TO_GROUPS",
                        "compared" => "ACCOUNT_TO_GROUPS.group_id",
                        "operator" => "=",
                        "referring" => "GROUP_TO_AGENDAS.group_id"
                    ]
                ],
                "conditions" => [
                    "ACCOUNT_TO_GROUPS.account_id =" => $accountId
                ]
            ]
        );

        $agendas = [];
        foreach ($groupAgendas as $agenda) {
            array_push($agendas, new Agenda($agenda));
        }
        return $agendas;
    }

    public function getAgendasByAccountID(int $accountid)
    {

        $personnalAgendas = $this->getPersonnalAgendasByAccountID($accountid);
        $groupAgendas = $this->getGroupsAgendasByAccountId($accountid);
        $agendas = array_merge($personnalAgendas, $groupAgendas);
        return $agendas;
    }

    public function getEventsByAgendaID(int $agendaID, array $range = [])
    {        

        $range = $this->checkRange($range);

        $this->table = "EVENTS";
        $events = $this->find(
            [
                "selectors" => [
                    "EVENTS.*",
                ],
                "innerJoin" => [
                    [   
                        "table" => "EVENTS_TO_AGENDAS",
                        "compared" => "EVENTS_TO_AGENDAS.event_id",
                        "operator" => "=",
                        "referring" => "EVENTS.id"
                    ]

                ],
                "conditions" => [
                    "EVENTS_TO_AGENDAS.agenda_id =" => $agendaID,
                    "EVENTS.startts  >= " => $range[0],
                    "EVENTS.endts  <= " => $range[1]
                    
                ]
            ]
        );
        
        $eventsArray = [];
        foreach ($events as $key => $value) {
            array_push($eventsArray, new Event($value));
        }
        return $eventsArray;

    }

    public function deleteAgenda(int $agendaID)
    {
        $this->table = "AGENDAS";
        $this->delete(
            [
                "conditions" => [
                    "AGENDAS.id =" => $agendaID
                ]
            ]
        );
    }


    
   //pattern pour la $range ["2022-02-16 12:36:10", "2022-09-16 12:36:10"]
   private function checkRange(array $range)
   {
       if (empty($range)) {
           if (!isset($_POST['month']) && !isset($_POST['year'])) {
               $month = date('m');
               $year = date('Y');
           } else {
               // peut être faire un regex ou autre à l'avenir pour check ...
               $month = $_POST['month'];
               $year = $_POST['year'];
           }
           $range = [
               $year . "-" . $month . "-01 00:00:00",
               $year . "-" . $month . "-" . date("t", mktime(0, 0, 0, $month, 1, $year)) . " 23:59:59"
           ];
       }
       return $range;
   }

   public function updateLast_Agenda(int $agendaID)
   {
    
    $_SESSION['last_agenda'] =$agendaID;
    $this->table = "ACCOUNTS";
    $this->save(
        [
            "selectors" => [
                "last_agenda" => $agendaID
            ],
            "id" => $_SESSION['accountid']

        ]
    );

   }

}
