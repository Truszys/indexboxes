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

        $boxData = [
            'bo_title' => $box->getBoTitle(),
            'id_shop' => $box->getIdShop(),
            'image' => $box->getImage(),
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
            'type' => 'category',
            'item_id' => '2',
            'position' => 1,
            'active' => true,
            'title' => [],
        ];
    }
}
