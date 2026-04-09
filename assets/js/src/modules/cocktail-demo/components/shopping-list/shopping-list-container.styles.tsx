import { createStyles } from 'antd-style'

export const useStyles = createStyles(({ token, css }) => {
  return {
    cocktailRow: css`
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: ${token.marginSM}px;
      padding: ${token.paddingXS}px 0;
    `,

    cocktailName: css`
      flex: 1;
      min-width: 0;
      font-weight: ${token.fontWeightStrong};
      font-size: ${token.fontSize}px;
      color: ${token.colorText};
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    `,

    cocktailControls: css`
      display: flex;
      align-items: center;
      gap: ${token.marginXS}px;
      flex-shrink: 0;
    `
  }
})
