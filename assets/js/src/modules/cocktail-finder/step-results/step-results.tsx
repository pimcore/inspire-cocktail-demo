import React from 'react'
import { Spin, Space } from '@pimcore/studio-ui-bundle/components'
import type { Cocktail } from '../api/cocktail-finder-api'
import { CocktailCard } from './cocktail-card/cocktail-card'
import { useStyles } from './step-results.styles'

interface StepResultsProps {
  cocktails: Cocktail[]
  isLoading: boolean
}

export const StepResults = ({ cocktails, isLoading }: StepResultsProps): React.JSX.Element => {
  const { styles } = useStyles()

  if (isLoading) {
    return (
      <div className={ styles.loadingWrapper }>
        <Spin size="large" />
      </div>
    )
  }

  if (cocktails.length === 0) {
    return (
      <div className={ styles.emptyWrapper }>
        <span>No cocktails found for your selection.</span>
      </div>
    )
  }

  return (
    <div>
      <h3 className={ styles.heading }>
        {cocktails.length === 1 ? 'Your perfect cocktail' : `${cocktails.length} cocktails for you`}
      </h3>

      <Space
        direction="vertical"
        size="small"
        style={ { width: '100%' } }
      >
        {cocktails.map((cocktail) => (
          <CocktailCard
            cocktail={ cocktail }
            key={ cocktail.id }
          />
        ))}
      </Space>
    </div>
  )
}
