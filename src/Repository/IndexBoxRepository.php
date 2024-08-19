<?php

namespace Module\IndexBoxes\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class IndexBoxRepository extends EntityRepository
{
    public function findOneById(int $id_box)
    {
        return $this->findOneBy(['id_box' => $id_box]);
    }

    public function findById(array $boxIds)
    {
        return $this->findBy(['id_box' => $boxIds]);
    }

    public function getAllActive(int $idLang = 0, int $idShop = 0)
    {
        /** @var QueryBuilder $qb */
        $qb = $this
            ->createQueryBuilder('b')
            ->addSelect('bl')
            ->leftJoin('b.boxLangs', 'bl')
        ;

        if (0 === $idLang) {
            $idLang - \Context::getContext()->language->id;
        }

        if (0 === $idShop) {
            $idShop - \Context::getContext()->shop->id;
        }
        $qb
            ->andWhere('bl.lang = :idLang')
            ->setParameter('idLang', $idLang)
            ->andWhere('b.id_shop = :idShop')
            ->setParameter('idShop', $idShop)
            ->orderBy('b.position', 'ASC')
        ;

        $boxes = $qb->getQuery()->getResult();

        return $boxes;
    }

    

    public function getMaxPosition(int $id_shop = 0): int
    {
        /** @var QueryBuilder $qb */
        $qb = $this
            ->createQueryBuilder('b')
            ->select('MAX(b.position)')
        ;

        if (0 === $id_shop) {
            $id_shop - \Context::getContext()->shop->id;
        }

        $qb
            ->where('b.id_shop = :id_shop')
            ->setParameter('id_shop', $id_shop)
        ;

        dump($qb->getQuery());

        return (int)$qb->getQuery()->getResult();
    }

    public function movePositions(int $boxId)
    {
        $box = $this->findOneById($boxId);
        if($box === null)
            return;
        $this->createQueryBuilder('b')
            ->update()
            ->set('b.position', 'b.position-1')
            ->where('b.position > :postition')
            ->setParameter('postition', $box->getPosition())
            ->getQuery()->execute()
        ;
    }
}
