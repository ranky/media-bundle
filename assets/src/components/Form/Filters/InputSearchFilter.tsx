import React from 'react';
import FilterOperator from '@rankyMedia/types/FilterOperator';

type InputSearchFilterType = {
  name: string;
  placeholder: string;
};

const InputSearchFilter: React.FC<InputSearchFilterType> = ({ name, placeholder }): React.ReactElement => {
  return (
    <div className="search-filter">
      <input
        name={`filters[${name}][${FilterOperator.LIKE}]`}
        aria-label={placeholder}
        placeholder={placeholder}
        type="search"
        id="search-input-filter"
      />
    </div>
  );
};

export default InputSearchFilter;
