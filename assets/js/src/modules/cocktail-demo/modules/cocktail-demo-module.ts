import { type AbstractModule } from '@pimcore/studio-ui-bundle'

export const CocktailDemoModule: AbstractModule = {
  onInit: () => {
    console.log('Hello from cocktail demo')
  }
}
