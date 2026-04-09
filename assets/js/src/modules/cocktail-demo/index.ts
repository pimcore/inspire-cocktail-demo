import { type IAbstractPlugin } from '@pimcore/studio-ui-bundle'
import { CocktailDemoModule } from './modules/cocktail-demo-module'
import { DynamicTypeFieldDefinitionAddToShoppingList } from './dynamic-types/field-definition/dynamic-type-field-definition-add-to-shopping-list'
import { DynamicTypeObjectLayoutAddToShoppingList } from './dynamic-types/object-layout/dynamic-type-object-layout-add-to-shopping-list'

export const CocktailDemoPlugin: IAbstractPlugin = {
  name: 'CocktailDemoPlugin',

  onInit ({ container }) {
    container
      .bind('CocktailDemo/FieldDefinition/AddToShoppingList')
      .to(DynamicTypeFieldDefinitionAddToShoppingList)
      .inSingletonScope()

    container
      .bind('CocktailDemo/ObjectLayout/AddToShoppingList')
      .to(DynamicTypeObjectLayoutAddToShoppingList)
      .inSingletonScope()
  },

  onStartup ({ moduleSystem }) {
    moduleSystem.registerModule(CocktailDemoModule)
  }
}
