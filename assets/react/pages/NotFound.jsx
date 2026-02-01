import React from 'react';
import { Link } from 'react-router-dom';
import { Button } from '../components/common';

export default function NotFound() {
  return (
    <div className="min-h-[60vh] flex flex-col items-center justify-center text-center px-4">
      <h1 className="text-9xl font-bold text-primary-100">404</h1>
      <h2 className="text-3xl font-bold text-secondary-900 mt-4">Page introuvable</h2>
      <p className="text-secondary-600 mt-4 max-w-md">
        Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
      </p>
      <Link to="/" className="mt-8">
        <Button size="lg">Retour à l'accueil</Button>
      </Link>
    </div>
  );
}
