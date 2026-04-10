import React, { useState } from 'react'
import { Empty } from 'antd'
import { isNil } from 'lodash'
import { useRowSelection } from '@pimcore/studio-ui-bundle/modules/element'
import {
  ContentLayout,
  Content,
  Header,
  Toolbar,
  Button,
  InputNumber,
  Form,
  FormKit
} from '@pimcore/studio-ui-bundle/components'
import { useShoppingList } from '../../../../hooks/use-shopping-list'

export const AddToShoppingListSidebar = (): React.JSX.Element => {
  const { selectedRows, selectedRowsData } = useRowSelection()
  const { items, setCocktailQuantity } = useShoppingList()

  const selectedIds = Object.keys(selectedRows ?? {}).map(Number)

  const [quantities, setQuantities] = useState<Record<number, number>>({})

  const getQuantity = (id: number): number => quantities[id] ?? 1

  const handleQuantityChange = (id: number, value: number | null): void => {
    if (isNil(value) || value <= 0) return
    setQuantities((prev) => ({ ...prev, [id]: value }))
  }

  const handleAddAll = (): void => {
    selectedIds.forEach((id) => {
      const qty = getQuantity(id)
      const existing = items[id] ?? 0
      setCocktailQuantity(id, existing + qty)
    })
  }

  return (
    <ContentLayout
      renderToolbar={
        selectedIds.length > 0
          ? (
            <Toolbar justify="flex-end">
              <Button
                onClick={ handleAddAll }
                type="primary"
              >
                Add to shopping list
              </Button>
            </Toolbar>
          )
          : undefined
      }
    >
      <Content padded>
        <Header title="Add to shopping list" />

        { selectedIds.length === 0 && (
          <Empty
            description="No cocktails selected"
            image={ Empty.PRESENTED_IMAGE_SIMPLE }
          />
        ) }

        { selectedIds.length > 0 && (
          <FormKit>
            { selectedIds.map((id) => {
              const row = selectedRowsData[id]
              const label: string = (row?.fullpath as string | undefined) ?? `Cocktail #${id}`

              return (
                <Form.Item
                  key={ id }
                  label={ label }
                >
                  <InputNumber
                    min={ 1 }
                    onChange={ (value) => { handleQuantityChange(id, isNil(value) ? null : Number(value)) } }
                    style={ { width: '100%' } }
                    value={ getQuantity(id) }
                  />
                </Form.Item>
              )
            }) }
          </FormKit>
        ) }
      </Content>
    </ContentLayout>
  )
}
