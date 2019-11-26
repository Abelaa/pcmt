<?php

declare(strict_types=1);

namespace Pcmt\PcmtProductBundle\Tests\Service;

use Akeneo\Pim\Enrichment\Component\Product\Model\ProductModel;
use Akeneo\Tool\Component\StorageUtils\Saver\SaverInterface;
use Akeneo\UserManagement\Component\Model\User;
use Akeneo\UserManagement\Component\Model\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Pcmt\PcmtProductBundle\Entity\AbstractDraft;
use Pcmt\PcmtProductBundle\Entity\NewProductModelDraft;
use Pcmt\PcmtProductBundle\Service\ProductModelDraftApprover;
use Pcmt\PcmtProductBundle\Service\ProductModelFromDraftCreator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductModelDraftApproverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testApprove(): void
    {
        $creator = $this->createMock(ProductModelFromDraftCreator::class);
        $creator->expects($this->once())->method('getProductModelToSave')->willReturn(new ProductModel());
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $user = $this->createMock(UserInterface::class);
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn($token);

        $validator = $this->createMock(ValidatorInterface::class);

        $violations = $this->createMock(ConstraintViolationListInterface::class);
        $violations->method('count')->willReturn(0);
        $validator->method('validate')->willReturn($violations);

        $productSaver = $this->createMock(SaverInterface::class);

        $service = new ProductModelDraftApprover($entityManager, $tokenStorage);
        $service->setCreator($creator);
        $service->setSaver($productSaver);
        $service->setValidator($validator);

        $attribute1 = 'attribute1';
        $productData = [
            $attribute1  => 'NEW',
            'attribute2' => 123,
        ];
        $author = new User();
        $author->setFirstName('Alfred');
        $author->setLastName('Nobel');
        $created = new \DateTime();
        $draft = new NewProductModelDraft($productData, $author, $created, AbstractDraft::STATUS_NEW);

        $service->approve($draft);

        $this->assertSame(AbstractDraft::STATUS_APPROVED, $draft->getStatus());
    }
}