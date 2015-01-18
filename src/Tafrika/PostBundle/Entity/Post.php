<?php

namespace Tafrika\PostBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Post
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Tafrika\PostBundle\Entity\PostRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *                  "image" = "Image",
 *                  "video" = "Video",
 *                  "status" = "Status"})
 */
class Post
{
    const TYPE = 'POST';


    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\Length(min=5, max=255,
     *              minMessage="يا معلم تيتر لازمو فوق 5 حروف",
     *              maxMessage="يا معلم تيتر لازمو أقل من 255 حرف")
     *
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Assert\DateTime()
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="likes", type="integer")
     * @Assert\EqualTo( value = 0)
     */
    private $likes;

    /**
     *
     * @var type User
     * @ORM\ManyToOne(targetEntity="Tafrika\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id",referencedColumnName="id")
     * @Assert\Valid()
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Tafrika\PostBundle\Entity\Comment", mappedBy="post")
     */
    private $comments;

    public function __construct() {
        $this->likes = 0;
        $this->createdAt = new \DateTime('now',new \DateTimeZone("UTC"));
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
     * Set title
     *
     * @param string $title
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Post
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set likes
     *
     * @param integer $likes
     * @return Post
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;

        return $this;
    }

    /**
     * Get likes
     *
     * @return integer 
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Set user
     *
     * @param \Tafrika\UserBundle\Entity\User $user
     * @return Post
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Tafrika\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    public static function get_type()
    {
        $c = get_called_class();
        return $c::TYPE;
    }

    /**
     * Add comments
     *
     * @param \Tafrika\PostBundle\Entity\Comment $comments
     * @return Post
     */
    public function addComment($comments)
    {
        $this->comments->add($comments);

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Tafrika\PostBundle\Entity\Comment $comments
     */
    public function removeComment($comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }
}
