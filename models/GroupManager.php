<?php

class GroupManager extends Model
{

    public function checkPermission(int $groupOwnerID, int $accountID)
    {
        if ($groupOwnerID == $accountID || $accountID == 10) {
            return true;
        }
        return false;
    }

    public function createGroup(Group $group)
    {

        $this->table = "GROUPS";
        $groupID = $this->save(
            [
                "selectors" => [
                    "owner_id" => $group->getOwner_ID(),
                    "name" => $group->getName()
                ],
                "RETURN" => ["id"]
            ]
        )["id"];
        $group->setId($groupID);
        $this->addGroupToAccount($group->getOwner_ID(), $groupID);
        return $groupID;
    }

    public function addGroupToAccount(int $accountID, int $groupID)
    {
        $this->table = "ACCOUNT_TO_GROUPS";
        $this->save(
            [
                "selectors" => [
                    "account_id" => $accountID,
                    "group_id" => $groupID
                ]
            ]
        );
    }
    public function getGroups()
    {
        $this->table = "GROUPS";
        $groupsArray = $this->find();
        $groups = [];
        foreach ($groupsArray as $group) {
            $members = $this->getMembersByGroupID($group["id"]);
            $group["members"] = $members;
            $groups[] = new Group($group);
        }
        return $groups;
    }

    public function getGroupByID(int $id)
    {
        $this->table = "GROUPS";
        $group = $this->find(
            [
                "conditions" => [
                    "GROUPS.id  =" => $id
                ]
            ]
        );
        $group = new Group($group[0]);
        return $group;
    }

    public function getGroupsByAccountID(int $id)
    {
        $this->table = "GROUPS";
        $groupsArray = $this->find(
            [
                "selectors" => [
                    "GROUPS.*",
                ],
                "innerJoin" => [
                    [
                        "table" => "ACCOUNT_TO_GROUPS",
                        "compared" => "ACCOUNT_TO_GROUPS.group_id ",
                        "operator" => "=",
                        "referring" => "GROUPS.id"
                    ]
                ],
                "conditions" => [
                    "ACCOUNT_TO_GROUPS.account_id  =" => $id
                ]
            ]

        );

        $groups = [];
        foreach ($groupsArray as $group) {
            $members = $this->getMembersByGroupID($group["id"]);
            $group["members"] = $members;
            $groups[] = new Group($group);
        }
        return $groups;
    }



    public function deleteGroup(int $groupID)
    {
        $this->table = "GROUPS";
        $this->delete(
            [
                "conditions" => [
                    "GROUPS.id =" => $groupID
                ]
            ]
        );
    }

    public function addMembers(int $groupID, array $accounts)
    {
        $accountManager = new AccountManager();

        foreach ($accounts as $key => $id) {
           
            //check if user exist
            if ($accountManager->isExist($id)) {

                // add user to group
                $this->table = "ACCOUNT_TO_GROUPS";
                $this->save(
                    [
                        "selectors" => [
                            "account_id" => $id,
                            "group_id" => $groupID
                        ],
                        "RETURN" => ["group_id"]
                    ]
                );
            }
        }
    }

    public function getMembersByGroupID(int $groupID)
    {
        $this->table = "ACCOUNTS";
        $account = $this->find(
            [
                "selectors" => [
                    "ACCOUNTS.id",
                    "ACCOUNTS.last_name",
                    "ACCOUNTS.first_name"
                ],
                "innerJoin" => [
                    [
                        "table" => "ACCOUNT_TO_GROUPS",
                        "compared" => "ACCOUNT_TO_GROUPS.account_id ",
                        "operator" => "=",
                        "referring" => "ACCOUNTS.id"
                    ]
                ],
                "conditions" => [
                    "ACCOUNT_TO_GROUPS.group_id  =" => $groupID
                ]
            ]
        );

        $members = [];
        foreach ($account as $a) {
            $members[] = new Account($a);
        }
        return $members;
    }

    public function editGroup(Group $group)
    {

        $this->table = "GROUPS";
        $this->save(
            [
                "selectors" => [
                    "owner_id" => $group->getOwner_ID(),
                    "name" => $group->getName()
                ],
                "values" => [
                    "name" => $group->getName(),
                    "owner_id" => $group->getOwner_ID()
                ],
                "id" => $group->getId()
            ]
        );

        $members = $group->getMembers();
        //delete all members from group
        $this->table = "ACCOUNT_TO_GROUPS";
        $this->delete(
            [
                "conditions" => [
                    "group_id =" => $group->getId()
                ]
            ]
        );
        //add new members to group
        $this->addMembers($group->getId(), $members);
    }

    public function addGroupToAgenda(int $groupID, int $agendaID)
    {
        $this->table = "GROUP_TO_AGENDAS";
        $this->save(
            [
                "selectors" => [
                    "agenda_id" => $agendaID,
                    "group_id" => $groupID
                ]
            ]
        );
    }

}
