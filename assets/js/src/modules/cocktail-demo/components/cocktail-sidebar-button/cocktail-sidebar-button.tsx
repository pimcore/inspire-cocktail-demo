import React from 'react'
import { IconButton } from '@pimcore/studio-ui-bundle/components'
import { useWidgetManager, type WidgetManagerTabConfig } from '@pimcore/studio-ui-bundle/modules/widget-manager'

export const COCKTAIL_LISTING_WIDGET_ID = 'cocktail-listing'

export const COCKTAIL_LISTING_WIDGET_CONFIG: WidgetManagerTabConfig = {
  name: 'Cocktails',
  id: COCKTAIL_LISTING_WIDGET_ID,
  component: COCKTAIL_LISTING_WIDGET_ID,
  config: {
    label: 'Cocktails',
    icon: {
      type: 'name',
      value: 'cocktail-glass'
    }
  }
}

export const CocktailSidebarButton = (): React.JSX.Element => {
  const { openMainWidget } = useWidgetManager()

  return (
    <IconButton
      icon={ { value: 'cocktail-glass' } }
      onClick={ () => { openMainWidget(COCKTAIL_LISTING_WIDGET_CONFIG) } }
      tooltip={ { title: 'Cocktails' } }
      type="text"
    />
  )
}
