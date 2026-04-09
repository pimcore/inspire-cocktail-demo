import { createStyles } from 'antd-style'

export const useStyles = createStyles(({ token, css }) => {
  return {
    loadingWrapper: css`
      padding: 48px 0;
      text-align: center;
    `,

    emptyWrapper: css`
      padding: ${token.paddingXL}px 0;
      text-align: center;
    `,

    heading: css`
      margin-bottom: ${token.marginMD}px;
      text-align: center;
      font-size: ${token.fontSizeHeading3}px;
    `
  }
})
