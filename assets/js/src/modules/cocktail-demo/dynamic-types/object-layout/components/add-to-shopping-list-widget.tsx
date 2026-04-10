import { Button, Icon, InputNumber, Panel } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { useElementContext } from '@pimcore/studio-ui-bundle/modules/element'
import React, { useState } from 'react'
import { useShoppingList } from '../../../hooks/use-shopping-list'
import { type AddToShoppingListLayoutDefinition } from '../dynamic-type-object-layout-add-to-shopping-list'
import { useAddToShoppingListWidgetStyles } from './add-to-shopping-list-widget.styles'

export const AddToShoppingListWidget = (props: AddToShoppingListLayoutDefinition): React.JSX.Element => {
  const { defaultAmount = 1 } = props
  const { t } = useTranslation()
  const { styles } = useAddToShoppingListWidgetStyles()
  const { id } = useElementContext()
  const { setCocktailQuantity, items } = useShoppingList()

  const currentQuantity = items[id] ?? 0
  const [amount, setAmount] = useState<number>(defaultAmount)

  const handleAdd = (): void => {
    setCocktailQuantity(id, currentQuantity + amount)
  }

  return (
    <Panel
      theme="fieldset"
      title={ t('cocktail-demo.add-to-shopping-list') }
    >
      <div className={ styles.controls }>
        <InputNumber
          min={ 1 }
          precision={ 0 }
          value={ amount }
          onChange={ (value) => { setAmount(typeof value === 'number' ? value : defaultAmount) } }
        />

        <Button
          icon={ <Icon value="shopping-cart" /> }
          type="primary"
          onClick={ handleAdd }
        >
          { t('cocktail-demo.add-to-shopping-list') }
        </Button>
      </div>
    </Panel>
  )
}
