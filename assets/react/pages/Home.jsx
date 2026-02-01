import React from 'react';
import { Link } from 'react-router-dom';
import { Button } from '../components/common';
import { useAuth } from '../hooks/useAuth';

export default function Home() {
  const { isAuthenticated } = useAuth();

  return (
    <div className="min-h-screen bg-white">
      {/* Hero Section */}
      <div className="relative overflow-hidden bg-secondary-50">
        <div className="max-w-7xl mx-auto">
          <div className="relative z-10 pb-8 bg-secondary-50 sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
            <main className="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
              <div className="sm:text-center lg:text-left">
                <h1 className="text-4xl tracking-tight font-extrabold text-secondary-900 sm:text-5xl md:text-6xl">
                  <span className="block xl:inline">Simplifiez vos</span>{' '}
                  <span className="block text-primary-600 xl:inline">démarches administratives</span>
                </h1>
                <p className="mt-3 text-base text-secondary-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                  Générez automatiquement des documents administratifs conformes aux normes locales. 
                  CV, lettres de motivation, demandes officielles et plus encore.
                </p>
                <div className="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                  <div className="rounded-md shadow">
                    <Link to={isAuthenticated ? "/dashboard" : "/register"}>
                      <Button size="lg" className="w-full sm:w-auto">
                        Commencer gratuitement
                      </Button>
                    </Link>
                  </div>
                  <div className="mt-3 sm:mt-0 sm:ml-3">
                    <Link to="/templates">
                      <Button variant="secondary" size="lg" className="w-full sm:w-auto">
                        Voir les modèles
                      </Button>
                    </Link>
                  </div>
                </div>
              </div>
            </main>
          </div>
        </div>
        <div className="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
          <div className="h-56 w-full bg-primary-100 sm:h-72 md:h-96 lg:w-full lg:h-full flex items-center justify-center">
            <span className="text-primary-300 text-9xl">📄</span>
          </div>
        </div>
      </div>
    </div>
  );
}
