import React from 'react'
import { Badge } from '@pimcore/studio-ui-bundle/components'
import { IconButton } from '@pimcore/studio-ui-bundle/components'
import { useWidgetManager, type WidgetManagerTabConfig } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { useShoppingList } from '../../hooks/use-shopping-list'
import { useStyles } from './shopping-list-sidebar-button.styles'

export const SHOPPING_LIST_WIDGET_ID = 'cocktail-shopping-list'

export const SHOPPING_LIST_WIDGET_CONFIG: WidgetManagerTabConfig = {
  name: 'Shopping List',
  id: SHOPPING_LIST_WIDGET_ID,
  component: SHOPPING_LIST_WIDGET_ID,
  config: {
    label: 'Shopping List',
    icon: {
      type: 'name',
      value: 'list'
    }
  }
}

export const ShoppingListSidebarButton = (): React.JSX.Element => {
  const { styles } = useStyles()
  const { openMainWidget } = useWidgetManager()
  const { totalCount } = useShoppingList()

  return (
    <div className={ styles.buttonWrapper }>
      <Badge
        count={ totalCount }
        offset={ [-9, 9] }
        size="small"
      >
        <IconButton
          icon={ { value: 'list' } }
          onClick={ () => { openMainWidget(SHOPPING_LIST_WIDGET_CONFIG) } }
          tooltip={ { title: 'Shopping List' } }
          type="text"
        />
      </Badge>
    </div>
  )
}
