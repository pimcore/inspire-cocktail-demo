import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import { type DynamicTypePipelineRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import { IngredientsToPartyModeTransformer } from '../grid/transformers/ingredients-to-party-mode'

const TRANSFORMER_SERVICE_ID = 'CocktailDemo/Grid/Transformers/IngredientsToPartyMode'

export const CocktailDemoModule: AbstractModule = {
  onInit: () => {
    container.bind(TRANSFORMER_SERVICE_ID).to(IngredientsToPartyModeTransformer).inSingletonScope()

    const transformersRegistry = container.get<DynamicTypePipelineRegistry>(
      serviceIds['DynamicTypes/Grid/TransformersRegistry']
    )

    transformersRegistry.registerDynamicType(
      container.get<IngredientsToPartyModeTransformer>(TRANSFORMER_SERVICE_ID)
    )
  }
}
