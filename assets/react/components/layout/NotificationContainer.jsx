import React from 'react';
import { Transition } from '@headlessui/react';
import {
  CheckCircleIcon,
  ExclamationTriangleIcon,
  XCircleIcon,
  InformationCircleIcon,
  XMarkIcon,
} from '@heroicons/react/24/outline';
import { useNotification } from '../../hooks/useNotification';
import { cn } from '../../utils/helpers';

const icons = {
  success: CheckCircleIcon,
  warning: ExclamationTriangleIcon,
  error: XCircleIcon,
  info: InformationCircleIcon,
};

const colors = {
  success: 'bg-success-50 text-success-700 border-success-500',
  warning: 'bg-warning-50 text-warning-700 border-warning-500',
  error: 'bg-danger-50 text-danger-700 border-danger-500',
  info: 'bg-primary-50 text-primary-700 border-primary-500',
};

export function NotificationContainer() {
  const { notifications, removeNotification } = useNotification();

  return (
    <div
      aria-live="assertive"
      className="fixed inset-0 flex items-end px-4 py-6 pointer-events-none sm:p-6 sm:items-start z-50"
    >
      <div className="w-full flex flex-col items-center space-y-4 sm:items-end">
        {notifications.map((notification) => {
          const Icon = icons[notification.type];
          
          return (
            <Transition
              key={notification.id}
              show={true}
              enter="transform ease-out duration-300 transition"
              enterFrom="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
              enterTo="translate-y-0 opacity-100 sm:translate-x-0"
              leave="transition ease-in duration-100"
              leaveFrom="opacity-100"
              leaveTo="opacity-0"
            >
              <div
                className={cn(
                  'max-w-sm w-full shadow-lg rounded-lg pointer-events-auto border-l-4 overflow-hidden',
                  colors[notification.type]
                )}
              >
                <div className="p-4">
                  <div className="flex items-start">
                    <div className="flex-shrink-0">
                      <Icon className="h-6 w-6" />
                    </div>
                    <div className="ml-3 w-0 flex-1">
                      {notification.title && (
                        <p className="font-medium">{notification.title}</p>
                      )}
                      {notification.message && (
                        <p className={cn('text-sm', notification.title && 'mt-1')}>
                          {notification.message}
                        </p>
                      )}
                    </div>
                    <div className="ml-4 flex-shrink-0 flex">
                      <button
                        className="rounded-md inline-flex hover:opacity-70"
                        onClick={() => removeNotification(notification.id)}
                      >
                        <XMarkIcon className="h-5 w-5" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </Transition>
          );
        })}
      </div>
    </div>
  );
}
