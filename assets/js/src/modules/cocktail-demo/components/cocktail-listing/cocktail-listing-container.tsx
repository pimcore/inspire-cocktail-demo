import React from 'react'
import { useInjection } from '@pimcore/studio-ui-bundle/app'
import { BaseListing, listingDefaultProps, DataObjectProvider } from '@pimcore/studio-ui-bundle/modules/data-object'
import { type ListingBuilder } from '@pimcore/studio-ui-bundle/modules/element'
import { withAddToShoppingListTab } from './components/add-to-shopping-list-sidebar/with-add-to-shopping-list-tab'

const COCKTAIL_CLASS_NAME = 'Cocktail'
const PIMCORE_ROOT_ID = 1

export const CocktailListingContainer = (): React.JSX.Element => {
  const listingBuilder = useInjection<ListingBuilder>(
    'DataObject/Listing/Builder'
  )

  const builtProps = listingBuilder.build({
    props: listingDefaultProps,
    config: {
      classDefinitionSelection: {
        config: {
          classRestriction: [{ classes: COCKTAIL_CLASS_NAME }],
          showConfigLayer: false
        }
      }
    }
  })

  return (
    <DataObjectProvider id={ PIMCORE_ROOT_ID }>
      <BaseListing
        { ...builtProps }
        useSidebarOptions={ withAddToShoppingListTab(builtProps.useSidebarOptions) }
      />
    </DataObjectProvider>
  )
}
