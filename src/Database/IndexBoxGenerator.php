<?php

namespace Module\IndexBoxes\Database;

use Doctrine\ORM\EntityManagerInterface;
use Module\IndexBoxes\Entity\IndexBox;
use Module\IndexBoxes\Entity\IndexBoxLang;
use Module\IndexBoxes\Repository\IndexBoxRepository;
use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Entity\Repository\LangRepository;

class IndexBoxGenerator
{
    /**
     * @var BoxRepository
     */
    private $boxRepository;

    /**
     * @var LangRepository
     */
    private $langRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param IndexBoxRepository $boxRepository
     * @param LangRepository $langRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        IndexBoxRepository $boxRepository,
        LangRepository $langRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->boxRepository = $boxRepository;
        $this->langRepository = $langRepository;
        $this->entityManager = $entityManager;
    }

    public function generateBoxes()
    {
        $this->removeAllBoxes();
        $this->insertBoxes();
    }

    private function removeAllBoxes()
    {
        $boxes = $this->boxRepository->findAll();
        foreach ($boxes as $box) {
            $this->entityManager->remove($box);
        }
        $this->entityManager->flush();
    }

    private function insertBoxes()
    {
        $languages = $this->langRepository->findAll();

        $boxesDataFile = __DIR__ . '/../../Resources/data/boxes.json';
        $boxesData = json_decode(file_get_contents($boxesDataFile), true);
        foreach ($boxesData as $boxData) {
            $box = new IndexBox();
            $box->setBoTitle($boxData['bo_title']);
            $box->setIdShop($boxData['id_shop']);
            $box->setImage($boxData['image']);
            $box->setType($boxData['type']);
            $box->setItemId($boxData['item_id']);
            $box->setPosition($boxData['position']);
            $box->setActive($boxData['active']);
            /** @var Lang $language */
            foreach ($languages as $language) {
                $boxLang = new IndexBoxLang();
                $boxLang->setLang($language);
                if (isset($boxData['title'][$language->getIsoCode()])) {
                    $boxLang->setTitle($boxData['title'][$language->getIsoCode()]);
                } else {
                    $boxLang->setTitle($boxData['title']['en']);
                }
                $box->addBoxLang($boxLang);
            }
            $this->entityManager->persist($box);
        }

        $this->entityManager->flush();
    }
}
