import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react'
import { injectSliceWithState, addAppMiddleware } from '@pimcore/studio-ui-bundle/app'

export interface FiniderOption {
  value: string
  label: string
  count: number
}

export interface FiniderOptionsResponse {
  options: FiniderOption[]
}

export interface CocktailIngredient {
  name: string
  amount: number | null
  unit: string | null
  notes: string | null
}

export interface Cocktail {
  id: number
  name: string
  description: string | null
  glassType: string | null
  preparationMethod: string | null
  strength: string | null
  flavourProfile: string[]
  occasion: string[]
  ingredients: CocktailIngredient[]
}

export interface FinderResultsResponse {
  cocktails: Cocktail[]
}

export interface GetOptionsParams {
  field: string
  strength?: string
  occasion?: string
  flavourProfile?: string
}

export interface GetResultsParams {
  strength?: string
  occasion?: string
  flavourProfile?: string
}

export const cocktailFinderApi = createApi({
  reducerPath: 'cocktailFinderApi',
  baseQuery: fetchBaseQuery({ baseUrl: '/pimcore-studio/api/bundle/inspire-cocktail-demo' }),
  endpoints: (builder) => ({
    getOptions: builder.query<FiniderOptionsResponse, GetOptionsParams>({
      query: (params) => {
        const search = new URLSearchParams()
        search.set('field', params.field)
        if (params.strength != null && params.strength !== '') search.set('strength', params.strength)
        if (params.occasion != null && params.occasion !== '') search.set('occasion', params.occasion)
        if (params.flavourProfile != null && params.flavourProfile !== '') search.set('flavourProfile', params.flavourProfile)
        return `/finder/options?${search.toString()}`
      }
    }),
    getResults: builder.query<FinderResultsResponse, GetResultsParams>({
      query: (params) => {
        const search = new URLSearchParams()
        if (params.strength != null && params.strength !== '') search.set('strength', params.strength)
        if (params.occasion != null && params.occasion !== '') search.set('occasion', params.occasion)
        if (params.flavourProfile != null && params.flavourProfile !== '') search.set('flavourProfile', params.flavourProfile)
        return `/finder/results?${search.toString()}`
      }
    })
  })
})

// Inject into the shared Redux store
injectSliceWithState(cocktailFinderApi)
addAppMiddleware(cocktailFinderApi.middleware)

export const { useGetOptionsQuery, useGetResultsQuery } = cocktailFinderApi
