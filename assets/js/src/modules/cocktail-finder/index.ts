import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import { type IconLibrary } from '@pimcore/studio-ui-bundle/modules/icon-library'
import { CocktailFinderButton } from './cocktail-finder-button/cocktail-finder-button'
import './api/cocktail-finder-api'
import CocktailGlassIcon from './assets/cocktail-glass.inline.svg?react' // eslint-disable-line import/no-unresolved

export const CocktailFinderModule: AbstractModule = {
  onInit: () => {
    const iconLibrary = container.get<IconLibrary>('IconLibrary')
    iconLibrary.register({ name: 'cocktail-glass', component: CocktailGlassIcon })

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const componentRegistry = container.get<any>(
      serviceIds['App/ComponentRegistry/ComponentRegistry']
    )

    componentRegistry.registerToSlot('leftSidebar.slot', {
      name: 'cocktailFinder',
      priority: 300,
      component: CocktailFinderButton
    })
  }
}
