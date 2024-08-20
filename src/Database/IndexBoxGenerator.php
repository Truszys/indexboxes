<?php

namespace Module\IndexBoxes\Database;

use Doctrine\ORM\EntityManagerInterface;
use Module\IndexBoxes\Entity\IndexBox;
use Module\IndexBoxes\Entity\IndexBoxLang;
use Module\IndexBoxes\Repository\IndexBoxRepository;
use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Entity\Repository\LangRepository;
use Symfony\Component\Filesystem\Filesystem;

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
        $boxes = $this->boxRepository->findByShop(\Context::getContext()->shop->id);
        foreach ($boxes as $box) {
            $box->deleteImage();
            $this->entityManager->remove($box);
        }
        $this->entityManager->flush();
    }

    private function insertBoxes()
    {
        $context = \Context::getContext();
        $filesystem = new Filesystem();
        $languages = $this->langRepository->findAll();

        $boxesDataFile = __DIR__ . '/../../Resources/data/boxes.json';
        $boxesDataImage = __DIR__ . '/../../Resources/data/test.png';
        $boxesData = json_decode(file_get_contents($boxesDataFile), true);
        foreach ($boxesData as $boxData) {
            $box = new IndexBox();
            $box->setBoTitle($boxData['bo_title']);
            $box->setIdShop($context->shop->id);
            $box->setImage($boxData['image']);
            $box->setClasses($boxData['classes']);
            $box->setIcon($boxData['icon']);
            $box->setType($boxData['type']);
            $box->setItemId($boxData['item_id']);
            $box->setPosition($this->boxRepository->getMaxPosition($context->shop->id) + 1);
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
            $this->entityManager->flush();

            if($filesystem->exists($boxesDataImage)) {
                $filesystem->copy($boxesDataImage, IndexBox::$IMAGE_PATH . $box->getId() . '.png', true);
                $box->setImage(IndexBox::$IMAGE_PATH_FRONT . $box->getId() . '.png');
            }

            $this->entityManager->persist($box);
            $this->entityManager->flush();
        }
    }
}
