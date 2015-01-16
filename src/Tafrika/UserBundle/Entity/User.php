<?php

namespace Tafrika\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Tafrika\UserBundle\Entity\UserRepository")
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
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="followed")
     */
    private $followers;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="followers")
     * @ORM\JoinTable(name="subscribed",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="followed_id", referencedColumnName="id")}
     *      )
     */
    private $followed;

    public function __construct()
    {
        parent::__construct();
        $this->addRole("ROLE_USER");
        $this->followed = new \Doctrine\Common\Collections\ArrayCollection();
        $this->followers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add followers
     *
     * @param \Tafrika\UserBundle\Entity\User $followers
     * @return User
     */
    public function addfollowers($followers)
    {
        $this->followers->add($followers);

        return $this;
    }

    /**
     * Remove followers
     *
     * @param \Tafrika\UserBundle\Entity\User $followers
     */
    public function removefollowers($followers)
    {
        $this->followers->removeElement($followers);
    }

    /**
     * Get followers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getfollowers()
    {
        return $this->followers;
    }

    /**
     * Add followed
     *
     * @param \Tafrika\UserBundle\Entity\User $followed
     * @return User
     */
    public function addfollowed($followed)
    {
        if( !$this->isFollowed($followed)) {
            $this->followed->add($followed);
            $followed->addfollowers($this);
        }
        return $this;
    }

    /**
     * Remove followed
     *
     * @param \Tafrika\UserBundle\Entity\User $followed
     */
    public function removefollowed($followed)
    {
        $this->followed->removeElement($followed);
        $followed->removefollowers($this);
    }

    /**
     * Get followed
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getfollowed()
    {
        return $this->followed;
    }

    public function isFollowed($user){
        return $this->getfollowed()->contains($user);
    }
}
