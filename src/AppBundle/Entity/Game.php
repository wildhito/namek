<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Game
 *
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GameRepository")
 */
class Game
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="shortname", type="string", length=255, unique=true)
     */
    private $shortname;

    /**
     * @var string
     *
     * @ORM\Column(name="fullname", type="string", length=255, unique=true)
     */
    private $fullname;

    /**
     * @var string
     *
     * @ORM\Column(name="brief", type="text", nullable=true)
     */
    private $brief;

    /**
     * @var string
     *
     * @ORM\Column(name="review", type="text", nullable=true)
     */
    private $review;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modifiedAt", type="datetime")
     */
    private $modifiedAt;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumn(name="modifier_id", referencedColumnName="id")
     */
    private $modifiedBy;

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
     * Set shortname
     *
     * @param string $shortname
     *
     * @return Game
     */
    public function setShortname($shortname)
    {
        $this->shortname = $shortname;

        return $this;
    }

    /**
     * Get shortname
     *
     * @return string
     */
    public function getShortname()
    {
        return $this->shortname;
    }

    /**
     * Set fullname
     *
     * @param string $fullname
     *
     * @return Game
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set brief
     *
     * @param string $brief
     *
     * @return Game
     */
    public function setBrief($brief)
    {
        $this->brief = $brief;

        return $this;
    }

    /**
     * Get brief
     *
     * @return string
     */
    public function getBrief()
    {
        return $this->brief;
    }

    /**
     * Set review
     *
     * @param string $review
     *
     * @return Game
     */
    public function setReview($review)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * Get review
     *
     * @return string
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Game
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
     * Set modifiedAt
     *
     * @param \DateTime $modifiedAt
     *
     * @return Game
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * Get modifiedAt
     *
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Set modifiedBy
     *
     * @param \AppBundle\Entity\Player $modifiedBy
     *
     * @return Game
     */
    public function setModifiedBy(\AppBundle\Entity\Player $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Get modifiedBy
     *
     * @return \AppBundle\Entity\Player
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * Set createdBy
     *
     * @param \AppBundle\Entity\Player $createdBy
     *
     * @return Game
     */
    public function setCreatedBy(\AppBundle\Entity\Player $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \AppBundle\Entity\Player
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function getLogoName()
    {
        return "logo.png";
    }

    public function getLogoMimeType()
    {
        return "image/png";
    }

    public function getPictureMimeTypes()
    {
        return array("image/jpeg", "image/png");
    }

    public function getLogoDir()
    {
        return sprintf("games/%d", $this->id);
    }

    public function getPictureDir()
    {
        return sprintf("%s/pictures", $this->getLogoDir());
    }

    public function getPictureWebDir()
    {
        return sprintf("/%s", $this->getPictureDir());
    }

    public function getLogoPath()
    {
        $logopath = sprintf("%s/%s", $this->getLogoDir(), $this->getLogoName());
        $fs = new Filesystem();
        if (!$fs->exists($logopath)) {
            return "images/default_game.png";
        }
        return $logopath;
    }

    public function getLogoWebPath()
    {
        return sprintf("/%s", $this->getLogoPath());
    }
}
