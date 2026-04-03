import { type IAbstractPlugin } from '@pimcore/studio-ui-bundle'
import { CocktailDemoModule } from './modules/cocktail-demo-module'

export const CocktailDemoPlugin: IAbstractPlugin = {
  name: 'CocktailDemoPlugin',

  onStartup ({ moduleSystem }) {
    moduleSystem.registerModule(CocktailDemoModule)
  }
}
