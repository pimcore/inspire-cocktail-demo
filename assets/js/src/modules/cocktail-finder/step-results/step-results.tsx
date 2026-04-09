import React from 'react'
import { Spin, Typography } from 'antd'
import type { Cocktail } from '../api/cocktail-finder-api'
import { CocktailCard } from './cocktail-card/cocktail-card'

interface StepResultsProps {
  cocktails: Cocktail[]
  isLoading: boolean
}

const { Title, Text } = Typography

export const StepResults = ({ cocktails, isLoading }: StepResultsProps): React.JSX.Element => {
  if (isLoading) {
    return (
      <div style={ { padding: '48px 0', textAlign: 'center' } }>
        <Spin size="large" />
      </div>
    )
  }

  if (cocktails.length === 0) {
    return (
      <div style={ { padding: '32px 0', textAlign: 'center' } }>
        <Text type="secondary">No cocktails found for your selection.</Text>
      </div>
    )
  }

  return (
    <div>
      <Title
        level={ 2 }
        style={ { textAlign: 'center', marginBottom: 24 } }
      >
        {cocktails.length === 1 ? 'Your perfect cocktail' : `${cocktails.length} cocktails for you`}
      </Title>

      {cocktails.map((cocktail) => (
        <CocktailCard
          cocktail={ cocktail }
          key={ cocktail.id }
        />
      ))}
    </div>
  )
}
