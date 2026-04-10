import React from 'react'
import { type AbstractDecoratorProps } from '@pimcore/studio-ui-bundle/modules/element'
import { Icon } from '@pimcore/studio-ui-bundle/components'
import { AddToShoppingListSidebar } from '../add-to-shopping-list-sidebar/add-to-shopping-list-sidebar'

export const withAddToShoppingListTab = (
  useBaseHook: AbstractDecoratorProps['useSidebarOptions']
): AbstractDecoratorProps['useSidebarOptions'] => {
  const useAddToShoppingListTab: typeof useBaseHook = () => {
    const { getProps: baseGetProps } = useBaseHook()

    const getProps: typeof baseGetProps = () => {
      const baseProps = baseGetProps()

      return {
        ...baseProps,
        entries: [
          ...baseProps.entries,
          {
            component: <AddToShoppingListSidebar />,
            key: 'add-to-shopping-list',
            icon: <Icon value="list" />,
            tooltip: 'Add to shopping list'
          }
        ]
      }
    }

    return { getProps }
  }

  return useAddToShoppingListTab
}
