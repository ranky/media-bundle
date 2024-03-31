import React from 'react';

type ButtonRadioFilterType = {
  name: string;
  value: string;
  checkedValue: string;
  label: string;
  type: string;
  icon: React.ReactElement;
};

const ButtonRadioFilter: React.FC<ButtonRadioFilterType> = ({
  name, value, checkedValue = false, label, type, icon,
}): React.ReactElement => {
  return (
    <div
      role="radio"
      tabIndex={0}
      aria-checked={checkedValue === value}
      className={`${type}-filter-item ${type}-filter-item--${value.toLocaleLowerCase()}`}
    >
      <label
        title={label}
        htmlFor={`${type}-filter-item--${value.toLocaleLowerCase()}`}
      >
        <input
          tabIndex={-1}
          aria-label={label}
          type="radio"
          name={name}
          value={value}
          id={`${type}-filter-item--${value.toLocaleLowerCase()}`}
          defaultChecked={checkedValue === value}
        />
        <span>{icon}</span>
      </label>
    </div>
  );
};

export default ButtonRadioFilter;
