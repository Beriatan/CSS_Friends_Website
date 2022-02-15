<?php

class Friendship
{
    protected $id, $friend1, $friend2, $relationship;



    public function __construct($db_row)
    {
        $this->id = $db_row['relationshipID'];
        $this->friend1 = $db_row['friend1'];
        $this->friend2 = $db_row['friend2'];
        $this->relationship = $db_row['relationship'];
    }


    /**
     * @return mixed
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFriend1(): mixed
    {
        return $this->friend1;
    }

    /**
     * @return mixed
     */
    public function getFriend2(): mixed
    {
        return $this->friend2;
    }

    /**
     * @return mixed
     */
    public function getRelationship(): mixed
    {
        return $this->relationship;
    }
}