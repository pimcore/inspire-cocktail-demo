import { createStyles } from 'antd-style'

export const useAddToShoppingListWidgetStyles = createStyles(({ token, css }) => ({
  controls: css`
    display: flex;
    align-items: center;
    gap: ${token.paddingSM}px;
    flex-wrap: wrap;
  `
}))
