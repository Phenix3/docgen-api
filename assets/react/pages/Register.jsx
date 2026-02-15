import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { useAuth } from '../hooks/useAuth';
import { useNotification } from '../hooks/useNotification';
import { useCountries } from '../hooks/useTemplates';
import { registerSchema } from '../utils/validators';
import { Button, Input, Select, Alert } from '../components/common';

export default function Register() {
  const navigate = useNavigate();
  const { register: registerUser } = useAuth();
  const { success } = useNotification();
  const { data: countries = [], isLoading: loadingCountries } = useCountries();

  const {
    register,
    handleSubmit,
    setError,
    formState: { errors, isSubmitting },
  } = useForm({
    resolver: zodResolver(registerSchema),
  });

  const onSubmit = async (data) => {
    // Convert countryId to number
    data.countryId = parseInt(data.countryId, 10);

    const result = await registerUser(data);

    if (result.success) {
      success('Compte créé', 'Vous pouvez maintenant vous connecter');
      navigate('/login');
    } else {
      setError('root', {
        type: 'manual',
        message: result.error,
      });
    }
  };

  const countryOptions = countries.map(c => ({
    value: c.id,
    label: c.name
  }));

  return (
    <div className="w-full">
      <h2 className="text-2xl font-bold text-secondary-900 mb-2">
        Créer un compte
      </h2>
      <p className="text-secondary-600 mb-8">
        Rejoignez DocGen Africa pour créer vos documents
      </p>

      {errors.root && (
        <Alert variant="danger" className="mb-6">
          {errors.root.message}
        </Alert>
      )}

      <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
        <Input
          label="Nom complet"
          type="text"
          autoComplete="name"
          placeholder="Jean Dupont"
          error={errors.name?.message}
          {...register('name')}
        />

        <Input
          label="Email"
          type="email"
          autoComplete="email"
          placeholder="votre@email.com"
          error={errors.email?.message}
          {...register('email')}
        />

        <Input
          label="Téléphone (optionnel)"
          type="tel"
          autoComplete="tel"
          placeholder="+237 600000000"
          error={errors.phone?.message}
          {...register('phone')}
        />

        <Select
          label="Pays"
          options={countryOptions}
          placeholder={loadingCountries ? "Chargement..." : "Sélectionnez votre pays"}
          error={errors.countryId?.message}
          disabled={loadingCountries}
          {...register('countryId')}
        />

        <Input
          label="Mot de passe"
          type="password"
          autoComplete="new-password"
          placeholder="••••••••"
          error={errors.password?.message}
          {...register('password')}
        />

        <Input
          label="Confirmer le mot de passe"
          type="password"
          autoComplete="new-password"
          placeholder="••••••••"
          error={errors.confirmPassword?.message}
          {...register('confirmPassword')}
        />

        <Button
          type="submit"
          className="w-full"
          isLoading={isSubmitting}
        >
          S'inscrire
        </Button>

        <p className="text-center text-sm text-secondary-600">
          Déjà un compte ?{' '}
          <Link
            to="/login"
            className="font-medium text-primary-600 hover:text-primary-500"
          >
            Se connecter
          </Link>
        </p>
      </form>
    </div>
  );
}
