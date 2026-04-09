import React, { useMemo, useState, useEffect } from 'react'
import { Empty, Skeleton } from 'antd'
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
  IconButton
} from '@pimcore/studio-ui-bundle/components'
import { useDataObjectGetByIdQuery } from '@pimcore/studio-ui-bundle/api/data-object'
import type { DataObjectWithDetailData } from '@pimcore/studio-ui-bundle/api/data-object'
import { useShoppingList } from '../../hooks/use-shopping-list'
import { useStyles } from './shopping-list-container.styles'

// ─── Local types ─────────────────────────────────────────────────────────────

interface CocktailIngredientMeta {
  element: {
    id: number
    type: string
    subtype: string | null
    fullPath: string
    isPublished: boolean | null
  }
  fieldName: string
  columns: string[] | null
  data: Record<string, string | number | null> | null
}

interface CocktailObjectData {
  ingredients?: CocktailIngredientMeta[]
  localizedfields?: {
    en?: { name?: string }
    [locale: string]: { name?: string } | undefined
  }
}

interface AggregatedIngredient {
  id: number
  name: string
  fullPath: string
  elementId: number
  published: boolean | null
  totalAmount: number
}

interface IngredientTableRow {
  key: string
  name: string
  fullPath: string
  elementId: number
  published: boolean | null
  totalAmount: number
}

// ─── Grid columns (defined once at module level) ──────────────────────────────

const columnHelper = createColumnHelper<IngredientTableRow>()

const INGREDIENT_COLUMNS = [
  columnHelper.accessor('name', {
    header: 'Ingredient',
    size: 200,
    meta: {
      type: 'element',
      editable: false,
      clearable: false,
      config: {
        getElementInfo: (props: { row: { original: unknown } }) => {
          const row = props.row.original as IngredientTableRow
          return {
            elementType: 'data-object' as const,
            id: row.elementId,
            fullPath: row.fullPath,
            published: row.published ?? undefined
          }
        }
      }
    }
  }),
  columnHelper.accessor('totalAmount', {
    header: 'Amount',
    size: 80
  })
]

// ─── Helpers ─────────────────────────────────────────────────────────────────

const resolveCocktailName = (data: DataObjectWithDetailData): string => {
  const objectData = data.objectData as CocktailObjectData
  const localizedName = objectData?.localizedfields?.en?.name
  if (!isNil(localizedName) && localizedName !== '') {
    return localizedName
  }
  return data.key
}

const resolveIngredients = (data: DataObjectWithDetailData): CocktailIngredientMeta[] => {
  const objectData = data.objectData as CocktailObjectData
  return objectData?.ingredients ?? []
}

// ─── CocktailRow ──────────────────────────────────────────────────────────────

interface CocktailRowProps {
  cocktailId: number
  onRemove: () => void
}

const CocktailRow = (props: CocktailRowProps): React.JSX.Element => {
  const { cocktailId, onRemove } = props
  const { styles } = useStyles()

  const { data, isLoading } = useDataObjectGetByIdQuery({ id: cocktailId })

  const cocktailData = (!isNil(data) && 'objectData' in data)
    ? (data as DataObjectWithDetailData)
    : null

  const name = !isNil(cocktailData)
    ? resolveCocktailName(cocktailData)
    : `Cocktail #${cocktailId}`

  return (
    <div className={ styles.cocktailRow }>
      <span className={ styles.cocktailName }>
        { isLoading
          ? (
            <Skeleton
              active
              paragraph={ false }
              style={ { width: 120 } }
              title={ { width: 120 } }
            />
          )
          : name }
      </span>

      <div className={ styles.cocktailControls }>
        <Form.Item
          name={ String(cocktailId) }
          noStyle
        >
          <InputNumber
            min={ 1 }
            style={ { width: 72 } }
          />
        </Form.Item>

        <IconButton
          icon={ { value: 'close' } }
          onClick={ onRemove }
        />
      </div>
    </div>
  )
}

// ─── CocktailIngredientFetcher ────────────────────────────────────────────────

interface CocktailIngredientFetcherProps {
  cocktailId: number
  onResolved: (cocktailId: number, ingredients: CocktailIngredientMeta[]) => void
}

const CocktailIngredientFetcher = (props: CocktailIngredientFetcherProps): null => {
  const { cocktailId, onResolved } = props
  const { data, isError } = useDataObjectGetByIdQuery({ id: cocktailId })

  useEffect(() => {
    // Resolve with empty array on error so the loading state doesn't get stuck
    if (isError) {
      onResolved(cocktailId, [])
      return
    }
    if (isNil(data)) return
    const ingredients = ('objectData' in data)
      ? resolveIngredients(data as DataObjectWithDetailData)
      : []
    onResolved(cocktailId, ingredients)
  }, [data, isError, cocktailId, onResolved])

  return null
}

// ─── ShoppingListContainer ────────────────────────────────────────────────────

export const ShoppingListContainer = (): React.JSX.Element => {
  const { items, removeCocktail, setCocktailQuantity, clearShoppingList } = useShoppingList()

  const [form] = Form.useForm()

  const cocktailIds = Object.keys(items).map(Number)
  const isEmpty = cocktailIds.length === 0

  // Sync Redux state into the form whenever items change
  useEffect(() => {
    const formValues: Record<string, number> = {}
    Object.entries(items).forEach(([id, qty]) => {
      formValues[id] = qty
    })
    form.setFieldsValue(formValues)
  }, [items, form])

  // State: raw ingredients per cocktail ID
  const [resolvedIngredients, setResolvedIngredients] = useState<Record<number, CocktailIngredientMeta[]>>({})

  const handleIngredientResolved = useMemo(() => (
    (cocktailId: number, ingredients: CocktailIngredientMeta[]): void => {
      setResolvedIngredients((prev) => {
        if (prev[cocktailId] === ingredients) return prev
        return { ...prev, [cocktailId]: ingredients }
      })
    }
  ), [])

  const handleValuesChange = (changedValues: Record<string, number | null>): void => {
    Object.entries(changedValues).forEach(([key, value]) => {
      const id = Number(key)
      if (isNil(value) || value <= 0) {
        removeCocktail(id)
      } else {
        setCocktailQuantity(id, value)
      }
    })
  }

  // Aggregate ingredients across all cocktails, multiplied by quantity
  const ingredientRows: IngredientTableRow[] = useMemo(() => {
    const aggregated = new Map<number, AggregatedIngredient>()

    cocktailIds.forEach((cocktailId) => {
      const quantity = items[cocktailId] ?? 1
      const ingredients = resolvedIngredients[cocktailId] ?? []

      ingredients.forEach((meta) => {
        const ingredientId = meta.element.id
        const pathSegments = meta.element.fullPath.split('/')
        const ingredientName = pathSegments[pathSegments.length - 1] ?? meta.element.fullPath
        const amount = Number(meta.data?.['amount'] ?? 0)

        const existing = aggregated.get(ingredientId)
        if (!isNil(existing)) {
            existing.totalAmount += amount * quantity
          } else {
            aggregated.set(ingredientId, {
              id: ingredientId,
              name: ingredientName,
              fullPath: meta.element.fullPath,
              elementId: ingredientId,
              published: meta.element.isPublished,
              totalAmount: amount * quantity
            })
          }
      })
    })

    return Array.from(aggregated.values())
      .sort((a, b) => a.name.localeCompare(b.name))
      .map((ing) => ({
        key: String(ing.id),
        name: ing.name,
        fullPath: ing.fullPath,
        elementId: ing.elementId,
        published: ing.published,
        totalAmount: ing.totalAmount
      }))
  }, [resolvedIngredients, items])

  const isResolving = !isEmpty && cocktailIds.some((id) => isNil(resolvedIngredients[id]))

  return (
    <ContentLayout
      renderToolbar={
        !isEmpty
          ? (
            <Toolbar justify="flex-end">
              <Button
                danger
                onClick={ clearShoppingList }
                size="small"
                type="text"
              >
                Clear
              </Button>
            </Toolbar>
          )
          : undefined
      }
    >
      { /* Hidden fetchers — one per cocktail, safe hook usage */ }
      { cocktailIds.map((id) => (
        <CocktailIngredientFetcher
          cocktailId={ id }
          key={ id }
          onResolved={ handleIngredientResolved }
        />
      )) }

      <Content
        loading={ isResolving }
        padded
      >
        <Header title="Shopping List" />

        { isEmpty && (
          <Empty
            description="No cocktails in your shopping list yet"
            image={ Empty.PRESENTED_IMAGE_SIMPLE }
          />
        ) }

        { !isEmpty && (
          <>
            <FormKit formProps={ { form, onValuesChange: handleValuesChange } }>
              { cocktailIds.map((id) => (
                <CocktailRow
                  cocktailId={ id }
                  key={ id }
                  onRemove={ () => { removeCocktail(id) } }
                />
              )) }
            </FormKit>

            <FormKit.Panel title="Ingredients needed">
              <Grid
                autoWidth
                columns={ INGREDIENT_COLUMNS }
                data={ ingredientRows }
                size="small"
              />
            </FormKit.Panel>
          </>
        ) }
      </Content>
    </ContentLayout>
  )
}
