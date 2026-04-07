# Pimcore Inspire Cocktail Demo

A Pimcore Studio demo bundle with sample cocktail and ingredient data objects.

## Setup

```bash
bin/console cocktail-demo:setup
```

Creates the `Cocktail` and `Ingredient` class definitions, Select Options, and sample data objects under `/Cocktail Demo`.

## Teardown

```bash
bin/console cocktail-demo:teardown
```

Removes all objects, class definitions, and Select Options created by setup. Use `--force` to skip the confirmation prompt.
