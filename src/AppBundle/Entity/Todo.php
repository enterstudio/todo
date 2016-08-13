<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Todo
 *
 * @ORM\Table(name="todo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TodoRepository")
 */
class Todo
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", unique=true, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true, nullable=false)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=255, unique=false, nullable=false)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, unique=false, nullable=false)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="priority", type="integer", length=2, unique=false, nullable=true, options={"default" : 10})
     */
    private $priority;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime", unique=false, nullable=false)
     */
    private $createDate;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetime")
     */
    private $updateDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="due_date", type="datetime", unique=false, nullable=false)
     */
    private $dueDate;

    /**
     * @var string
     *
     * @ORM\Column(name="completed", type="string", length=255,  unique=false, nullable=true, options={"default" : 0})
     */
    private $completed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="completed_date", type="datetime")
     */
    private $completedDate;


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
     * Set name
     *
     * @param string $name
     *
     * @return Todo
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set category
     *
     * @param string $category
     *
     * @return Todo
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
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
     * Set priority
     *
     * @param int(1) $priority
     *
     * @return Todo
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return Todo
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }
    
    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     *
     * @return Todo
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Set dueDate
     *
     * @param \DateTime $dueDate
     *
     * @return Todo
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Get dueDate
     *
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
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
     * Set completedDate
     *
     * @param \DateTime $completedDate
     *
     * @return Todo
     */
    public function setCompletedDate($completedDate)
    {
        $this->completedDate = $completedDate;

        return $this;
    }

    /**
     * Get completedDate
     *
     * @return \DateTime
     */
    public function getCompletedDate()
    {
        return $this->completedDate;
    }
}

