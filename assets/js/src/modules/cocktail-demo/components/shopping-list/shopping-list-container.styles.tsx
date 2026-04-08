import { createStyles } from 'antd-style'

export const useStyles = createStyles(({ token, css }) => {
  return {
    container: css`
      display: flex;
      flex-direction: column;
      height: 100%;
      overflow: hidden;
    `,

    header: css`
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: ${token.paddingMD}px ${token.paddingLG}px;
      border-bottom: 1px solid ${token.colorBorderSecondary};
      flex-shrink: 0;
    `,

    headerTitle: css`
      display: flex;
      align-items: center;
      gap: ${token.marginSM}px;
      font-size: ${token.fontSizeLG}px;
      font-weight: ${token.fontWeightStrong};
      color: ${token.colorTextHeading};
    `,

    headerCount: css`
      font-size: ${token.fontSize}px;
      font-weight: normal;
      color: ${token.colorTextSecondary};
    `,

    content: css`
      flex: 1;
      overflow-y: auto;
      padding: ${token.paddingLG}px;
    `,

    emptyState: css`
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100%;
      gap: ${token.marginMD}px;
      color: ${token.colorTextSecondary};
    `,

    cocktailSection: css`
      margin-bottom: ${token.marginLG}px;
    `,

    cocktailHeader: css`
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: ${token.marginSM}px;
    `,

    cocktailName: css`
      font-weight: ${token.fontWeightStrong};
      font-size: ${token.fontSize}px;
      color: ${token.colorText};
    `,

    quantityControls: css`
      display: flex;
      align-items: center;
      gap: ${token.marginXS}px;
    `,

    quantityValue: css`
      min-width: 24px;
      text-align: center;
      font-weight: ${token.fontWeightStrong};
    `,

    summarySection: css`
      margin-top: ${token.marginLG}px;
      padding-top: ${token.paddingLG}px;
      border-top: 2px solid ${token.colorBorderSecondary};
    `,

    summaryTitle: css`
      font-size: ${token.fontSizeLG}px;
      font-weight: ${token.fontWeightStrong};
      color: ${token.colorTextHeading};
      margin-bottom: ${token.marginMD}px;
    `
  }
})
