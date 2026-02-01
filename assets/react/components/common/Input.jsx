import React, { forwardRef } from 'react';
import { cn } from '../../utils/helpers';

export const Input = forwardRef(function Input(
  { label, error, helpText, className, ...props },
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
      <input
        ref={ref}
        className={cn(
          error ? 'form-input-error' : 'form-input',
          className
        )}
        {...props}
      />
      {error && <p className="form-error">{error}</p>}
      {helpText && !error && <p className="form-help">{helpText}</p>}
    </div>
  );
});
