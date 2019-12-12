<?php

declare(strict_types=1);

namespace PcmtCoreBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use PcmtCoreBundle\Entity\AbstractDraft;
use PcmtCoreBundle\Entity\ProductModelDraftInterface;
use PcmtCoreBundle\Exception\DraftViolationException;
use PcmtCoreBundle\Normalizer\ProductDraftNormalizer;
use PcmtCoreBundle\Normalizer\ProductModelDraftNormalizer;
use PcmtCoreBundle\Service\Builder\PaginatedResponseBuilder;
use PcmtCoreBundle\Service\Draft\DraftFacade;
use PcmtCoreBundle\Service\Draft\DraftStatusListService;
use PcmtCoreBundle\Service\Draft\DraftStatusTranslatorService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

class PcmtDraftController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ProductDraftNormalizer */
    private $productDraftNormalizer;

    /** @var ProductModelDraftNormalizer */
    private $productModelDraftNormalizer;

    /** @var DraftStatusTranslatorService */
    private $draftStatusTranslatorService;

    /** @var DraftStatusListService */
    private $draftStatusListService;

    /** @var DraftFacade */
    private $draftFacade;

    /** @var NormalizerInterface */
    protected $constraintViolationNormalizer;

    /** @var PaginatedResponseBuilder */
    protected $responseBuilder;

    public function __construct(
        EntityManagerInterface $entityManager,
        DraftStatusTranslatorService $draftStatusTranslatorService,
        DraftStatusListService $draftStatusListService,
        DraftFacade $draftFacade,
        NormalizerInterface $constraintViolationNormalizer,
        PaginatedResponseBuilder $responseBuilder
    ) {
        $this->entityManager = $entityManager;
        $this->draftStatusTranslatorService = $draftStatusTranslatorService;
        $this->draftStatusListService = $draftStatusListService;
        $this->draftFacade = $draftFacade;
        $this->constraintViolationNormalizer = $constraintViolationNormalizer;
        $this->responseBuilder = $responseBuilder;
    }

    /**
     * @AclAncestor("pcmt_permission_drafts_list")
     */
    public function getList(Request $request): JsonResponse
    {
        $criteria = [
            'status' => $request->query->get('status') ?? AbstractDraft::STATUS_NEW,
        ];

        $draftRepository = $this->entityManager->getRepository(AbstractDraft::class);

        $page = $request->query->get('page') ?? PaginatedResponseBuilder::FIRST_PAGE;
        $drafts = $draftRepository->findBy(
            $criteria,
            null,
            PaginatedResponseBuilder::PER_PAGE,
            ($page * PaginatedResponseBuilder::PER_PAGE) - PaginatedResponseBuilder::PER_PAGE
        );
        $total = $draftRepository->count($criteria);

        return $this->responseBuilder->buildPaginatedResponse($drafts, $total, (int) $page);
    }

    /**
     * @AclAncestor("pcmt_permission_drafts_list")
     */
    public function getDraft(AbstractDraft $draft): Response
    {
        if (!$draft) {
            throw new NotFoundHttpException('The draft does not exist');
        }

        $serializer = new Serializer([$this->productDraftNormalizer, $this->productModelDraftNormalizer]);
        $data = $serializer->normalize($draft, null, ['include_product' => true]);

        return new JsonResponse($data);
    }

    /**
     * @AclAncestor("pcmt_permission_drafts_edit")
     */
    public function updateDraft(AbstractDraft $draft, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['product'])) {
            throw new BadRequestHttpException('There is no product values');
        }

        if ($draft instanceof ProductModelDraftInterface && isset($data['product']['family'])) {
            unset($data['product']['family']);
        }

        $draft->setProductData($data['product']);

        $this->draftFacade->updateDraft($draft);

        $serializer = new Serializer([$this->productDraftNormalizer, $this->productModelDraftNormalizer]);
        $data = $serializer->normalize($draft, null, ['include_product' => true]);

        return new JsonResponse($data);
    }

    /**
     * @AclAncestor("pcmt_permission_drafts_list")
     */
    public function getListParams(): JsonResponse
    {
        $statuses = [];
        $ids = $this->draftStatusListService->getAll();
        foreach ($ids as $id) {
            $statuses[] = [
                'id'   => $id,
                'name' => $this->draftStatusTranslatorService->getNameTranslated($id),
            ];
        }
        $data = [
            'statuses' => $statuses,
        ];

        return new JsonResponse($data);
    }

    /**
     * @AclAncestor("pcmt_permission_drafts_reject")
     */
    public function rejectDraft(AbstractDraft $draft): JsonResponse
    {
        if (!$draft) {
            throw new NotFoundHttpException('The draft does not exist');
        }
        if (AbstractDraft::STATUS_NEW !== $draft->getStatus()) {
            throw new BadRequestHttpException("You can only reject draft of status 'new'");
        }
        $this->draftFacade->rejectDraft($draft);

        return new JsonResponse();
    }

    /**
     * @AclAncestor("pcmt_permission_drafts_approve")
     */
    public function approveDraft(AbstractDraft $draft): JsonResponse
    {
        if (!$draft) {
            throw new NotFoundHttpException('The draft does not exist');
        }
        if (AbstractDraft::STATUS_NEW !== $draft->getStatus()) {
            throw new BadRequestHttpException("You can only approve draft of status 'new'");
        }

        try {
            $this->draftFacade->approveDraft($draft);
        } catch (DraftViolationException $e) {
            $normalizedViolations = [];
            $context = [];
            if ($e->getProduct()) {
                $context['product'] = $e->getProduct();
            }
            if ($e->getProductModel()) {
                $context['productModel'] = $e->getProductModel();
            }
            foreach ($e->getViolations() as $violation) {
                $normalizedViolations[] = $this->constraintViolationNormalizer->normalize(
                    $violation,
                    'internal_api',
                    $context
                );
            }

            return new JsonResponse(['values' => $normalizedViolations], 400);
        }

        return new JsonResponse();
    }

    /**
     * @AclAncestor("pcmt_permission_drafts_approve")
     */
    public function approveBulkDraft(Request $request): JsonResponse
    {
        $draftRepository = $this->entityManager->getRepository(AbstractDraft::class);
        $chosenDrafts = json_decode($request->getContent(), true)['chosenDrafts'];

        $normalizedViolations = [];
        foreach ($chosenDrafts as $draftId) {
            $draft = $draftRepository->find($draftId);
            try {
                $this->draftFacade->approveDraft($draft);
            } catch (DraftViolationException $e) {
                $context = [];
                if ($e->getProduct()) {
                    $context['product'] = $e->getProduct();
                }
                if ($e->getProductModel()) {
                    $context['productModel'] = $e->getProductModel();
                }
                foreach ($e->getViolations() as $violation) {
                    $normalizedViolations[] = $this->constraintViolationNormalizer->normalize(
                        $violation,
                        'internal_api',
                        $context
                    );
                }
            }
        }
        if ($normalizedViolations) {
            return new JsonResponse(['values' => $normalizedViolations], 400);
        }

        return new JsonResponse();
    }
}