import React, { forwardRef, useEffect, useRef } from 'react';
import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/build/css/intlTelInput.css';
import { cn } from '../../utils/helpers';

export const PhoneInput = forwardRef(function PhoneInput(
  { label, error, countryCode = 'fr', value, onChange },
  _ref
) {
  const inputRef = useRef(null);
  const itiRef = useRef(null);

  useEffect(() => {
    itiRef.current = intlTelInput(inputRef.current, {
      initialCountry: countryCode,
      loadUtils: () => import('intl-tel-input/utils'),
    });

    const handleChange = () => {
      if (itiRef.current) {
        onChange(itiRef.current.getNumber());
      }
    };

    const input = inputRef.current;
    input.addEventListener('change', handleChange);
    input.addEventListener('keyup', handleChange);

    return () => {
      input.removeEventListener('change', handleChange);
      input.removeEventListener('keyup', handleChange);
      itiRef.current?.destroy();
    };
  }, []);

  useEffect(() => {
    if (itiRef.current && countryCode) {
      itiRef.current.setCountry(countryCode);
    }
  }, [countryCode]);

  useEffect(() => {
    if (itiRef.current && value !== undefined) {
      itiRef.current.setNumber(value || '');
    }
  }, [value]);

  return (
    <div className="w-full">
      {label && <label className="form-label">{label}</label>}
      <input
        ref={inputRef}
        type="tel"
        className={cn(error ? 'form-input-error' : 'form-input', 'w-full')}
      />
      {error && <p className="form-error">{error}</p>}
    </div>
  );
});
