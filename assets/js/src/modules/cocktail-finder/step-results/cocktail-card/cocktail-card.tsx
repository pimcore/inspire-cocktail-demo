import React from 'react'
import { Tag, Typography, Divider } from 'antd'
import { useDataObjectHelper } from '@pimcore/studio-ui-bundle/modules/data-object'
import { isNil } from 'lodash'
import type { Cocktail } from '../../api/cocktail-finder-api'

interface CocktailCardProps {
  cocktail: Cocktail
}

const { Title, Text, Paragraph } = Typography

const formatLabel = (value: string): string => {
  return value.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase())
}

export const CocktailCard = ({ cocktail }: CocktailCardProps): React.JSX.Element => {
  const { openDataObject } = useDataObjectHelper()

  return (
    <div style={ { marginBottom: 24 } }>
      <Title
        level={ 4 }
        style={ { marginBottom: 8, cursor: 'pointer' } }
        onClick={ () => { void openDataObject({ config: { id: cocktail.id } }) } }
      >
        {cocktail.name}
      </Title>

      <div style={ { display: 'flex', flexWrap: 'wrap', gap: 4, marginBottom: 8 } }>
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
        <Paragraph type="secondary">{cocktail.description}</Paragraph>
      )}

      {cocktail.ingredients.length > 0 && (
        <>
          <Divider orientation="left">Ingredients</Divider>

          <table style={ { width: '100%', borderCollapse: 'collapse' } }>
            <tbody>
              {cocktail.ingredients.map((ing, i) => (
                <tr key={ i }>
                  <td style={ { padding: '2px 0' } }>
                    <Text>{ing.name}</Text>
                  </td>

                  <td style={ { padding: '2px 0', textAlign: 'right' } }>
                    <Text type="secondary">
                      {!isNil(ing.amount) ? `${ing.amount} ${ing.unit ?? ''}`.trim() : ''}
                      {!isNil(ing.notes) && ing.notes !== '' ? ` — ${ing.notes}` : ''}
                    </Text>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </>
      )}
    </div>
  )
}
