import { createStyles } from 'antd-style'

export const useStyles = createStyles(({ token, css }) => {
  return {
    stepsWrapper: css`
      margin-bottom: ${token.marginLG}px;
    `,

    backWrapper: css`
      margin-top: ${token.marginMD}px;
    `
  }
})
