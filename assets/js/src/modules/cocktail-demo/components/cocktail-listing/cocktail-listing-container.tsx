import React from 'react'
import { useInjection } from '@pimcore/studio-ui-bundle/app'
import { BaseListing, listingDefaultProps } from '@pimcore/studio-ui-bundle/modules/data-object'
import { type ListingBuilder } from '@pimcore/studio-ui-bundle/modules/element'

const COCKTAIL_CLASS_NAME = 'Cocktail'

export const CocktailListingContainer = (): React.JSX.Element => {
  const listingBuilder = useInjection<ListingBuilder>(
    'DataObject/Listing/Builder'
  )

  return (
    <BaseListing
      { ...listingBuilder.build({
        props: listingDefaultProps,
        config: {
          classDefinitionSelection: {
            config: {
              classRestriction: [{ classes: COCKTAIL_CLASS_NAME }],
              showConfigLayer: false,
              isResolvingClassDefinitionsBasedOnElementId: false
            }
          }
        }
      }) }
    />
  )
}
