import React from 'react';
import { Link } from 'react-router-dom';
import { Menu, Transition } from '@headlessui/react';
import {
  Bars3Icon,
  UserCircleIcon,
  ArrowRightOnRectangleIcon,
  Cog6ToothIcon,
} from '@heroicons/react/24/outline';
import { useAuth } from '../../hooks/useAuth';
import { getInitials, cn } from '../../utils/helpers';

export function Header({ onMenuClick }) {
  const { user, logout } = useAuth();

  return (
    <header className="sticky top-0 z-40 bg-white border-b border-secondary-200">
      <div className="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        {/* Left side */}
        <div className="flex items-center">
          <button
            type="button"
            className="p-2 rounded-lg text-secondary-500 hover:bg-secondary-100 lg:hidden"
            onClick={onMenuClick}
          >
            <Bars3Icon className="h-6 w-6" />
          </button>
          
          <Link to="/dashboard" className="flex items-center ml-2 lg:ml-0">
            <span className="text-xl font-bold text-primary-600">DocGen</span>
            <span className="text-xl font-light text-secondary-600 ml-1">Africa</span>
          </Link>
        </div>

        {/* Right side - User menu */}
        <Menu as="div" className="relative">
          <Menu.Button className="flex items-center gap-3 p-2 rounded-lg hover:bg-secondary-100">
            <div className="hidden sm:block text-right">
              <p className="text-sm font-medium text-secondary-900">{user?.name}</p>
              <p className="text-xs text-secondary-500">{user?.email}</p>
            </div>
            <div className="h-9 w-9 rounded-full bg-primary-600 flex items-center justify-center text-white font-medium">
              {getInitials(user?.name)}
            </div>
          </Menu.Button>

          <Transition
            enter="transition ease-out duration-100"
            enterFrom="transform opacity-0 scale-95"
            enterTo="transform opacity-100 scale-100"
            leave="transition ease-in duration-75"
            leaveFrom="transform opacity-100 scale-100"
            leaveTo="transform opacity-0 scale-95"
          >
            <Menu.Items className="absolute right-0 mt-2 w-56 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black/5 focus:outline-none">
              <div className="p-1">
                <Menu.Item>
                  {({ active }) => (
                    <Link
                      to="/profile"
                      className={cn(
                        'flex items-center gap-2 px-4 py-2 text-sm rounded-lg',
                        active ? 'bg-secondary-100' : ''
                      )}
                    >
                      <UserCircleIcon className="h-5 w-5 text-secondary-500" />
                      Mon profil
                    </Link>
                  )}
                </Menu.Item>
                <Menu.Item>
                  {({ active }) => (
                    <Link
                      to="/profile"
                      className={cn(
                        'flex items-center gap-2 px-4 py-2 text-sm rounded-lg',
                        active ? 'bg-secondary-100' : ''
                      )}
                    >
                      <Cog6ToothIcon className="h-5 w-5 text-secondary-500" />
                      Paramètres
                    </Link>
                  )}
                </Menu.Item>
                <hr className="my-1 border-secondary-200" />
                <Menu.Item>
                  {({ active }) => (
                    <button
                      onClick={logout}
                      className={cn(
                        'flex items-center gap-2 w-full px-4 py-2 text-sm rounded-lg text-danger-600',
                        active ? 'bg-danger-50' : ''
                      )}
                    >
                      <ArrowRightOnRectangleIcon className="h-5 w-5" />
                      Déconnexion
                    </button>
                  )}
                </Menu.Item>
              </div>
            </Menu.Items>
          </Transition>
        </Menu>
      </div>
    </header>
  );
}
