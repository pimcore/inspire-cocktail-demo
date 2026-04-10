import React from 'react'
import { Spin, Card } from '@pimcore/studio-ui-bundle/components'
import type { FiniderOption } from '../api/cocktail-finder-api'
import { useStyles } from './step-question.styles'

interface StepQuestionProps {
  question: string
  options: FiniderOption[]
  isLoading: boolean
  onSelect: (value: string) => void
}

export const StepQuestion = ({ question, options, isLoading, onSelect }: StepQuestionProps): React.JSX.Element => {
  const { styles } = useStyles()

  if (isLoading) {
    return (
      <div className={ styles.loadingWrapper }>
        <Spin size="large" />
      </div>
    )
  }

  return (
    <div className={ styles.wrapper }>
      <h3 className={ styles.question }>
        {question}
      </h3>

      <div className={ styles.grid }>
        {options.map((option) => (
          <Card
            className={ styles.optionCard }
            contentPadding="medium"
            key={ option.value }
            onClick={ () => { onSelect(option.value) } }
          >
            <span className={ styles.optionLabel }>
              {option.label}
            </span>

            <span className={ styles.optionCount }>
              {option.count} {option.count === 1 ? 'cocktail' : 'cocktails'}
            </span>
          </Card>
        ))}
      </div>
    </div>
  )
}
