import React, { useMemo, useState, useEffect } from 'react'
import { Button, Table, Empty, Skeleton } from 'antd'
import { isNil } from 'lodash'
import { useDataObjectGetByIdQuery } from '@pimcore/studio-ui-bundle/api/data-object'
import type { DataObjectWithDetailData } from '@pimcore/studio-ui-bundle/api/data-object'
import { useShoppingList } from '../../hooks/use-shopping-list'
import { useShoppingListPrefill } from '../../hooks/use-shopping-list-prefill'
import { useStyles } from './shopping-list-container.styles'

// ─── Local types ─────────────────────────────────────────────────────────────

// Shape of a single entry in the Cocktail.ingredients advancedManyToManyObjectRelation
// as returned by the Pimcore Studio Backend API
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

// Shape of the objectData field on a Cocktail data object
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
  totalAmount: number
}

interface IngredientTableRow {
  key: string
  name: string
  totalAmount: number
}

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

const INGREDIENT_TABLE_COLUMNS = [
  {
    title: 'Ingredient',
    dataIndex: 'name',
    key: 'name'
  },
  {
    title: 'Amount',
    dataIndex: 'totalAmount',
    key: 'totalAmount',
    width: 100,
    align: 'right' as const
  }
]

// ─── CocktailRow ──────────────────────────────────────────────────────────────

interface CocktailRowProps {
  cocktailId: number
  quantity: number
  onIncrement: () => void
  onDecrement: () => void
}

const CocktailRow = (props: CocktailRowProps): React.JSX.Element => {
  const { cocktailId, quantity, onIncrement, onDecrement } = props
  const { styles } = useStyles()

  const { data, isLoading } = useDataObjectGetByIdQuery({ id: cocktailId })

  const cocktailData = (!isNil(data) && 'objectData' in data)
    ? (data as DataObjectWithDetailData)
    : null

  const name = !isNil(cocktailData)
    ? resolveCocktailName(cocktailData)
    : `Cocktail #${cocktailId}`

  if (isLoading) {
    return (
      <div className={ styles.cocktailSection }>
        <Skeleton
          active
          paragraph={ { rows: 1 } }
          title={ false }
        />
      </div>
    )
  }

  return (
    <div className={ styles.cocktailSection }>
      <div className={ styles.cocktailHeader }>
        <span className={ styles.cocktailName }>{ name }</span>

        <div className={ styles.quantityControls }>
          <Button
            onClick={ onDecrement }
            size="small"
            type="text"
          >
            −
          </Button>

          <span className={ styles.quantityValue }>{ quantity }</span>

          <Button
            onClick={ onIncrement }
            size="small"
            type="text"
          >
            +
          </Button>
        </div>
      </div>
    </div>
  )
}

// ─── CocktailIngredientFetcher ────────────────────────────────────────────────
// Fetches one cocktail's data and reports resolved ingredients via useEffect.
// Renders nothing — used purely for data fetching in a hook-safe way.

interface CocktailIngredientFetcherProps {
  cocktailId: number
  onResolved: (cocktailId: number, ingredients: CocktailIngredientMeta[]) => void
}

const CocktailIngredientFetcher = (props: CocktailIngredientFetcherProps): null => {
  const { cocktailId, onResolved } = props
  const { data } = useDataObjectGetByIdQuery({ id: cocktailId })

  useEffect(() => {
    if (!isNil(data) && 'objectData' in data) {
      const ingredients = resolveIngredients(data as DataObjectWithDetailData)
      onResolved(cocktailId, ingredients)
    }
  }, [data, cocktailId, onResolved])

  return null
}

// ─── ShoppingListContainer ────────────────────────────────────────────────────

export const ShoppingListContainer = (): React.JSX.Element => {
  const { styles } = useStyles()
  const { items, totalCount, addCocktail, removeCocktail, clearShoppingList } = useShoppingList()

  // Seed the list with demo cocktails on first mount
  useShoppingListPrefill()

  const cocktailIds = Object.keys(items).map(Number)
  const isEmpty = cocktailIds.length === 0

  // State: raw ingredients per cocktail ID — drives re-render when resolved
  const [resolvedIngredients, setResolvedIngredients] = useState<Record<number, CocktailIngredientMeta[]>>({})

  const handleIngredientResolved = useMemo(() => (
    (cocktailId: number, ingredients: CocktailIngredientMeta[]): void => {
      setResolvedIngredients((prev) => {
        if (prev[cocktailId] === ingredients) return prev
        return { ...prev, [cocktailId]: ingredients }
      })
    }
  ), [])

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
        totalAmount: ing.totalAmount
      }))
  }, [resolvedIngredients, items, cocktailIds])

  return (
    <div className={ styles.container }>
      { /* Hidden fetchers — one per cocktail, safe hook usage */ }
      { cocktailIds.map((id) => (
        <CocktailIngredientFetcher
          cocktailId={ id }
          key={ id }
          onResolved={ handleIngredientResolved }
        />
      )) }

      <div className={ styles.header }>
        <div className={ styles.headerTitle }>
          <span>Shopping List</span>

          { !isEmpty && (
            <span className={ styles.headerCount }>
              ({ totalCount } cocktail{ totalCount !== 1 ? 's' : '' })
            </span>
          ) }
        </div>

        { !isEmpty && (
          <Button
            danger
            onClick={ clearShoppingList }
            size="small"
            type="text"
          >
            Clear
          </Button>
        ) }
      </div>

      <div className={ styles.content }>
        { isEmpty && (
          <div className={ styles.emptyState }>
            <Empty
              description="No cocktails in your shopping list yet"
              image={ Empty.PRESENTED_IMAGE_SIMPLE }
            />
          </div>
        ) }

        { !isEmpty && (
          <>
            { cocktailIds.map((id) => (
              <CocktailRow
                cocktailId={ id }
                key={ id }
                onDecrement={ () => { removeCocktail(id) } }
                onIncrement={ () => { addCocktail(id) } }
                quantity={ items[id] ?? 1 }
              />
            )) }

            <div className={ styles.summarySection }>
              <div className={ styles.summaryTitle }>
                Ingredients needed
              </div>

              <Table<IngredientTableRow>
                columns={ INGREDIENT_TABLE_COLUMNS }
                dataSource={ ingredientRows }
                pagination={ false }
                size="small"
              />
            </div>
          </>
        ) }
      </div>
    </div>
  )
}
