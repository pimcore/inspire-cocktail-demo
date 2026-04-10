import { Form, FormKit, InputNumber } from '@pimcore/studio-ui-bundle/components'
import React from 'react'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'

export const AddToShoppingListFormFields = (): React.JSX.Element => {
  const { t } = useTranslation()

  return (
    <FormKit.Panel title={ t('cocktail-demo.field-definition.add-to-shopping-list.settings') }>
      <Form.Item
        label={ t('cocktail-demo.field-definition.add-to-shopping-list.default-amount') }
        name="defaultAmount"
      >
        <InputNumber
          min={ 1 }
          precision={ 0 }
        />
      </Form.Item>
    </FormKit.Panel>
  )
}
