import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import { type IconLibrary } from '@pimcore/studio-ui-bundle/modules/icon-library'
import { type WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { type ComponentRegistry, componentConfig } from '@pimcore/studio-ui-bundle/modules/app'
import CocktailGlassIcon from '../assets/icons/cocktail-glass.inline.svg?react'
import { CocktailListingContainer } from '../components/cocktail-listing/cocktail-listing-container'
import {
  CocktailSidebarButton,
  COCKTAIL_LISTING_WIDGET_ID
} from '../components/cocktail-sidebar-button/cocktail-sidebar-button'

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

    // 3. Register the sidebar button into the left sidebar slot
    const componentRegistry = container.get<ComponentRegistry>(
      serviceIds['App/ComponentRegistry/ComponentRegistry']
    )
    componentRegistry.registerToSlot(componentConfig.leftSidebar.slot.name, {
      name: 'cocktailListing',
      priority: 300,
      component: CocktailSidebarButton
    })
  }
}
