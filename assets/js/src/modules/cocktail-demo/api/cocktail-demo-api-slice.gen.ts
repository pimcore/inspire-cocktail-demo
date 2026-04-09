import { api } from "@pimcore/studio-ui-bundle/api";
export const addTagTypes = ["Bundle Inspire Cocktail Demo"] as const;
const injectedRtkApi = api
  .enhanceEndpoints({
    addTagTypes,
  })
  .injectEndpoints({
    endpoints: (build) => ({
      bundleInspireCocktailDemoCocktailCollection: build.query<
        BundleInspireCocktailDemoCocktailCollectionApiResponse,
        BundleInspireCocktailDemoCocktailCollectionApiArg
      >({
        query: (queryArg) => ({
          url: `/pimcore-studio/api/bundle/inspire-cocktail-demo/cocktails`,
          params: { page: queryArg.page, pageSize: queryArg.pageSize },
        }),
        providesTags: ["Bundle Inspire Cocktail Demo"],
      }),
      bundleInspireCocktailDemoCocktailGet: build.query<
        BundleInspireCocktailDemoCocktailGetApiResponse,
        BundleInspireCocktailDemoCocktailGetApiArg
      >({
        query: (queryArg) => ({
          url: `/pimcore-studio/api/bundle/inspire-cocktail-demo/cocktails/${queryArg.id}`,
        }),
        providesTags: ["Bundle Inspire Cocktail Demo"],
      }),
      bundleInspireCocktailDemoCocktailUpdate: build.mutation<
        BundleInspireCocktailDemoCocktailUpdateApiResponse,
        BundleInspireCocktailDemoCocktailUpdateApiArg
      >({
        query: (queryArg) => ({
          url: `/pimcore-studio/api/bundle/inspire-cocktail-demo/cocktails/${queryArg.id}`,
          method: "PUT",
          body: queryArg.bundleInspireCocktailDemoUpdateCocktail,
        }),
        invalidatesTags: ["Bundle Inspire Cocktail Demo"],
      }),
      bundleInspireCocktailDemoShoppingListCalculate: build.query<
        BundleInspireCocktailDemoShoppingListCalculateApiResponse,
        BundleInspireCocktailDemoShoppingListCalculateApiArg
      >({
        query: (queryArg) => ({
          url: `/pimcore-studio/api/bundle/inspire-cocktail-demo/shopping-list/calculate`,
          method: "POST",
          body: queryArg.bundleInspireCocktailDemoShoppingListParameters,
        }),
        providesTags: ["Bundle Inspire Cocktail Demo"],
      }),
    }),
    overrideExisting: false,
  });
export { injectedRtkApi as api };
export type BundleInspireCocktailDemoCocktailCollectionApiResponse =
  /** status 200 Paginated list of cocktails with basic details */ {
    totalItems: number;
    items: CocktailListItem[];
  };
export type BundleInspireCocktailDemoCocktailCollectionApiArg = {
  /** Page number */
  page: number;
  /** Number of items per page */
  pageSize: number;
};
export type BundleInspireCocktailDemoCocktailGetApiResponse =
  /** status 200 Full cocktail details including ingredients */ Cocktail;
export type BundleInspireCocktailDemoCocktailGetApiArg = {
  /** Id of the cocktail */
  id: number;
};
export type BundleInspireCocktailDemoCocktailUpdateApiResponse =
  /** status 200 Updated cocktail details */ Cocktail;
export type BundleInspireCocktailDemoCocktailUpdateApiArg = {
  /** Id of the cocktail */
  id: number;
  bundleInspireCocktailDemoUpdateCocktail: UpdateCocktail;
};
export type BundleInspireCocktailDemoShoppingListCalculateApiResponse =
  /** status 200 Shopping list with cocktails and aggregated ingredients */ ShoppingListResponse;
export type BundleInspireCocktailDemoShoppingListCalculateApiArg = {
  bundleInspireCocktailDemoShoppingListParameters: ShoppingListParameters;
};
export type CocktailListItem = {
  /** AdditionalAttributes */
  additionalAttributes?: {
    [key: string]: string | number | boolean | object;
  };
  /** Cocktail ID */
  id: number;
  /** Name of the cocktail */
  name: string;
  /** Glass type */
  glassType?: any;
  /** Preparation method */
  preparationMethod?: any;
  /** Strength level */
  strength?: any;
};
export type Error = {
  /** Message */
  message: string;
};
export type DevError = {
  /** Message */
  message: string;
  /** Details */
  details: string;
};
export type CocktailIngredient = {
  /** AdditionalAttributes */
  additionalAttributes?: {
    [key: string]: string | number | boolean | object;
  };
  /** Ingredient ID */
  ingredientId: number;
  /** Ingredient name */
  name: string;
  /** Amount of the ingredient */
  amount?: any;
  /** Additional notes for the ingredient */
  notes?: any;
  /** Unit of measurement */
  unit?: any;
};
export type Cocktail = {
  /** AdditionalAttributes */
  additionalAttributes?: {
    [key: string]: string | number | boolean | object;
  };
  /** Cocktail ID */
  id: number;
  /** Name of the cocktail */
  name: string;
  /** Description of the cocktail */
  description?: any;
  /** Glass type */
  glassType?: any;
  /** Preparation method */
  preparationMethod?: any;
  /** Strength level */
  strength?: any;
  /** Flavour profile tags */
  flavourProfile?: string[];
  /** Occasion tags */
  occasion?: string[];
  /** Ingredients list */
  ingredients?: CocktailIngredient[];
};
export type UpdateCocktailIngredient = {
  /** ID of the ingredient to link */
  ingredientId: number;
  /** Amount of the ingredient */
  amount?: any;
  /** Additional notes */
  notes?: any;
};
export type UpdateCocktail = {
  /** Locale for localized fields */
  locale?: string;
  /** Name of the cocktail */
  name?: any;
  /** Description of the cocktail */
  description?: any;
  /** Preparation instructions */
  instructions?: any;
  /** Glass type */
  glassType?: any;
  /** Preparation method */
  preparationMethod?: any;
  /** Strength level */
  strength?: any;
  /** Flavour profile tags */
  flavourProfile?: string[];
  /** Occasion tags */
  occasion?: string[];
  /** Ingredients list */
  ingredients?: UpdateCocktailIngredient[];
};
export type ShoppingListResponse = {
  /** AdditionalAttributes */
  additionalAttributes?: {
    [key: string]: string | number | boolean | object;
  };
  /** List of requested cocktails */
  cocktails: Cocktail[];
  /** Aggregated shopping list of ingredients with total amounts */
  ingredients: CocktailIngredient[];
};
export type ShoppingListItem = {
  /** ID of the cocktail */
  cocktailId: number;
  /** Number of cocktails to prepare */
  amount: number;
};
export type ShoppingListParameters = {
  /** List of cocktails with amounts */
  items: ShoppingListItem[];
  /** Locale for translations */
  locale?: string;
};
export const {
  useBundleInspireCocktailDemoCocktailCollectionQuery,
  useBundleInspireCocktailDemoCocktailGetQuery,
  useBundleInspireCocktailDemoCocktailUpdateMutation,
  useBundleInspireCocktailDemoShoppingListCalculateQuery,
} = injectedRtkApi;
