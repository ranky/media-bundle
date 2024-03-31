import React from 'react';
import FilterOperator from '@rankyMedia/types/FilterOperator';
import { Filter } from '@rankyMedia/types/Filters';

type SelectFilterType = {
  name: string;
  operator: FilterOperator;
  data: Filter[];
  defaultOption?: { value: string, label: string };
};

const SelectFilter = ({
  name, operator, data, defaultOption = { value: 'all', label: 'Todos los tipos' },
}: SelectFilterType): React.ReactElement => {
  return (
    <div className={`${name.toLocaleLowerCase()}-filter`}>
      <select name={`filters[${name}][${operator}]`} id={`${name.toLocaleLowerCase()}-select-filter`}>
        <option value={defaultOption.value} key={defaultOption.value}>{defaultOption.label}</option>
        {data.map((filter) => {
          return (<option value={filter.value} key={filter.value}>{filter.label}</option>);
        })}
      </select>
    </div>
  );
};

/* SelectFilter.defaultProps = {
  defaultOption: { value: 'all', label: 'Todos los tipos' },
}; */

export default SelectFilter;
