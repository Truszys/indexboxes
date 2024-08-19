<?php

namespace Module\IndexBoxes\Entity;

use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Entity\Lang;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class IndexBoxLang
{
    /**
     * @var IndexBox
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Module\IndexBoxes\Entity\IndexBox", inversedBy="boxLangs")
     * @ORM\JoinColumn(name="id_box", referencedColumnName="id_box", nullable=false, onDelete="CASCADE")
     */
    private $box;

    /**
     * @var Lang
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Lang")
     * @ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", nullable=false, onDelete="CASCADE")
     */
    private $lang;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @return IndexBox
     */
    public function getBox()
    {
        return $this->box;
    }

    /**
     * @param IndexBox $box
     * @return $this
     */
    public function setBox(IndexBox $box)
    {
        $this->box = $box;

        return $this;
    }

    /**
     * @return Lang
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param Lang $lang
     * @return $this
     */
    public function setLang(Lang $lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }
}