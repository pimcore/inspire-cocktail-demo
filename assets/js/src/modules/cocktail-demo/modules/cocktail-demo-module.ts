import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import { type IconLibrary } from '@pimcore/studio-ui-bundle/modules/icon-library'
import { type WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { type ComponentRegistry, componentConfig } from '@pimcore/studio-ui-bundle/modules/app'
import {
  type DynamicTypeFieldDefinitionRegistry,
  type GroupInfo
} from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { type DynamicTypeObjectLayoutRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import CocktailGlassIcon from '../assets/icons/cocktail-glass.inline.svg?react'
import { CocktailListingContainer } from '../components/cocktail-listing/cocktail-listing-container'
import {
  CocktailSidebarButton,
  COCKTAIL_LISTING_WIDGET_ID
} from '../components/cocktail-sidebar-button/cocktail-sidebar-button'
import {
  ShoppingListSidebarButton,
  SHOPPING_LIST_WIDGET_ID
} from '../components/shopping-list-sidebar-button/shopping-list-sidebar-button'
import { ShoppingListContainer } from '../components/shopping-list/shopping-list-container'

// Importing the slice registers it into the Redux store via injectSliceWithState
import '../store/cocktail-shopping-list-slice'

const INTERACTION_GROUP_INFO: GroupInfo = {
  icon: { type: 'name', value: 'cocktail-glass' },
  translationKey: 'field-definition.groups.interaction',
  order: 1100
}

export const CocktailDemoModule: AbstractModule = {
  onInit: () => {
    // 1. Register the custom cocktail glass icon
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)
    iconLibrary.register({
      name: 'cocktail-glass',
      component: CocktailGlassIcon
    })

    // 2. Register the cocktail listing as a widget
    const widgetRegistry = container.get<WidgetRegistry>(serviceIds.widgetManager)
    widgetRegistry.registerWidget({
      name: COCKTAIL_LISTING_WIDGET_ID,
      component: CocktailListingContainer
    })

    // 3. Register the shopping list as a widget
    widgetRegistry.registerWidget({
      name: SHOPPING_LIST_WIDGET_ID,
      component: ShoppingListContainer
    })

    const componentRegistry = container.get<ComponentRegistry>(
      serviceIds['App/ComponentRegistry/ComponentRegistry']
    )

    // 4. Register the cocktail listing sidebar button (priority 300)
    componentRegistry.registerToSlot(componentConfig.leftSidebar.slot.name, {
      name: 'cocktailListing',
      priority: 300,
      component: CocktailSidebarButton
    })

    // 5. Register the shopping list sidebar button (priority 290 — just below cocktails)
    componentRegistry.registerToSlot(componentConfig.leftSidebar.slot.name, {
      name: 'cocktailShoppingList',
      priority: 290,
      component: ShoppingListSidebarButton
    })

    // 6. Register the "Add to Shopping List" field definition type
    const fieldDefinitionRegistry = container.get<DynamicTypeFieldDefinitionRegistry>(
      'DynamicTypes/FieldDefinitionRegistry'
    )
    fieldDefinitionRegistry.registerDropdownGroupInfo('interaction', INTERACTION_GROUP_INFO)
    fieldDefinitionRegistry.registerDynamicType(
      container.get('CocktailDemo/FieldDefinition/AddToShoppingList')
    )

    // 7. Register the "Add to Shopping List" object layout renderer
    const objectLayoutRegistry = container.get<DynamicTypeObjectLayoutRegistry>(
      'DynamicTypes/ObjectLayoutRegistry'
    )
    objectLayoutRegistry.registerDynamicType(
      container.get('CocktailDemo/ObjectLayout/AddToShoppingList')
    )
  }
}
