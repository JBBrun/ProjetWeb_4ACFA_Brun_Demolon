<?php

namespace AbsenceUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="year", type="string", length=255, nullable=true)
     */
    private $year;
    /**
     * Set year
     *
     * @param string $year
     *
     * @return User
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get rear
     *
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}

