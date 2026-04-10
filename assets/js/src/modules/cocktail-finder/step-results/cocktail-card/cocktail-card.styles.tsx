import { createStyles } from 'antd-style'

export const useStyles = createStyles(({ token, css }) => {
  return {
    card: css`
      margin-bottom: ${token.marginSM}px;
      border-left: 4px solid var(--cocktail-accent, ${token.colorPrimary}) !important;
      border-radius: 0 ${token.borderRadiusLG}px ${token.borderRadiusLG}px 0 !important;
    `,

    titleLink: css`
      color: inherit;
      cursor: pointer;

      &:hover {
        color: ${token.colorPrimary};
        text-decoration: underline;
      }
    `,

    tagRow: css`
      display: flex;
      flex-wrap: wrap;
      gap: ${token.marginXXS}px;
      margin-bottom: ${token.marginSM}px;
    `,

    ingredientRow: css`
      display: flex;
      justify-content: space-between;
      margin-bottom: ${token.marginXXS}px;
    `,

    ingredientList: css`
      list-style: none;
      margin: 0;
      padding: 0;
    `,

    divider: css`
      font-size: ${token.fontSizeSM}px;
      margin: ${token.marginXS}px 0 ${token.marginXXS}px;
    `
  }
})
