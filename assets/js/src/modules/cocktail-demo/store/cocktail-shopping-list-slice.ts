import { createSlice, type PayloadAction } from '@reduxjs/toolkit'
import { injectSliceWithState, type RootState } from '@pimcore/studio-ui-bundle/app'

export interface CocktailShoppingListState {
  items: Record<number, number>
}

const initialState: CocktailShoppingListState = {
  items: {}
}

const cocktailShoppingListSlice = createSlice({
  name: 'cocktail-shopping-list',
  initialState,
  reducers: {
    addCocktail (state, action: PayloadAction<number>): void {
      const id = action.payload
      state.items[id] = (state.items[id] ?? 0) + 1
    },

    removeCocktail (state, action: PayloadAction<number>): void {
      const id = action.payload
      const current = state.items[id] ?? 0

      if (current <= 1) {
        // eslint-disable-next-line @typescript-eslint/no-dynamic-delete
        delete state.items[id]
      } else {
        state.items[id] = current - 1
      }
    },

    setCocktailQuantity (state, action: PayloadAction<{ id: number, quantity: number }>): void {
      const { id, quantity } = action.payload

      if (quantity <= 0) {
        // eslint-disable-next-line @typescript-eslint/no-dynamic-delete
        delete state.items[id]
      } else {
        state.items[id] = quantity
      }
    },

    clearShoppingList (state): void {
      state.items = {}
    }
  }
})

injectSliceWithState(cocktailShoppingListSlice)

export const {
  addCocktail,
  removeCocktail,
  setCocktailQuantity,
  clearShoppingList
} = cocktailShoppingListSlice.actions

export const selectShoppingListItems = (state: RootState): Record<number, number> =>
  (state['cocktail-shopping-list'] as CocktailShoppingListState | undefined)?.items ?? {}
