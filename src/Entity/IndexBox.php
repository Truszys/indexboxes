<?php

namespace Module\IndexBoxes\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Module\IndexBoxes\Repository\IndexBoxRepository")
 * @ORM\HasLifecycleCallbacks
 */
class IndexBox
{
    public static $MAIN_PATH = __DIR__ . '/../..';
    public static $IMAGE_PATH = __DIR__ . '/../../views/img/';


    public static $TYPES = [
        'product' => 'product',
        'category' => 'category',
        'CMS' => 'cms'
    ];

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_box", type="integer", columnDefinition="AUTO_INCREMENT")
     * @ORM\GeneratedValue()
     */
    private $id_box;

    
    /**
     * @var int
     *
     * @ORM\Column(name="id_shop", type="integer")
     */
    private $id_shop;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="bo_title", type="string", length=255)
     */
    private $bo_title;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=45)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=45)
     */
    private $icon;

    /**
     * @var int
     *
     * @ORM\Column(name="item_id", type="integer")
     */
    private $item_id;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="integer", length=1)
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity="Module\IndexBoxes\Entity\IndexBoxLang", cascade={"persist", "remove"}, mappedBy="box")
     */
    private $boxLangs;

    public function __construct()
    {
        $this->boxLangs = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id_box;
    }

    /**
     * @return int
     */
    public function getIdShop(): int
    {
        return $this->id_shop;
    }

    public function setIdShop(int $id_shop = 1)
    {
        $this->id_shop = $id_shop;

        return $this;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string
     */
    public function getBoTitle(): string
    {
        return $this->bo_title;
    }

    public function setBoTitle(string $bo_title)
    {
        $this->bo_title = $bo_title;

        return $this;
    }

    /**
     * @return string
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getItemId(): int
    {
        return $this->item_id;
    }

    public function setItemId(int $item_id)
    {
        $this->item_id = $item_id;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return (bool)$this->active;
    }

    public function setActive(bool $active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getBoxLangs()
    {
        return $this->boxLangs;
    }

    /**
     * @param int $langId
     * @return IndexBoxLang|null
     */
    public function getBoxLangByLangId(int $langId): ?IndexBoxLang
    {
        foreach ($this->boxLangs as $boxLang) {
            if ($langId === $boxLang->getLang()->getId()) {
                return $boxLang;
            }
        }

        return null;
    }

    /**
     * @param IndexBoxLang $boxLang
     * @return $this
     */
    public function addBoxLang(IndexBoxLang $boxLang): self
    {
        $boxLang->setBox($this);
        $this->boxLangs->add($boxLang);

        return $this;
    }

    /**
     * Delete box image
     * @return bool
     */
    public function deleteImage(): bool
    {
        $filesystem = new Filesystem();
        $image_path = realpath(self::$MAIN_PATH . '/../..' . $this->getImage());
        if($filesystem->exists($image_path)) {
            $filesystem->remove($image_path);
            return true;
        }
        return false;
    }
}