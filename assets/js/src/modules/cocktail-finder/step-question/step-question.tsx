import React from 'react'
import { Spin, Typography } from 'antd'
import type { FiniderOption } from '../api/cocktail-finder-api'

interface StepQuestionProps {
  question: string
  options: FiniderOption[]
  isLoading: boolean
  onSelect: (value: string) => void
}

const { Title, Text } = Typography

export const StepQuestion = ({ question, options, isLoading, onSelect }: StepQuestionProps): React.JSX.Element => {
  if (isLoading) {
    return (
      <div style={ { padding: '48px 0', textAlign: 'center' } }>
        <Spin size="large" />
      </div>
    )
  }

  return (
    <div style={ { padding: '8px 0' } }>
      <Title
        level={ 2 }
        style={ { textAlign: 'center', marginBottom: 32 } }
      >
        {question}
      </Title>

      <div style={ { display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(160px, 1fr))', gap: 16 } }>
        {options.map((option) => (
          <div
            key={ option.value }
            onClick={ () => { onSelect(option.value) } }
            style={ { textAlign: 'center', cursor: 'pointer', padding: '8px 0' } }
          >
            <Text strong style={ { display: 'block', fontSize: 14 } }>
              {option.label}
            </Text>

            <Text
              style={ { fontSize: 12 } }
              type="secondary"
            >
              {option.count} {option.count === 1 ? 'cocktail' : 'cocktails'}
            </Text>
          </div>
        ))}
      </div>
    </div>
  )
}
