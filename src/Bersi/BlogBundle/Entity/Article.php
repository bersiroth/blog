<?php

namespace Bersi\BlogBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Article
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bersi\BlogBundle\Entity\ArticleRepository")
 */
class Article
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255,  unique=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="introduction", type="string", length=255)
     */
    private $introduction;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var boolean
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published = false;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="Bersi\BlogBundle\Entity\Category", cascade={"persist"})
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="Bersi\BlogBundle\Entity\Author", cascade={"persist"})
     */
    private $author;

    /**
     * @ORM\ManyToMany(targetEntity="Bersi\BlogBundle\Entity\Tag", cascade={"persist"}, inversedBy="articles")
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="Bersi\BlogBundle\Entity\Comment",  mappedBy="article")
     */
    private $comments;

    public function __construct()
    {
        $this->date = new \Datetime();
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
     * Set date
     *
     * @param \DateTime $date
     * @return news
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
     * Set title
     *
     * @param string $title
     * @return news
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
     * Set content
     *
     * @param string $content
     * @return news
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
     * Set published
     *
     * @param boolean $published
     * @return news
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Article
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }


    /**
     * Set categories
     *
     * @param \Bersi\BlogBundle\Entity\Category $category
     * @return Article
     */
    public function setCategory(\Bersi\BlogBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get categories
     *
     * @return \Bersi\BlogBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set author
     *
     * @param \Bersi\BlogBundle\Entity\Author $author
     * @return Article
     */
    public function setAuthor(\Bersi\BlogBundle\Entity\Author $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \Bersi\BlogBundle\Entity\Author
     */
    public function getAuthor()
    {
        return $this->author;
    }


    /**
     * Add tags
     *
     * @param \Bersi\BlogBundle\Entity\Tag $tags
     * @return Article
     */
    public function addTag(\Bersi\BlogBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \Bersi\BlogBundle\Entity\Tag $tags
     */
    public function removeTag(\Bersi\BlogBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set introduction
     *
     * @param string $introduction
     * @return Article
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;

        return $this;
    }

    /**
     * Get introduction
     *
     * @return string
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }


    /**
     * Add comments
     *
     * @param \Bersi\BlogBundle\Entity\Comment $comments
     * @return Article
     */
    public function addComment(\Bersi\BlogBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;
        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Bersi\BlogBundle\Entity\Comment $comments
     */
    public function removeComment(\Bersi\BlogBundle\Entity\Comment $comments)
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

    public function getCountPublishComment()
    {
        $publishComment = array();
        foreach($this->comments as $comment){
            if($comment->getPublished()){
                $publishComment[] = $comment;
            }
        }
        return count($publishComment);
    }
}
