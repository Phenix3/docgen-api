import { z } from 'zod';

// Login schema
export const loginSchema = z.object({
  email: z
    .string()
    .min(1, 'L\'email est requis')
    .email('Email invalide'),
  password: z
    .string()
    .min(1, 'Le mot de passe est requis'),
});

// Register schema
export const registerSchema = z.object({
  name: z
    .string()
    .min(2, 'Le nom doit contenir au moins 2 caractères')
    .max(255, 'Le nom ne peut pas dépasser 255 caractères'),
  email: z
    .string()
    .min(1, 'L\'email est requis')
    .email('Email invalide'),
  phone: z
    .string()
    .regex(/^\+[1-9]\d{6,14}$/, 'Numéro de téléphone invalide')
    .optional()
    .or(z.literal('')),
  password: z
    .string()
    .min(8, 'Le mot de passe doit contenir au moins 8 caractères'),
  confirmPassword: z
    .string()
    .min(1, 'Veuillez confirmer le mot de passe'),
  countryId: z
    .number()
    .min(1, 'Veuillez sélectionner un pays'),
}).refine((data) => data.password === data.confirmPassword, {
  message: 'Les mots de passe ne correspondent pas',
  path: ['confirmPassword'],
});

// Document schema
export const documentSchema = z.object({
  templateId: z
    .number()
    .min(1, 'Veuillez sélectionner un type de document'),
  title: z
    .string()
    .min(3, 'Le titre doit contenir au moins 3 caractères')
    .max(255, 'Le titre ne peut pas dépasser 255 caractères'),
  inputData: z.record(z.any()),
});

// Profile update schema
export const profileSchema = z.object({
  name: z
    .string()
    .min(2, 'Le nom doit contenir au moins 2 caractères')
    .max(255, 'Le nom ne peut pas dépasser 255 caractères'),
  phone: z
    .string()
    .regex(/^\+[1-9]\d{6,14}$/, 'Numéro de téléphone invalide')
    .optional()
    .or(z.literal('')),
});
