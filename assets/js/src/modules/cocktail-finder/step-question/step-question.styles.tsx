import { createStyles } from 'antd-style'

export const useStyles = createStyles(({ token, css }) => {
  return {
    wrapper: css`
      padding: ${token.paddingXS}px 0;
    `,

    question: css`
      margin-bottom: ${token.marginXL}px;
      text-align: center;
      font-size: ${token.fontSizeHeading3}px;
    `,

    grid: css`
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: ${token.marginMD}px;
    `,

    optionCard: css`
      cursor: pointer;
      text-align: center;
      transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;

      &:hover {
        border-color: ${token.colorPrimary} !important;
        box-shadow: ${token.boxShadow};
        transform: translateY(-2px);
      }
    `,

    optionLabel: css`
      display: block;
      font-size: ${token.fontSizeLG}px;
      font-weight: ${token.fontWeightStrong};
      margin-bottom: ${token.marginXXS}px;
    `,

    optionCount: css`
      color: ${token.colorTextSecondary};
      font-size: ${token.fontSizeSM}px;
    `,

    loadingWrapper: css`
      padding: 48px 0;
      text-align: center;
    `
  }
})
