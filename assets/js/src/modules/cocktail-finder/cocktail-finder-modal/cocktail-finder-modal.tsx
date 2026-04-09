import React, { useState } from 'react'
import { Modal, Steps, Button } from 'antd'
import { StepQuestion } from '../step-question/step-question'
import { StepResults } from '../step-results/step-results'
import { useGetOptionsQuery, useGetResultsQuery } from '../api/cocktail-finder-api'

interface CocktailFinderModalProps {
  open: boolean
  onClose: () => void
}

type WizardStep = 'strength' | 'occasion' | 'flavourProfile' | 'results'

const WIZARD_STEPS: WizardStep[] = ['strength', 'occasion', 'flavourProfile', 'results']

const STEP_QUESTIONS: Record<string, string> = {
  strength: 'How much of a kick are you looking for?',
  occasion: "What's the vibe?",
  flavourProfile: 'What flavours speak to your soul?'
}

const STEP_LABELS: Record<string, string> = {
  strength: 'Strength',
  occasion: 'Occasion',
  flavourProfile: 'Flavour',
  results: 'Results'
}

export const CocktailFinderModal = ({ open, onClose }: CocktailFinderModalProps): React.JSX.Element => {
  const [currentStep, setCurrentStep] = useState<WizardStep>('strength')
  const [selections, setSelections] = useState<Record<string, string>>({})

  const currentStepIndex = WIZARD_STEPS.indexOf(currentStep)
  const isResultsStep = currentStep === 'results'

  const { data: optionsData, isFetching: optionsLoading } = useGetOptionsQuery(
    { field: currentStep, ...selections },
    { skip: isResultsStep }
  )

  const { data: resultsData, isFetching: resultsLoading } = useGetResultsQuery(
    selections,
    { skip: !isResultsStep }
  )

  const handleSelect = (value: string): void => {
    const newSelections = { ...selections, [currentStep]: value }
    setSelections(newSelections)
    const nextIndex = currentStepIndex + 1
    setCurrentStep(WIZARD_STEPS[nextIndex])
  }

  const handleStepClick = (stepIndex: number): void => {
    if (stepIndex >= currentStepIndex) return
    const targetStep = WIZARD_STEPS[stepIndex]
    const newSelections = { ...selections }
    WIZARD_STEPS.slice(stepIndex).forEach((s) => {
      // eslint-disable-next-line @typescript-eslint/no-dynamic-delete
      delete newSelections[s]
    })
    setSelections(newSelections)
    setCurrentStep(targetStep)
  }

  const handleBack = (): void => {
    if (currentStepIndex > 0) {
      const prevStep = WIZARD_STEPS[currentStepIndex - 1]
      const newSelections = { ...selections }
      WIZARD_STEPS.slice(currentStepIndex).forEach((s) => {
        // eslint-disable-next-line @typescript-eslint/no-dynamic-delete
        delete newSelections[s]
      })
      setSelections(newSelections)
      setCurrentStep(prevStep)
    }
  }

  const handleClose = (): void => {
    setCurrentStep('strength')
    setSelections({})
    onClose()
  }

  const stepItems = WIZARD_STEPS.slice(0, 3).map((step, i) => ({
    title: STEP_LABELS[step],
    status: (
      i < currentStepIndex
        ? 'finish'
        : i === currentStepIndex && !isResultsStep
          ? 'process'
          : 'wait'
    ) as 'finish' | 'process' | 'wait'
  }))

  return (
    <Modal
      footer={ null }
      onCancel={ handleClose }
      open={ open }
      title="What's Your Vibe?"
      width={ 620 }
    >
      <div style={ { marginBottom: 24 } }>
        <Steps
          current={ Math.min(currentStepIndex, 2) }
          items={ stepItems }
          onChange={ handleStepClick }
          size="small"
        />
      </div>

      {!isResultsStep && (
        <StepQuestion
          isLoading={ optionsLoading }
          onSelect={ handleSelect }
          options={ optionsData?.options ?? [] }
          question={ STEP_QUESTIONS[currentStep] }
        />
      )}

      {isResultsStep && (
        <StepResults
          cocktails={ resultsData?.cocktails ?? [] }
          isLoading={ resultsLoading }
        />
      )}

      {currentStepIndex > 0 && (
        <div style={ { marginTop: 20 } }>
          <Button onClick={ handleBack }>Back</Button>
        </div>
      )}
    </Modal>
  )
}
