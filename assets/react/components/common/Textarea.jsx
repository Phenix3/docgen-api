import React, { forwardRef } from 'react';
import { cn } from '../../utils/helpers';

export const Textarea = forwardRef(function Textarea(
  { label, error, helpText, className, rows = 4, ...props },
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
      <textarea
        ref={ref}
        rows={rows}
        className={cn(
          error ? 'form-input-error' : 'form-input',
          'resize-none',
          className
        )}
        {...props}
      />
      {error && <p className="form-error">{error}</p>}
      {helpText && !error && <p className="form-help">{helpText}</p>}
    </div>
  );
});
