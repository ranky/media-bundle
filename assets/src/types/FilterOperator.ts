enum FilterOperator {
  EQUALS = 'eq',
  NOT_EQUALS = 'neq',
  GREATER_THAN = 'gt',
  GREATER_THAN_OR_EQUAL = 'gte',
  LESS_THAN = 'lt',
  LESS_THAN_OR_EQUAL = 'lte',
  LIKE = 'like',
  NOT_LIKE = 'nlike',
  EXACT = 'exact',
  NOT_EXACT = 'nexact',
  STARTS = 'starts',
  ENDS = 'ends',
  INCLUDES = 'in',
  NOT_INCLUDES = 'nin',
}

export default FilterOperator;
