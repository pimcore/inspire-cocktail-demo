import { injectable } from '@pimcore/studio-ui-bundle/app'
import {
  DynamicTypeObjectLayoutAbstract,
  type AbstractObjectLayoutDefinition
} from '@pimcore/studio-ui-bundle/modules/element'
import React from 'react'
import { AddToShoppingListWidget } from './components/add-to-shopping-list-widget'

export interface AddToShoppingListLayoutDefinition extends AbstractObjectLayoutDefinition {
  defaultAmount?: number
}

@injectable()
export class DynamicTypeObjectLayoutAddToShoppingList extends DynamicTypeObjectLayoutAbstract {
  readonly id = 'addToShoppingList'

  getObjectLayoutComponent (props: AddToShoppingListLayoutDefinition): React.ReactElement {
    return <AddToShoppingListWidget { ...props } />
  }
}
