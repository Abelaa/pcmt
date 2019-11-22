<?php

declare(strict_types=1);

namespace Pcmt\PcmtProductModelBundle\Controller;

use Akeneo\Pim\Enrichment\Bundle\Filter\ObjectFilterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Akeneo\Pim\Enrichment\Component\Product\Comparator\Filter\EntityWithValuesFilter;
use Akeneo\Pim\Enrichment\Component\Product\Converter\ConverterInterface;
use Akeneo\Pim\Enrichment\Component\Product\Localization\Localizer\AttributeConverterInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductModelInterface;
use Akeneo\Pim\Enrichment\Component\Product\ProductModel\Filter\AttributeFilterInterface;
use Akeneo\Pim\Enrichment\Component\Product\Repository\ProductModelRepositoryInterface;
use Akeneo\Pim\Structure\Component\Model\FamilyVariantInterface;
use Akeneo\Pim\Structure\Component\Repository\FamilyVariantRepositoryInterface;
use Akeneo\Tool\Bundle\ElasticsearchBundle\Client;
use Akeneo\Tool\Component\StorageUtils\Factory\SimpleFactoryInterface;
use Akeneo\Tool\Component\StorageUtils\Remover\RemoverInterface;
use Akeneo\Tool\Component\StorageUtils\Saver\SaverInterface;
use Akeneo\Tool\Component\StorageUtils\Updater\ObjectUpdaterInterface;
use Akeneo\UserManagement\Bundle\Context\UserContext;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Pcmt\PcmtProductBundle\Event\ProductModelFetchEvent;

class PcmtProductModelController
{
    protected const PRODUCT_MODELS_LIMIT = 20;

    /** @var EventDispatcherInterface $eventDispatcher */
    protected $eventDispatcher;

    /** @var NormalizerInterface */
    protected $normalizer;

    /** @var UserContext */
    protected $userContext;

    /** @var ObjectFilterInterface */
    protected $objectFilter;

    /** @var ProductModelRepositoryInterface */
    protected $productModelRepository;

    /** @var AttributeConverterInterface */
    protected $localizedConverter;

    /** @var EntityWithValuesFilter */
    protected $emptyValuesFilter;

    /** @var ConverterInterface */
    protected $productValueConverter;

    /** @var ObjectUpdaterInterface */
    protected $productModelUpdater;

    /** @var RemoverInterface */
    protected $productModelRemover;

    /** @var ValidatorInterface */
    protected $validator;

    /** @var SaverInterface */
    protected $productModelSaver;

    /** @var NormalizerInterface */
    protected $constraintViolationNormalizer;

    /** @var NormalizerInterface */
    protected $entityWithFamilyVariantNormalizer;

    /** @var SimpleFactoryInterface */
    protected $productModelFactory;

    /** @var NormalizerInterface */
    protected $violationNormalizer;

    /** @var FamilyVariantRepositoryInterface */
    protected $familyVariantRepository;

    /** @var AttributeFilterInterface */
    protected $productModelAttributeFilter;

    /** @var Client */
    protected $productModelClient;

    /** @var Client */
    protected $productAndProductModelClient;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ProductModelRepositoryInterface $productModelRepository,
        NormalizerInterface $normalizer,
        UserContext $userContext,
        ObjectFilterInterface $objectFilter,
        AttributeConverterInterface $localizedConverter,
        EntityWithValuesFilter $emptyValuesFilter,
        ConverterInterface $productValueConverter,
        ObjectUpdaterInterface $productModelUpdater,
        RemoverInterface $productModelRemover,
        ValidatorInterface $validator,
        SaverInterface $productModelSaver,
        NormalizerInterface $constraintViolationNormalizer,
        NormalizerInterface $entityWithFamilyVariantNormalizer,
        SimpleFactoryInterface $productModelFactory,
        NormalizerInterface $violationNormalizer,
        FamilyVariantRepositoryInterface $familyVariantRepository,
        AttributeFilterInterface $productModelAttributeFilter,
        Client $productModelClient = null,
        Client $productAndProductModelClient = null
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->productModelRepository = $productModelRepository;
        $this->normalizer = $normalizer;
        $this->userContext = $userContext;
        $this->objectFilter = $objectFilter;
        $this->localizedConverter = $localizedConverter;
        $this->emptyValuesFilter = $emptyValuesFilter;
        $this->productValueConverter = $productValueConverter;
        $this->productModelUpdater = $productModelUpdater;
        $this->productModelRemover = $productModelRemover;
        $this->validator = $validator;
        $this->productModelSaver = $productModelSaver;
        $this->constraintViolationNormalizer = $constraintViolationNormalizer;
        $this->entityWithFamilyVariantNormalizer = $entityWithFamilyVariantNormalizer;
        $this->productModelFactory = $productModelFactory;
        $this->violationNormalizer = $violationNormalizer;
        $this->familyVariantRepository = $familyVariantRepository;
        $this->productModelAttributeFilter = $productModelAttributeFilter;
        $this->productModelClient = $productModelClient;
        $this->productAndProductModelClient = $productAndProductModelClient;
    }

    public function getAction(int $id): JsonResponse
    {
        $productModel = $this->findProductModelOr404($id);
        $normalizedProductModel = $this->normalizeProductModel($productModel);

        $event = new ProductModelFetchEvent($id);
        $this->eventDispatcher->dispatch(ProductModelFetchEvent::class, $event);

        return new JsonResponse($normalizedProductModel);
    }

    /**
     * @param string $identifier
     *
     * @throws NotFoundHttpException If product model is not found or the user cannot see it
     *
     * @return JsonResponse
     */
    public function getByCodeAction(string $identifier): JsonResponse
    {
        $productModel = $this->productModelRepository->findOneByIdentifier($identifier);
        $cantView = $this->objectFilter->filterObject($productModel, 'pim.internal_api.product.view');

        if (null === $productModel || true === $cantView) {
            throw new NotFoundHttpException(
                sprintf('Product model with identifier "%s" could not be found.', $identifier)
            );
        }

        $normalizedProductModel = $this->normalizeProductModel($productModel);

        return new JsonResponse($normalizedProductModel);
    }

    /**
     * Returns a set of product models from identifiers parameter
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexAction(Request $request): JsonResponse
    {
        $productModelIdentifiers = explode(',', $request->get('identifiers'));
        $productModels = $this->productModelRepository->findByIdentifiers($productModelIdentifiers);

        $normalizedProductModels = array_map(function ($productModel) {
            return $this->normalizeProductModel($productModel);
        }, $productModels);

        return new JsonResponse($normalizedProductModels);
    }

    /**
     * @param Request $request
     *
     * @AclAncestor("pim_enrich_product_model_create")
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new RedirectResponse('/');
        }

        $productModel = $this->productModelFactory->create();
        $content = json_decode($request->getContent(), true);

        $this->productModelUpdater->update($productModel, $content);

        $violations = $this->validator->validate($productModel);

        if (count($violations) > 0) {
            $normalizedViolations = [];
            foreach ($violations as $violation) {
                $normalizedViolations[] = $this->violationNormalizer->normalize(
                    $violation,
                    'internal_api',
                    ['product_model' => $productModel]
                );
            }

            return new JsonResponse(['values' => $normalizedViolations], 400);
        }

        $this->productModelSaver->save($productModel);
        $normalizedProductModel = $this->normalizeProductModel($productModel);

        return new JsonResponse($normalizedProductModel);
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @AclAncestor("pim_enrich_product_model_edit_attributes")
     *
     * @return Response
     */
    public function postAction(Request $request, int $id): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new RedirectResponse('/');
        }

        $productModel = $this->findProductModelOr404($id);
        $data = json_decode($request->getContent(), true);

        $this->updateProductModel($productModel, $data);

        $violations = $this->validator->validate($productModel);
        $violations->addAll($this->localizedConverter->getViolations());

        if (0 === $violations->count()) {
            $this->productModelSaver->save($productModel);
            $normalizedProductModel = $this->normalizeProductModel($productModel);

            return new JsonResponse($normalizedProductModel);
        }

        $normalizedViolations = [];
        foreach ($violations as $violation) {
            $normalizedViolations[] = $this->constraintViolationNormalizer->normalize(
                $violation,
                'internal_api',
                ['productModel' => $productModel]
            );
        }

        return new JsonResponse(['values' => $normalizedViolations], 400);
    }

    /**
     * Return direct children (products or product models) of the parent's given id
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function childrenAction(Request $request): JsonResponse
    {
        $parent = $this->findProductModelOr404($request->get('id'));
        $children = $this->productModelRepository->findChildrenProductModels($parent);
        if (empty($children)) {
            $children = $this->productModelRepository->findChildrenProducts($parent);
        }

        $normalizedChildren = [];
        foreach ($children as $child) {
            if (!$child instanceof ProductModelInterface && !$child instanceof ProductInterface) {
                throw new \LogicException(sprintf(
                    'Child of a product model must be of class "%s" or "%s", "%s" received.',
                    ProductModelInterface::class,
                    ProductInterface::class,
                    get_class($child)
                ));
            }

            $normalizedChildren[] = $this->entityWithFamilyVariantNormalizer->normalize(
                $child,
                'internal_api',
                ['locale' => $this->userContext->getCurrentLocaleCode()]
            );
        }

        return new JsonResponse($normalizedChildren);
    }

    /**
     * Returns the last level of product models belonging to a Family Variant with a given search code
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function searchLastLevelProductModelByCode(Request $request): JsonResponse
    {
        $search = $request->query->get('search');
        $options = $request->query->get('options');
        $familyVariantCode = $options['family_variant'];
        $page = (int) ($options['page']) - 1;
        $familyVariant = $this->getFamilyVariant($familyVariantCode);

        $productModels = $this->productModelRepository->searchLastLevelByCode(
            $familyVariant,
            $search,
            self::PRODUCT_MODELS_LIMIT,
            $page
        );

        $normalizedProductModels = $this->buildNormalizedProductModels($productModels);

        return new JsonResponse($normalizedProductModels);
    }

    /**
     * Returns all the product models (sub and root) of a family variant
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listFamilyVariantProductModels(Request $request)
    {
        $search = trim($request->query->get('search'));
        $options = $request->query->get('options');
        $familyVariant = $this->getFamilyVariant($options['family_variant']);

        $productModels = $this->productModelRepository->findProductModelsForFamilyVariant($familyVariant, $search);
        $normalizedProductModels = $this->buildNormalizedProductModels($productModels);

        return new JsonResponse($normalizedProductModels);
    }

    /**
     * Remove product model
     *
     * @param Request $request
     * @param int     $id
     *
     * @AclAncestor("pim_enrich_product_model_remove")
     *
     * @return Response
     */
    public function removeAction(Request $request, $id): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new RedirectResponse('/');
        }

        $productModel = $this->findProductModelOr404($id);
        $this->productModelRemover->remove($productModel);

        if (null !== $this->productModelClient && null !== $this->productAndProductModelClient) {
            $this->productModelClient->refreshIndex();
            $this->productAndProductModelClient->refreshIndex();
        }

        return new JsonResponse();
    }

    /**
     * Returns the family variant object from a family variant code
     *
     * @param string $familyVariantCode
     *
     * @throws \InvalidArgumentException
     *
     * @return FamilyVariantInterface
     */
    private function getFamilyVariant(string $familyVariantCode): FamilyVariantInterface
    {
        $familyVariant = $this->familyVariantRepository->findOneByIdentifier($familyVariantCode);
        if (null === $familyVariant) {
            throw new \InvalidArgumentException(sprintf('Unknown family variant code "%s"', $familyVariantCode));
        }

        return $familyVariant;
    }

    /**
     * Returns an array of normalized product models from an array of product model objects
     *
     * @param array $productModels
     *
     * @return array
     */
    private function buildNormalizedProductModels(array $productModels): array
    {
        $normalizedProductModels = [];
        foreach ($productModels as $productModel) {
            $normalizedProductModels[$productModel->getCode()] = $this->normalizeProductModel(
                $productModel
            );
        }

        return $normalizedProductModels;
    }

    /**
     * @param ProductModelInterface $productModel
     *
     * @return array
     */
    private function normalizeProductModel(ProductModelInterface $productModel): array
    {
        $normalizationContext = $this->userContext->toArray() + ['filter_types' => []];

        return $this->normalizer->normalize(
            $productModel,
            'internal_api',
            $normalizationContext
        );
    }

    /**
     * Updates product with the provided request data
     *
     * @param ProductModelInterface $productModel
     * @param array                 $data
     */
    private function updateProductModel(ProductModelInterface $productModel, array $data): void
    {
        unset($data['parent']);
        $values = $this->productValueConverter->convert($data['values']);

        $values = $this->localizedConverter->convertToDefaultFormats($values, [
            'locale' => $this->userContext->getUiLocale()->getCode(),
        ]);

        $dataFiltered = $this->emptyValuesFilter->filter($productModel, ['values' => $values]);

        if (!empty($dataFiltered)) {
            $data = array_replace($data, $dataFiltered);
        } else {
            $data['values'] = [];
        }

        if (!$productModel->isRoot()) {
            $data = $this->productModelAttributeFilter->filter($data);
        }

        $this->productModelUpdater->update($productModel, $data);
    }

    /**
     * Find a product model by its id or throw a 404
     *
     * @param string $id the product id
     *
     * @throws NotFoundHttpException
     *
     * @return ProductModelInterface
     */
    protected function findProductModelOr404($id): ProductModelInterface
    {
        $productModel = $this->productModelRepository->find($id);
        $productModel = $this->objectFilter->filterObject($productModel, 'pim.internal_api.product.view') ? null : $productModel;

        if (null === $productModel) {
            throw new NotFoundHttpException(
                sprintf('ProductModel with id %s could not be found.', $id)
            );
        }

        return $productModel;
    }
}