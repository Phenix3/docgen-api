import React from 'react';
import { useAuth } from '../hooks/useAuth';
import { Card, CardHeader, CardBody, Button, Input } from '../components/common';

export default function Profile() {
  const { user, logout } = useAuth();

  return (
    <div className="max-w-4xl mx-auto space-y-6">
      <h1 className="text-2xl font-bold text-secondary-900">Mon profil</h1>

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold">Informations personnelles</h2>
        </CardHeader>
        <CardBody className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label className="block text-sm font-medium text-secondary-700">Nom complet</label>
              <div className="mt-1 p-3 bg-secondary-50 rounded-lg text-secondary-900 border border-secondary-200">
                {user?.name}
              </div>
            </div>
            <div>
              <label className="block text-sm font-medium text-secondary-700">Email</label>
              <div className="mt-1 p-3 bg-secondary-50 rounded-lg text-secondary-900 border border-secondary-200">
                {user?.email}
              </div>
            </div>
            <div>
              <label className="block text-sm font-medium text-secondary-700">Pays</label>
              <div className="mt-1 p-3 bg-secondary-50 rounded-lg text-secondary-900 border border-secondary-200">
                {user?.country?.name || 'Non défini'}
              </div>
            </div>
            <div>
              <label className="block text-sm font-medium text-secondary-700">Téléphone</label>
              <div className="mt-1 p-3 bg-secondary-50 rounded-lg text-secondary-900 border border-secondary-200">
                {user?.phone || 'Non renseigné'}
              </div>
            </div>
          </div>
        </CardBody>
      </Card>

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold">Abonnement et Quota</h2>
        </CardHeader>
        <CardBody>
          <div className="flex items-center justify-between p-4 bg-primary-50 rounded-lg border border-primary-100">
            <div>
              <p className="font-semibold text-primary-900 capitalize">
                Plan {user?.subscriptionType || 'Gratuit'}
              </p>
              <p className="text-sm text-primary-700 mt-1">
                {user?.quota?.used || 0} / {user?.quota?.limit || 3} documents générés ce mois
              </p>
            </div>
            <Button variant="primary">Gérer mon abonnement</Button>
          </div>
        </CardBody>
      </Card>

      <div className="flex justify-end">
        <Button variant="danger" variantType="outline" onClick={logout}>
          Se déconnecter
        </Button>
      </div>
    </div>
  );
}
