<?php

namespace Module\IndexBoxes\Form;

use Doctrine\ORM\EntityManagerInterface;
use Module\IndexBoxes\Entity\IndexBox;
use Module\IndexBoxes\Entity\IndexBoxLang;
use Module\IndexBoxes\Repository\IndexBoxRepository;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShopBundle\Entity\Repository\LangRepository;
use Symfony\Component\Filesystem\Filesystem;

class IndexBoxFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var IndexBoxRepository
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

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        $context = \Context::getContext();
        $box = new IndexBox();
        $box->setBoTitle($data['bo_title']);
        $box->setIdShop($context->shop->id);
        $box->setClasses([
            'col_xl' => $data['col_xl'],
            'col_lg' => $data['col_lg'],
            'col_md' => $data['col_md'],
            'col_sm' => $data['col_sm'],
            'col_xs' => $data['col_xs'],
            'custom_classes' => $data['custom_classes'],
        ]);
        $box->setIcon($data['icon']);
        $box->setImage('');
        $box->setType($data['type']);
        $box->setItemId($data[$data['type'] . '_id']);
        $box->setPosition($position = $this->boxRepository->getMaxPosition($context->shop->id) + 1);
        $box->setActive($data['active']);
        $defLang = null;
        foreach ($data['title'] as $langId => $langTitle) {
            $lang = $this->langRepository->findOneById($langId);
            $boxLang = new IndexBoxLang();
            if($langTitle === null) {
                if($defLang === null) {
                    $defLang = \Configuration::get('PS_LANG_DEFAULT', null, $context->shop->id_shop_group, $context->shop->id);
                }
                $boxLang
                    ->setLang($lang)
                    ->setTitle($data['title'][$defLang])
                ;
            } else {
                $boxLang
                    ->setLang($lang)
                    ->setTitle($langTitle)
                ;
            }
            $box->addBoxLang($boxLang);
        }
        $this->entityManager->persist($box);
        $this->entityManager->flush();
        $filesystem = new Filesystem();
        if($filesystem->exists($data['new_image'])) {
            $ext = $data['new_image']->guessExtension();
            $filesystem->rename($data['new_image'], IndexBox::$IMAGE_PATH . $box->getId() . '.' . $ext, true);
            $box->setImage(IndexBox::$IMAGE_PATH_FRONT . $box->getId() . '.' . $ext);
            $this->entityManager->persist($box);
            $this->entityManager->flush();
            return $box->getId();
        } else {
            $this->entityManager->remove($box);
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $box = $this->boxRepository->findOneById($id);
        $box->setBoTitle($data['bo_title']);
        $box->setIcon($data['icon']);
        $box->setClasses([
            'col_xl' => $data['col_xl'],
            'col_lg' => $data['col_lg'],
            'col_md' => $data['col_md'],
            'col_sm' => $data['col_sm'],
            'col_xs' => $data['col_xs'],
            'custom_classes' => $data['custom_classes'],
        ]);
        $box->setType($data['type']);
        $box->setItemId($data[$data['type'] . '_id']);
        $box->setActive($data['active']);
        foreach ($data['title'] as $langId => $title) {
            $boxLang = $box->getBoxLangByLangId($langId);
            if (null === $boxLang) {
                continue;
            }
            $boxLang->setTitle($title);
        }
        if($data['new_image']) {
            $filesystem = new Filesystem();
            if(!$filesystem->exists($data['new_image'])) {
                return false;
            }
            $ext = $data['new_image']->guessExtension();
            $filesystem->rename($data['new_image'], IndexBox::$IMAGE_PATH . $id . '.' . $ext, true);
            $box->setImage(IndexBox::$IMAGE_PATH_FRONT . $id . '.' . $ext);
        } else {
            $box->setImage($data['image']);
        }
        $this->entityManager->persist($box);
        $this->entityManager->flush();
        return $box->getId();
    }
}
