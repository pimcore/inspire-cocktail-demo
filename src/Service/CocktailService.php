<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Service;

use Exception;
use Pimcore\Bundle\InspireCocktailDemoBundle\Event\CocktailEvent;
use Pimcore\Bundle\InspireCocktailDemoBundle\Event\CocktailListItemEvent;
use Pimcore\Bundle\InspireCocktailDemoBundle\Hydrator\CocktailHydratorInterface;
use Pimcore\Bundle\InspireCocktailDemoBundle\MappedParameter\CocktailCollectionParameters;
use Pimcore\Bundle\InspireCocktailDemoBundle\Repository\CocktailRepositoryInterface;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\Cocktail;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\UpdateCocktail;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\EnvironmentException;
use Pimcore\Bundle\StudioBackendBundle\Response\Collection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
final readonly class CocktailService implements CocktailServiceInterface
{
    public function __construct(
        private CocktailRepositoryInterface $cocktailRepository,
        private CocktailHydratorInterface $cocktailHydrator,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function listCocktails(CocktailCollectionParameters $parameters): Collection
    {
        $cocktails = $this->cocktailRepository->getCocktails(
            $parameters->getOffset(),
            $parameters->getPageSize(),
        );

        $items = [];
        foreach ($cocktails as $cocktail) {
            $item = $this->cocktailHydrator->hydrateListItem(
                $cocktail,
                $parameters->getLocale(),
            );
            $this->eventDispatcher->dispatch(
                new CocktailListItemEvent($item),
                CocktailListItemEvent::EVENT_NAME,
            );
            $items[] = $item;
        }

        $totalCount = $this->cocktailRepository->getTotalCount();

        return new Collection($totalCount, $items);
    }

    public function getCocktail(int $id, string $locale): Cocktail
    {
        $cocktail = $this->cocktailRepository->getCocktailById($id);

        $response = $this->cocktailHydrator->hydrate($cocktail, $locale);
        $this->eventDispatcher->dispatch(
            new CocktailEvent($response),
            CocktailEvent::EVENT_NAME,
        );

        return $response;
    }

    public function updateCocktail(int $id, UpdateCocktail $parameters): Cocktail
    {
        $cocktail = $this->cocktailRepository->getCocktailById($id);
        $locale = $parameters->getLocale();

        if ($parameters->getName() !== null) {
            $cocktail->setName($parameters->getName(), $locale);
        }

        if ($parameters->getDescription() !== null) {
            $cocktail->setDescription($parameters->getDescription(), $locale);
        }

        if ($parameters->getInstructions() !== null) {
            $cocktail->setInstructions($parameters->getInstructions(), $locale);
        }

        if ($parameters->getGlassType() !== null) {
            $cocktail->setGlassType($parameters->getGlassType());
        }

        if ($parameters->getPreparationMethod() !== null) {
            $cocktail->setPreparationMethod($parameters->getPreparationMethod());
        }

        if ($parameters->getStrength() !== null) {
            $cocktail->setStrength($parameters->getStrength());
        }

        if ($parameters->getFlavourProfile() !== null) {
            $cocktail->setFlavourProfile($parameters->getFlavourProfile());
        }

        if ($parameters->getOccasion() !== null) {
            $cocktail->setOccasion($parameters->getOccasion());
        }

        if ($parameters->getIngredients() !== null) {
            $ingredientData = array_map(
                static fn ($ing) => [
                    'ingredientId' => $ing->getIngredientId(),
                    'amount' => $ing->getAmount(),
                    'notes' => $ing->getNotes(),
                ],
                $parameters->getIngredients(),
            );
            $cocktail->setIngredients(
                $this->cocktailRepository->buildIngredientRelations($ingredientData),
            );
        }

        try {
            $cocktail->save();
        } catch (Exception $e) {
            throw new EnvironmentException($e->getMessage());
        }

        $response = $this->cocktailHydrator->hydrate($cocktail, $locale);
        $this->eventDispatcher->dispatch(
            new CocktailEvent($response),
            CocktailEvent::EVENT_NAME,
        );

        return $response;
    }
}
