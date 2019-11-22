<?php

declare(strict_types=1);

namespace Pcmt\PcmtFamilyBundle\Repository;

use Akeneo\Pim\Structure\Bundle\Doctrine\ORM\Repository\FamilyVariantRepository;
use Akeneo\Pim\Structure\Component\Model\FamilyVariantInterface;

class PcmtFamilyVariantRepository extends FamilyVariantRepository
{
    public function getFamilyByFamilyVariant(FamilyVariantInterface $familyVariant): ?array
    {
        $qb = $this->createQueryBuilder('fv');
        $qb->select('f.id')
            ->join('fv.family', 'f')
            ->where(
                $qb->expr()->eq(
                    'fv', ':familyVariant'
                )
            );
        $qb->setParameters([
            ':familyVariant' => $familyVariant,
        ]);

        return $qb->getQuery()->getSingleResult();
    }
}