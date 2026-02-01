import React, { forwardRef } from 'react';
import { cn } from '../../utils/helpers';

export const Select = forwardRef(function Select(
  { label, error, helpText, options = [], placeholder, className, ...props },
  ref
) {
  return (
    <div className="w-full">
      {label && (
        <label className="form-label">
          {label}
          {props.required && <span className="text-danger-500 ml-1">*</span>}
        </label>
      )}
      <select
        ref={ref}
        className={cn(
          error ? 'form-input-error' : 'form-input',
          'pr-10',
          className
        )}
        {...props}
      >
        {placeholder && (
          <option value="">{placeholder}</option>
        )}
        {options.map((option) => (
          <option key={option.value} value={option.value}>
            {option.label}
          </option>
        ))}
      </select>
      {error && <p className="form-error">{error}</p>}
      {helpText && !error && <p className="form-help">{helpText}</p>}
    </div>
  );
});
