<?php

class User
{
    protected $id;
    protected $repositories = [];

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getRepositories()
    {
        return $this->repositories;
    }

    /**
     * @param mixed $repositories
     */
    public function setRepositories($repositories)
    {
        $this->repositories = $repositories;
    }
}