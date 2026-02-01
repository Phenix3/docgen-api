import React from 'react';
import { Link, useNavigate, useLocation } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { useAuth } from '../hooks/useAuth';
import { useNotification } from '../hooks/useNotification';
import { loginSchema } from '../utils/validators';
import { Button, Input, Alert } from '../components/common';

export default function Login() {
  const navigate = useNavigate();
  const location = useLocation();
  const { login } = useAuth();
  const { success } = useNotification();
  
  const from = location.state?.from?.pathname || '/dashboard';

  const {
    register,
    handleSubmit,
    setError,
    formState: { errors, isSubmitting },
  } = useForm({
    resolver: zodResolver(loginSchema),
  });

  const onSubmit = async (data) => {
    const result = await login(data.email, data.password);
    
    if (result.success) {
      success('Bienvenue', 'Vous êtes maintenant connecté');
      navigate(from, { replace: true });
    } else {
      setError('root', {
        type: 'manual',
        message: result.error,
      });
    }
  };

  return (
    <div className="w-full">
      <h2 className="text-2xl font-bold text-secondary-900 mb-2">
        Connexion
      </h2>
      <p className="text-secondary-600 mb-8">
        Accédez à votre espace pour gérer vos documents
      </p>

      {errors.root && (
        <Alert variant="danger" className="mb-6">
          {errors.root.message}
        </Alert>
      )}

      <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
        <Input
          label="Email"
          type="email"
          autoComplete="email"
          placeholder="votre@email.com"
          error={errors.email?.message}
          {...register('email')}
        />

        <Input
          label="Mot de passe"
          type="password"
          autoComplete="current-password"
          placeholder="••••••••"
          error={errors.password?.message}
          {...register('password')}
        />

        <div className="flex items-center justify-between">
          <div className="flex items-center">
            <input
              id="remember-me"
              name="remember-me"
              type="checkbox"
              className="h-4 w-4 text-primary-600 focus:ring-primary-500 border-secondary-300 rounded"
            />
            <label htmlFor="remember-me" className="ml-2 block text-sm text-secondary-900">
              Se souvenir de moi
            </label>
          </div>

          <div className="text-sm">
            <Link
              to="/forgot-password"
              className="font-medium text-primary-600 hover:text-primary-500"
            >
              Mot de passe oublié ?
            </Link>
          </div>
        </div>

        <Button
          type="submit"
          className="w-full"
          isLoading={isSubmitting}
        >
          Se connecter
        </Button>

        <p className="text-center text-sm text-secondary-600">
          Pas encore de compte ?{' '}
          <Link
            to="/register"
            className="font-medium text-primary-600 hover:text-primary-500"
          >
            Créer un compte
          </Link>
        </p>
      </form>
    </div>
  );
}
