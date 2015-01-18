<?php

namespace Tafrika\PostBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Tafrika\PostBundle\Entity\CommentRepository")
 */
class Comment
{
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
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     *
     * @var type Post
     * @ORM\ManyToOne(targetEntity="Tafrika\PostBundle\Entity\Post", inversedBy="comments")
     * @ORM\JoinColumn(name="post_id",referencedColumnName="id")
     */
    private $post;

    /**
     *
     * @var type User
     * @ORM\ManyToOne(targetEntity="Tafrika\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id",referencedColumnName="id")
     */
    private $user;

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
     * Set content
     *
     * @param string $content
     * @return Comment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set post
     *
     * @param \Tafrika\PostBundle\Entity\Post $post
     * @return Comment
     */
    public function setPost($post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \Tafrika\PostBundle\Entity\Post 
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set user
     *
     * @param \Tafrika\UserBundle\Entity\User $user
     * @return Comment
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
}
