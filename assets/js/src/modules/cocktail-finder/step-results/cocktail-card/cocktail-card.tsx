import React from 'react'
import { Tag, Divider, Card } from '@pimcore/studio-ui-bundle/components'
import { useDataObjectHelper } from '@pimcore/studio-ui-bundle/modules/data-object'
import { isNil } from 'lodash'
import type { Cocktail } from '../../api/cocktail-finder-api'
import { useStyles } from './cocktail-card.styles'

interface CocktailCardProps {
  cocktail: Cocktail
}

const STRENGTH_ACCENT: Record<string, string> = {
  low: '#52c41a',
  medium: '#fa8c16',
  strong: '#f5222d'
}

const formatLabel = (value: string): string => {
  return value.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase())
}

export const CocktailCard = ({ cocktail }: CocktailCardProps): React.JSX.Element => {
  const { styles } = useStyles()
  const { openDataObject } = useDataObjectHelper()

  const accent = !isNil(cocktail.strength)
    ? (STRENGTH_ACCENT[cocktail.strength] ?? '#1677ff')
    : '#1677ff'

  const titleNode = (
    <span
      className={ styles.titleLink }
      onClick={ () => { void openDataObject({ config: { id: cocktail.id } }) } }
      role="button"
      tabIndex={ 0 }
      onKeyDown={ (e) => { if (e.key === 'Enter') { void openDataObject({ config: { id: cocktail.id } }) } } }
    >
      {cocktail.name}
    </span>
  )

  return (
    <Card
      className={ styles.card }
      contentPadding="medium"
      style={ { '--cocktail-accent': accent } as React.CSSProperties }
      title={ titleNode }
    >
      <div className={ styles.tagRow }>
        {!isNil(cocktail.strength) && (
          <Tag color="volcano">{formatLabel(cocktail.strength)}</Tag>
        )}

        {!isNil(cocktail.glassType) && (
          <Tag color="blue">{formatLabel(cocktail.glassType)}</Tag>
        )}

        {!isNil(cocktail.preparationMethod) && (
          <Tag color="geekblue">{formatLabel(cocktail.preparationMethod)}</Tag>
        )}

        {cocktail.flavourProfile.map((f) => (
          <Tag
            color="purple"
            key={ f }
          >
            {formatLabel(f)}
          </Tag>
        ))}
      </div>

      {!isNil(cocktail.description) && cocktail.description !== '' && (
        <p>{cocktail.description}</p>
      )}

      {cocktail.ingredients.length > 0 && (
        <>
          <Divider
            className={ styles.divider }
            orientation="left"
          >
            Ingredients
          </Divider>

          <ul className={ styles.ingredientList }>
            {cocktail.ingredients.map((ing, i) => (
              <li
                className={ styles.ingredientRow }
                key={ i }
              >
                <span>{ing.name}</span>

                <span>
                  {!isNil(ing.amount) ? `${ing.amount} ${ing.unit ?? ''}`.trim() : ''}
                  {!isNil(ing.notes) && ing.notes !== '' ? ` — ${ing.notes}` : ''}
                </span>
              </li>
            ))}
          </ul>
        </>
      )}
    </Card>
  )
}
