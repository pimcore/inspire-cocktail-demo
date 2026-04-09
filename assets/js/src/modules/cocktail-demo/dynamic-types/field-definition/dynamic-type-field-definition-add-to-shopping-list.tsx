import { injectable } from '@pimcore/studio-ui-bundle/app'
import {
  DynamicTypeFieldDefinitionLayoutAbstract,
  type FieldDefinitionContext,
  type FieldDefinitionLayout
} from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { type ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import React from 'react'
import { AddToShoppingListFormFields } from './components/add-to-shopping-list-form-fields'

export interface FieldDefinitionAddToShoppingList extends FieldDefinitionLayout {
  defaultAmount: number
}

@injectable()
export class DynamicTypeFieldDefinitionAddToShoppingList extends DynamicTypeFieldDefinitionLayoutAbstract {
  readonly id = 'addToShoppingList'

  getGroup (): string[] {
    return ['interaction']
  }

  getIcon (): ElementIcon {
    return { type: 'name', value: 'cocktail-glass' }
  }

  getDefaultData (): FieldDefinitionAddToShoppingList {
    return {
      ...super.getDefaultData(),
      fieldtype: 'addToShoppingList',
      defaultAmount: 1
    }
  }

  getAllowedChildTags (_context: FieldDefinitionContext): string[] {
    return []
  }

  getSpecificFormFields (_context: FieldDefinitionContext): React.JSX.Element {
    return <AddToShoppingListFormFields />
  }
}
