import React, { useState } from 'react'
import { IconButton } from '@pimcore/studio-ui-bundle/components'
import { CocktailFinderModal } from '../cocktail-finder-modal/cocktail-finder-modal'

export const CocktailFinderButton = (): React.JSX.Element => {
  const [open, setOpen] = useState(false)

  return (
    <>
      <IconButton
        icon={ { value: 'cocktail-finder' } }
        onClick={ () => { setOpen(true) } }
        tooltip={ { title: 'Find my cocktail' } }
        type="text"
      />

      <CocktailFinderModal
        onClose={ () => { setOpen(false) } }
        open={ open }
      />
    </>
  )
}
