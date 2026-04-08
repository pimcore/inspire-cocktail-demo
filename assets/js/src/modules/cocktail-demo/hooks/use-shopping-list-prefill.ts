import { useEffect, useRef } from 'react'
import { useElementGetIdByPathQuery } from '@pimcore/studio-ui-bundle/api/elements'
import { useShoppingList } from './use-shopping-list'

interface PrefillEntry {
  path: string
  quantity: number
}

const PREFILL_COCKTAILS: PrefillEntry[] = [
  { path: '/Cocktail Demo/Cocktails/negroni', quantity: 2 },
  { path: '/Cocktail Demo/Cocktails/mojito', quantity: 1 },
  { path: '/Cocktail Demo/Cocktails/aperol-spritz', quantity: 3 }
]

interface PathQueryResult {
  path: string
  quantity: number
  id: number | undefined
  isLoading: boolean
}

const usePathQuery = (entry: PrefillEntry): PathQueryResult => {
  const { data, isLoading } = useElementGetIdByPathQuery({
    elementType: 'data-object',
    elementPath: entry.path
  })

  return {
    path: entry.path,
    quantity: entry.quantity,
    id: data?.id,
    isLoading
  }
}

export const useShoppingListPrefill = (): void => {
  const hasPrefilledRef = useRef(false)
  const { setCocktailQuantity } = useShoppingList()

  const result0 = usePathQuery(PREFILL_COCKTAILS[0])
  const result1 = usePathQuery(PREFILL_COCKTAILS[1])
  const result2 = usePathQuery(PREFILL_COCKTAILS[2])

  const results = [result0, result1, result2]
  const allResolved = results.every((r) => !r.isLoading && r.id !== undefined)

  useEffect(() => {
    if (hasPrefilledRef.current || !allResolved) {
      return
    }

    hasPrefilledRef.current = true

    results.forEach((r) => {
      if (r.id !== undefined) {
        setCocktailQuantity(r.id, r.quantity)
      }
    })
  }, [allResolved])
}
