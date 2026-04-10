import React, { useEffect } from 'react'
import { Empty } from 'antd'
import { isNil } from 'lodash'
import { createColumnHelper } from '@tanstack/react-table'
import {
  ContentLayout,
  Content,
  Header,
  Toolbar,
  FormKit,
  Form,
  InputNumber,
  Grid,
  Button,
  IconButton,
  Spin,
  Flex,
  Space,
  Box
} from '@pimcore/studio-ui-bundle/components'
import { useShoppingList } from '../../hooks/use-shopping-list'
import {
  useBundleInspireCocktailDemoShoppingListCalculateQuery,
  type CocktailIngredient
} from '../../api/cocktail-demo-api-slice.gen'

// ─── Grid columns ─────────────────────────────────────────────────────────────

const columnHelper = createColumnHelper<CocktailIngredient>()

const INGREDIENT_COLUMNS = [
  columnHelper.accessor('name', { header: 'Ingredient', size: 200 }),
  columnHelper.accessor('amount', { header: 'Amount', size: 80 }),
  columnHelper.accessor('unit', { header: 'Unit', size: 80 }),
]

// ─── ShoppingListContainer ────────────────────────────────────────────────────

export const ShoppingListContainer = (): React.JSX.Element => {
  const { items, removeCocktail, setCocktailQuantity, clearShoppingList } = useShoppingList()

  const [form] = Form.useForm()

  const cocktailIds = Object.keys(items).map(Number)
  const isEmpty = cocktailIds.length === 0

  const { data, isFetching } = useBundleInspireCocktailDemoShoppingListCalculateQuery(
    {
      bundleInspireCocktailDemoShoppingListParameters: {
        items: cocktailIds.map((id) => ({ cocktailId: id, amount: items[id] ?? 1 }))
      }
    },
    { skip: isEmpty }
  )

  // Sync Redux state into the form whenever items change
  useEffect(() => {
    const formValues: Record<string, number> = {}
    Object.entries(items).forEach(([id, qty]) => {
      formValues[id] = qty
    })
    form.setFieldsValue(formValues)
  }, [items, form])

  const handleValuesChange = (changedValues: Record<string, number | null>): void => {
    Object.entries(changedValues).forEach(([key, value]) => {
      if (isNil(value) || value <= 0) return
      setCocktailQuantity(Number(key), value)
    })
  }

  return (
    <ContentLayout
      renderToolbar={
        !isEmpty
          ? (
            <Toolbar justify="flex-end">
              <Button
                color="danger"
                onClick={ clearShoppingList }
              >
                Clear
              </Button>
            </Toolbar>
          )
          : undefined
      }
    >
      <Content padded>
        <Header title="Shopping List" />

        { isEmpty && (
          <Empty
            description="No cocktails in your shopping list yet"
            image={ Empty.PRESENTED_IMAGE_SIMPLE }
          />
        ) }

        { !isEmpty && (
          <>
            <Box style={ { maxWidth: 600 } }>
              <FormKit formProps={ { form, onValuesChange: handleValuesChange } }>
                { (data?.cocktails ?? cocktailIds.map((id) => ({ id, name: `Cocktail #${id}` }))).map((cocktail) => (
                  <Flex
                    align="center"
                    justify="space-between"
                    key={ cocktail.id }
                  >
                    <span>{ cocktail.name }</span>

                    <Space size="extra-small">
                      <Form.Item
                        name={ String(cocktail.id) }
                        noStyle
                      >
                        <InputNumber min={ 1 } />
                      </Form.Item>

                      <IconButton
                        icon={ { value: 'close' } }
                        onClick={ () => { removeCocktail(cocktail.id) } }
                      />
                    </Space>
                  </Flex>
                )) }
              </FormKit>
            </Box>

            <FormKit.Panel title={
              <Space size="extra-small">
                Ingredients needed
                { isFetching && <Spin size="small" /> }
              </Space>
            }>
              <Grid
                autoWidth
                columns={ INGREDIENT_COLUMNS }
                data={ data?.ingredients ?? [] }
                size="small"
              />
            </FormKit.Panel>
          </>
        ) }
      </Content>
    </ContentLayout>
  )
}
