import React from 'react';
import { cn } from '../../utils/helpers';
import {
  CheckCircleIcon,
  ExclamationTriangleIcon,
  XCircleIcon,
  InformationCircleIcon,
  XMarkIcon,
} from '@heroicons/react/24/outline';

const variants = {
  success: {
    className: 'alert-success',
    icon: CheckCircleIcon,
  },
  warning: {
    className: 'alert-warning',
    icon: ExclamationTriangleIcon,
  },
  danger: {
    className: 'alert-danger',
    icon: XCircleIcon,
  },
  info: {
    className: 'alert-info',
    icon: InformationCircleIcon,
  },
};

export function Alert({ 
  children, 
  variant = 'info', 
  title, 
  onClose, 
  className,
  ...props 
}) {
  const { className: variantClassName, icon: Icon } = variants[variant];

  return (
    <div className={cn(variantClassName, className)} role="alert" {...props}>
      <div className="flex">
        <Icon className="h-5 w-5 flex-shrink-0" />
        <div className="ml-3 flex-1">
          {title && <h3 className="font-medium">{title}</h3>}
          <div className={title ? 'mt-1' : ''}>{children}</div>
        </div>
        {onClose && (
          <button
            type="button"
            className="ml-3 flex-shrink-0 rounded hover:opacity-70"
            onClick={onClose}
          >
            <XMarkIcon className="h-5 w-5" />
          </button>
        )}
      </div>
    </div>
  );
}
