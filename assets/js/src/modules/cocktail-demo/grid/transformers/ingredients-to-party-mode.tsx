import React, { type ReactElement } from 'react'
import { injectable } from 'inversify'
import { DynamicTypePipelineAbstract } from '@pimcore/studio-ui-bundle/modules/element'

@injectable()
export class IngredientsToPartyModeTransformer extends DynamicTypePipelineAbstract {
  readonly id = 'ingredientsToPartyMode'
  readonly group = 'party'

  getComponent (): ReactElement {
    return <></>
  }
}
