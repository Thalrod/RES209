<?php


class EventManager extends Model
{
 

    public function createEvent(Event $event, int $agendaID){
        $this->table = "EVENTS";
        $eventid = $this->save(
            [
                "selectors" => [
                    "title" => $event->getTitle(),
                    "startts" => $event->getStartts(),
                    "endts" => $event->getEndts(),
                    "owner_id" => $event->getOwner_id(),
                    "export" => $event->getExport(),
                    "description" => $event->getDescription(),
                    "color" => $event->getColor(),
                ],
                "RETURN" => ["id"]
            ]
        )["id"];
        $event->setId($eventid);
        $this->addEventToAgenda($eventid, $agendaID);



    }

    public function getEventByID(int $id){

        $this->table = "EVENTS";
        $event = $this->find(
            [
            'conditions' => [
                'id =' => $id
                ]
            ]
        );
    
        $event = new Event($event[0]);
        return $event;

    }

    public function updateEvent(Event $event){

        $this->table = "EVENTS";
        $this->save(
            [
                "selectors" => [
                    "title" => (string) $event->getTitle(),
                    "description" => (string) $event->getDescription(),
                    "startts" => (string) $event->getStartts(),
                    "endts" => (string) $event->getEndts(),
                    "owner_id" => (int) $event->getOwner_id(),
                    "export" => (string) $event->getExport(),
                    "color" => (string) $event->getColor(),
                ],
                "id" => (int) $event->getId()
            ]
        );

    }


    public function deleteEvent(Event $event)
    {

        $this->table = "EVENTS";
        $this->delete(
            [
                "conditions" => [
                    "id =" => (int) $event->getId()
                ]
            ]
        );



    }

    public function addEventToAgenda(int $eventID,int $agendaID){
            
            $this->table = "EVENTS_TO_AGENDAS";
            $this->save(
                [
                    "selectors" => [
                        "agenda_id" =>$agendaID,
                        "event_id" => $eventID
                    ]
                ]
            );
    
    }


    public function checkPermission(int $eventOwnerID,int $accountID){
        if($eventOwnerID == $accountID || $accountID == 10){
            return true;
        }
        return false;
    }

    // ajouter fonction getGroupEvent($accountId, array $range = [])


   

    // ajouter la fonction addGroupEvent($accountId,Event event)
}
