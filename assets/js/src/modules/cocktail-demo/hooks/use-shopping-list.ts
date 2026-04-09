import { useAppDispatch, useAppSelector } from '@pimcore/studio-ui-bundle/app'
import {
  addCocktail,
  removeCocktail,
  setCocktailQuantity,
  clearShoppingList,
  selectShoppingListItems
} from '../store/cocktail-shopping-list-slice'

export interface UseShoppingListReturn {
  items: Record<number, number>
  totalCount: number
  addCocktail: (id: number) => void
  removeCocktail: (id: number) => void
  setCocktailQuantity: (id: number, quantity: number) => void
  clearShoppingList: () => void
}

export const useShoppingList = (): UseShoppingListReturn => {
  const dispatch = useAppDispatch()
  const items = useAppSelector(selectShoppingListItems)
  const totalCount = Object.values(items).reduce((sum, qty) => sum + qty, 0)

  return {
    items,
    totalCount,
    addCocktail: (id: number) => { dispatch(addCocktail(id)) },
    removeCocktail: (id: number) => { dispatch(removeCocktail(id)) },
    setCocktailQuantity: (id: number, quantity: number) => { dispatch(setCocktailQuantity({ id, quantity })) },
    clearShoppingList: () => { dispatch(clearShoppingList()) }
  }
}
