<?php

namespace AbsenceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AbsenceUserBundle\Entity\User;

/**
 * Absence
 *
 * @ORM\Table(name="absence")
 * @ORM\Entity(repositoryClass="AbsenceBundle\Repository\AbsenceRepository")
 */
class Absence
{

    /**
     * @ORM\ManyToOne(targetEntity="\AbsenceUserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;


     public function setUser(User $user)
     {
         $this->user = $user;

         return $this;
     }

     public function getUser()
     {
         return $this->user;
     }

     /**
      * @var int
      *
      * @ORM\Column(name="id", type="integer")
      * @ORM\Id
      * @ORM\GeneratedValue(strategy="AUTO")
      */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="id_lesson", type="integer")
     */
    private $idLesson;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string", length=255)
     */
    private $reason;

    /**
     * @var bool
     *
     * @ORM\Column(name="justify", type="boolean")
     */
    private $justify;


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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Absence
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set idLesson
     *
     * @param integer $idLesson
     *
     * @return Absence
     */
    public function setIdLesson($idLesson)
    {
        $this->idLesson = $idLesson;

        return $this;
    }

    /**
     * Get idLesson
     *
     * @return int
     */
    public function getIdLesson()
    {
        return $this->idLesson;
    }

    /**
     * Set reason
     *
     * @param string $reason
     *
     * @return Absence
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set justify
     *
     * @param boolean $justify
     *
     * @return Absence
     */
    public function setJustify($justify)
    {
        $this->justify = $justify;

        return $this;
    }

    /**
     * Get justify
     *
     * @return bool
     */
    public function getJustify()
    {
        return $this->justify;
    }
}

