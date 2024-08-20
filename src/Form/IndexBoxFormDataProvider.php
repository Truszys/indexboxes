<?php

namespace Module\IndexBoxes\Form;

use Module\IndexBoxes\Entity\IndexBox;
use Module\IndexBoxes\Entity\IndexBoxLang;
use Module\IndexBoxes\Repository\IndexBoxRepository;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;

class IndexBoxFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var IndexBoxRepository
     */
    private $repository;

    /**
     * @param IndexBoxRepository $repository
     */
    public function __construct(IndexBoxRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($boxId)
    {
        $box = $this->repository->findOneById($boxId);

        $classes = $box->getClasses();
        $boxData = [
            'id_box' => $box->getId(),
            'bo_title' => $box->getBoTitle(),
            'id_shop' => $box->getIdShop(),
            'image' => $box->getImage(),
            'col_xl' => $classes['col_xl'],
            'col_lg' => $classes['col_lg'],
            'col_md' => $classes['col_md'],
            'col_sm' => $classes['col_sm'],
            'col_xs' => $classes['col_xs'],
            'custom_classes' => $classes['custom_classes'],
            'icon' => $box->getIcon(),
            'type' => $box->getType(),
            'item_id' => $box->getItemId(),
            'position' => $box->getPosition(),
            'active' => $box->isActive(),
        ];
        foreach ($box->getBoxLangs() as $boxLang) {
            $boxData['title'][$boxLang->getLang()->getId()] = $boxLang->getTitle();
        }

        return $boxData;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'bo_title' => '',
            'id_shop' => 1,
            'image' => '',
            'col_xl' => 12,
            'col_lg' => 12,
            'col_md' => 12,
            'col_sm' => 12,
            'col_xs' => 12,
            'custom_classes' => '',
            'type' => 'category',
            'item_id' => '2',
            'position' => 1,
            'active' => true,
            'title' => [],
        ];
    }
}
