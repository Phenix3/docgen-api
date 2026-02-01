import React from 'react';
import { Outlet, Link } from 'react-router-dom';

export default function AuthLayout() {
  return (
    <div className="min-h-screen flex">
      {/* Left side - Branding */}
      <div className="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary-600 to-primary-800 p-12 flex-col justify-between">
        <div>
          <Link to="/" className="flex items-center">
            <span className="text-3xl font-bold text-white">DocGen</span>
            <span className="text-3xl font-light text-primary-200 ml-2">Africa</span>
          </Link>
        </div>
        
        <div className="space-y-6">
          <h1 className="text-4xl font-bold text-white leading-tight">
            Générez vos documents administratifs en quelques clics
          </h1>
          <p className="text-xl text-primary-100">
            Une solution simple et rapide pour tous vos courriers officiels
          </p>
          
          <div className="flex items-center gap-8 pt-8">
            <div className="text-center">
              <p className="text-3xl font-bold text-white">10+</p>
              <p className="text-sm text-primary-200">Types de documents</p>
            </div>
            <div className="text-center">
              <p className="text-3xl font-bold text-white">3</p>
              <p className="text-sm text-primary-200">Pays supportés</p>
            </div>
            <div className="text-center">
              <p className="text-3xl font-bold text-white">100%</p>
              <p className="text-sm text-primary-200">Gratuit pour commencer</p>
            </div>
          </div>
        </div>
        
        <p className="text-sm text-primary-200">
          © 2026 DocGen Africa. Tous droits réservés.
        </p>
      </div>

      {/* Right side - Form */}
      <div className="flex-1 flex items-center justify-center p-8 bg-secondary-50">
        <div className="w-full max-w-md">
          {/* Mobile logo */}
          <div className="lg:hidden text-center mb-8">
            <Link to="/" className="inline-flex items-center">
              <span className="text-2xl font-bold text-primary-600">DocGen</span>
              <span className="text-2xl font-light text-secondary-600 ml-1">Africa</span>
            </Link>
          </div>
          
          <Outlet />
        </div>
      </div>
    </div>
  );
}
