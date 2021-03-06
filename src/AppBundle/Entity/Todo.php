<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Todo
 *
 * @ORM\Table(name="todo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TodoRepository")
 */
class Todo implements \Serializable 
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", unique=true, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="session_id", type="string", length=255, unique=false, nullable=false)
     */
    private $sessionId;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, unique=false, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="completed", type="string", length=255,  unique=false, nullable=true, options={"default" : 0})
     */
    private $completed;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Todo
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set completed
     *
     * @param string $completed
     *
     * @return Todo
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;

        return $this;
    }

    /**
     * Get completed
     *
     * @return string
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * Get sessionId
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }
    
    /**
     * Set sessionId
     *
     * @param string $sessionId
     *
     * @return Todo
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->description
        ]);
    }

    public function unserialize($data)
    {
        list($this->id, $this->description) = unserialize($data);
    }
}
