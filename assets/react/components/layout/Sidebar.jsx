import React from 'react';
import { NavLink } from 'react-router-dom';
import {
  HomeIcon,
  DocumentTextIcon,
  PlusCircleIcon,
  UserCircleIcon,
} from '@heroicons/react/24/outline';
import { cn } from '../../utils/helpers';
import { useAuth } from '../../hooks/useAuth';

const navigation = [
  { name: 'Tableau de bord', href: '/dashboard', icon: HomeIcon },
  { name: 'Mes documents', href: '/documents', icon: DocumentTextIcon },
  { name: 'Nouveau document', href: '/documents/new', icon: PlusCircleIcon },
  { name: 'Mon profil', href: '/profile', icon: UserCircleIcon },
];

export function Sidebar({ isOpen, onClose }) {
  const { user } = useAuth();

  return (
    <>
      {/* Backdrop */}
      {isOpen && (
        <div
          className="fixed inset-0 bg-black/50 z-40 lg:hidden"
          onClick={onClose}
        />
      )}

      {/* Sidebar */}
      <aside
        className={cn(
          'fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-secondary-200 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:z-0',
          isOpen ? 'translate-x-0' : '-translate-x-full'
        )}
      >
        <div className="flex flex-col h-full">
          {/* Logo */}
          <div className="flex items-center h-16 px-6 border-b border-secondary-200 lg:hidden">
            <span className="text-xl font-bold text-primary-600">DocGen</span>
            <span className="text-xl font-light text-secondary-600 ml-1">Africa</span>
          </div>

          {/* Navigation */}
          <nav className="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            {navigation.map((item) => (
              <NavLink
                key={item.name}
                to={item.href}
                onClick={onClose}
                className={({ isActive }) =>
                  cn(
                    'flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors',
                    isActive
                      ? 'bg-primary-50 text-primary-700'
                      : 'text-secondary-600 hover:bg-secondary-100'
                  )
                }
              >
                <item.icon className="h-5 w-5" />
                {item.name}
              </NavLink>
            ))}
          </nav>

          {/* Quota info */}
          <div className="p-4 border-t border-secondary-200">
            <div className="p-4 rounded-lg bg-secondary-50">
              <p className="text-sm font-medium text-secondary-900">
                Documents ce mois
              </p>
              <div className="mt-2 flex items-baseline gap-2">
                <span className="text-2xl font-bold text-primary-600">
                  {user?.quota?.used || 0}
                </span>
                <span className="text-sm text-secondary-500">
                  / {user?.quota?.limit || 3}
                </span>
              </div>
              <div className="mt-2 h-2 bg-secondary-200 rounded-full overflow-hidden">
                <div
                  className="h-full bg-primary-600 rounded-full transition-all"
                  style={{
                    width: `${Math.min(
                      100,
                      ((user?.quota?.used || 0) / (user?.quota?.limit || 3)) * 100
                    )}%`,
                  }}
                />
              </div>
            </div>
          </div>
        </div>
      </aside>
    </>
  );
}
