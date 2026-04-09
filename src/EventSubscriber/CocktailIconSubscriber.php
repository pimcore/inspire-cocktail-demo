<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\EventSubscriber;

use Pimcore\Bundle\StudioBackendBundle\DataObject\Event\PreResponse\DataObjectEvent;
use Pimcore\Bundle\StudioBackendBundle\Response\ElementIcon;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\ElementIconTypes;
use Pimcore\Model\DataObject\Cocktail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
final class CocktailIconSubscriber implements EventSubscriberInterface
{
    private const string ICON_BASE_PATH = '/bundles/pimcoreinspirecocktaildemo/icon/';

    private const array STRENGTH_ICON_MAP = [
        'non_alcoholic' => 'cocktail-non-alcoholic.svg',
        'low' => 'cocktail-low.svg',
        'medium' => 'cocktail-medium.svg',
        'strong' => 'cocktail-strong.svg',
    ];

    public static function getSubscribedEvents(): array
    {
        return [
            DataObjectEvent::EVENT_NAME => [
                ['handle'],
            ],
        ];
    }

    public function handle(DataObjectEvent $event): void
    {
        $dataObject = $event->getDataObject();

        if ($dataObject->getClassName() !== 'Cocktail') {
            return;
        }

        $cocktail = Cocktail::getById($dataObject->getId());

        if ($cocktail === null) {
            return;
        }

        $strength = $cocktail->getStrength();
        $iconFile = self::STRENGTH_ICON_MAP[$strength] ?? 'cocktail.svg';

        $customAttributes = $event->getCustomAttributes();
        $customAttributes->setIcon(
            new ElementIcon(
                ElementIconTypes::PATH->value,
                self::ICON_BASE_PATH . $iconFile,
            )
        );
        $event->setCustomAttributes($customAttributes);
    }
}
