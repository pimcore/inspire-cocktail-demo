import { type ConfigFile } from '@rtk-query/codegen-openapi'
import type { EndpointMatcherFunction } from '@rtk-query/codegen-openapi'

const pathMatcher = (pattern: RegExp): EndpointMatcherFunction => {
  return (name, definition) => {
    return pattern.test(definition.path)
  }
}

const config: ConfigFile = {
  schemaFile: './docs.jsonopenapi.json',
  apiFile: '@pimcore/studio-ui-bundle/api',
  apiImport: 'api',
  outputFiles: {
    '../../js/src/modules/cocktail-demo/api/cocktail-demo-api-slice.gen.ts': {
      filterEndpoints: pathMatcher(/bundle\/inspire-cocktail-demo/i)
    },
  },
  exportName: 'api',
  hooks: true,
  tag: true,
  endpointOverrides: [
    {
      pattern: 'bundleInspireCocktailDemoShoppingListCalculate',
      type: 'query',
    },
  ],
}

export default config
