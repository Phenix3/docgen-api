# DOCGEN AFRICA - GUIDE FRONTEND REACT + TAILWINDCSS
# Intégration dans Symfony avec Webpack Encore
# Version: 1.0.0
# Date: Février 2026

---

## MÉTADONNÉES DU PROJET

```yaml
project_name: docgen-africa-api
frontend_framework: React 18
css_framework: TailwindCSS 3.4
bundler: Webpack Encore
routing: React Router 6
state_management: TanStack Query (React Query)
forms: React Hook Form + Zod
ui_components: Headless UI + Heroicons
```

---

## PRÉREQUIS

Ce guide suppose que vous avez déjà:
- Complété le guide backend Symfony + API Platform
- Node.js 20+ installé
- npm ou yarn disponible

---

## ARCHITECTURE FRONTEND

```
assets/
├── app.js                      # Point d'entrée React
├── styles/
│   └── app.css                 # TailwindCSS + styles globaux
├── react/
│   ├── App.jsx                 # Composant racine
│   ├── index.jsx               # Bootstrap React
│   ├── api/                    # Services API
│   │   ├── client.js           # Client HTTP (fetch)
│   │   ├── auth.js             # API authentification
│   │   └── documents.js        # API documents
│   ├── components/             # Composants réutilisables
│   │   ├── common/
│   │   │   ├── Button.jsx
│   │   │   ├── Input.jsx
│   │   │   ├── Select.jsx
│   │   │   ├── Textarea.jsx
│   │   │   ├── Card.jsx
│   │   │   ├── Modal.jsx
│   │   │   ├── Alert.jsx
│   │   │   ├── Spinner.jsx
│   │   │   └── Badge.jsx
│   │   ├── layout/
│   │   │   ├── Header.jsx
│   │   │   ├── Sidebar.jsx
│   │   │   ├── Footer.jsx
│   │   │   └── Layout.jsx
│   │   ├── documents/
│   │   │   ├── DocumentCard.jsx
│   │   │   ├── DocumentList.jsx
│   │   │   ├── DocumentForm.jsx
│   │   │   ├── DocumentPreview.jsx
│   │   │   └── DocumentWizard.jsx
│   │   └── auth/
│   │       ├── LoginForm.jsx
│   │       ├── RegisterForm.jsx
│   │       └── ProtectedRoute.jsx
│   ├── contexts/               # React Contexts
│   │   ├── AuthContext.jsx
│   │   └── NotificationContext.jsx
│   ├── hooks/                  # Custom Hooks
│   │   ├── useAuth.js
│   │   ├── useDocuments.js
│   │   ├── useTemplates.js
│   │   └── useLocalStorage.js
│   ├── pages/                  # Pages/Routes
│   │   ├── Home.jsx
│   │   ├── Login.jsx
│   │   ├── Register.jsx
│   │   ├── Dashboard.jsx
│   │   ├── Documents.jsx
│   │   ├── DocumentNew.jsx
│   │   ├── DocumentView.jsx
│   │   └── Profile.jsx
│   ├── router/                 # Configuration routes
│   │   └── index.jsx
│   └── utils/                  # Utilitaires
│       ├── constants.js
│       ├── helpers.js
│       └── validators.js
└── images/                     # Assets statiques
    └── logo.svg
```

---

# PHASE 1: INSTALLATION ET CONFIGURATION

## TASK 1.1: Installer Webpack Encore

### Objectif
Installer Symfony Webpack Encore pour gérer les assets frontend

### Commandes à exécuter

```bash
cd docgen-africa-api

# Installer Webpack Encore
composer require symfony/webpack-encore-bundle

# Installer les dépendances npm
npm install
```

### Validation
```bash
ls webpack.config.js
# Le fichier doit exister
```

---

## TASK 1.2: Installer React et ses dépendances

### Objectif
Installer React 18, React Router et les librairies essentielles

### Commandes à exécuter

```bash
# React core
npm install react react-dom

# React Router pour le routing SPA
npm install react-router-dom

# TanStack Query pour la gestion d'état serveur
npm install @tanstack/react-query

# React Hook Form + Zod pour les formulaires
npm install react-hook-form @hookform/resolvers zod

# Axios pour les appels API (alternative à fetch)
npm install axios

# Date-fns pour les dates
npm install date-fns

# Activer le preset React dans Encore
npm install @babel/preset-react --save-dev
```

### Validation
```bash
cat package.json | grep react
# Doit afficher les packages React
```

---

## TASK 1.3: Installer TailwindCSS

### Objectif
Installer et configurer TailwindCSS 3.4

### Commandes à exécuter

```bash
# TailwindCSS et ses dépendances
npm install -D tailwindcss postcss autoprefixer postcss-loader

# Initialiser TailwindCSS
npx tailwindcss init -p

# Headless UI pour les composants accessibles
npm install @headlessui/react

# Heroicons pour les icônes
npm install @heroicons/react

# clsx pour les classes conditionnelles
npm install clsx

# tailwind-merge pour fusionner les classes
npm install tailwind-merge
```

### Validation
```bash
ls tailwind.config.js postcss.config.js
# Les deux fichiers doivent exister
```

---

## TASK 1.4: Configurer TailwindCSS

### Objectif
Configurer TailwindCSS avec les chemins corrects et le thème personnalisé

### Fichier à créer: `tailwind.config.js`

```bash
cat > tailwind.config.js << 'EOF'
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.{js,jsx,ts,tsx}",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#f0f9ff',
          100: '#e0f2fe',
          200: '#bae6fd',
          300: '#7dd3fc',
          400: '#38bdf8',
          500: '#0ea5e9',
          600: '#0284c7',
          700: '#0369a1',
          800: '#075985',
          900: '#0c4a6e',
          950: '#082f49',
        },
        secondary: {
          50: '#f8fafc',
          100: '#f1f5f9',
          200: '#e2e8f0',
          300: '#cbd5e1',
          400: '#94a3b8',
          500: '#64748b',
          600: '#475569',
          700: '#334155',
          800: '#1e293b',
          900: '#0f172a',
          950: '#020617',
        },
        success: {
          50: '#f0fdf4',
          500: '#22c55e',
          700: '#15803d',
        },
        warning: {
          50: '#fffbeb',
          500: '#f59e0b',
          700: '#b45309',
        },
        danger: {
          50: '#fef2f2',
          500: '#ef4444',
          700: '#b91c1c',
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
      spacing: {
        '18': '4.5rem',
        '88': '22rem',
      },
      animation: {
        'spin-slow': 'spin 2s linear infinite',
        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
EOF
```

### Installer les plugins Tailwind

```bash
npm install -D @tailwindcss/forms @tailwindcss/typography
```

### Fichier à créer: `postcss.config.js`

```bash
cat > postcss.config.js << 'EOF'
module.exports = {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  },
}
EOF
```

### Validation
```bash
cat tailwind.config.js | grep content
# Doit afficher les chemins configurés
```

---

## TASK 1.5: Configurer Webpack Encore

### Objectif
Configurer Webpack Encore pour React et TailwindCSS

### Fichier à créer: `webpack.config.js`

```bash
cat > webpack.config.js << 'EOF'
const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // Répertoire de sortie
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    
    // Point d'entrée principal
    .addEntry('app', './assets/app.js')
    
    // Activer le splitChunks pour optimiser
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    
    // Nettoyage du répertoire de sortie
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    
    // Activer React (JSX)
    .enableReactPreset()
    
    // Activer PostCSS pour TailwindCSS
    .enablePostCssLoader()
    
    // Configuration Babel
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })
    
    // Alias pour les imports
    .addAliases({
        '@': __dirname + '/assets/react',
        '@components': __dirname + '/assets/react/components',
        '@pages': __dirname + '/assets/react/pages',
        '@hooks': __dirname + '/assets/react/hooks',
        '@api': __dirname + '/assets/react/api',
        '@contexts': __dirname + '/assets/react/contexts',
        '@utils': __dirname + '/assets/react/utils',
    })
    
    // Configuration du dev server
    .configureDevServerOptions(options => {
        options.allowedHosts = 'all';
        options.https = false;
        options.hot = true;
    })
;

module.exports = Encore.getWebpackConfig();
EOF
```

### Validation
```bash
npm run dev
# Doit compiler sans erreur (même si des fichiers n'existent pas encore)
```

---

## TASK 1.6: Créer les fichiers CSS de base

### Objectif
Créer le fichier CSS principal avec TailwindCSS

### Fichier à créer: `assets/styles/app.css`

```bash
mkdir -p assets/styles

cat > assets/styles/app.css << 'EOF'
/* Import TailwindCSS */
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Import Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

/* Base styles */
@layer base {
  html {
    @apply scroll-smooth;
  }
  
  body {
    @apply font-sans text-secondary-900 bg-secondary-50 antialiased;
  }
  
  h1 {
    @apply text-3xl font-bold text-secondary-900;
  }
  
  h2 {
    @apply text-2xl font-semibold text-secondary-800;
  }
  
  h3 {
    @apply text-xl font-semibold text-secondary-800;
  }
  
  h4 {
    @apply text-lg font-medium text-secondary-700;
  }
}

/* Component styles */
@layer components {
  /* Buttons */
  .btn {
    @apply inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed;
  }
  
  .btn-primary {
    @apply btn bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500;
  }
  
  .btn-secondary {
    @apply btn bg-secondary-100 text-secondary-700 hover:bg-secondary-200 focus:ring-secondary-500;
  }
  
  .btn-success {
    @apply btn bg-success-500 text-white hover:bg-success-700 focus:ring-success-500;
  }
  
  .btn-danger {
    @apply btn bg-danger-500 text-white hover:bg-danger-700 focus:ring-danger-500;
  }
  
  .btn-outline {
    @apply btn border-2 border-primary-600 text-primary-600 hover:bg-primary-50 focus:ring-primary-500;
  }
  
  .btn-ghost {
    @apply btn text-secondary-600 hover:bg-secondary-100 focus:ring-secondary-500;
  }
  
  .btn-lg {
    @apply px-6 py-3 text-base;
  }
  
  .btn-sm {
    @apply px-3 py-1.5 text-xs;
  }
  
  /* Form inputs */
  .form-input {
    @apply block w-full px-4 py-2.5 text-secondary-900 bg-white border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200;
  }
  
  .form-input-error {
    @apply form-input border-danger-500 focus:ring-danger-500 focus:border-danger-500;
  }
  
  .form-label {
    @apply block mb-2 text-sm font-medium text-secondary-700;
  }
  
  .form-error {
    @apply mt-1 text-sm text-danger-500;
  }
  
  .form-help {
    @apply mt-1 text-sm text-secondary-500;
  }
  
  /* Cards */
  .card {
    @apply bg-white rounded-xl shadow-sm border border-secondary-200 overflow-hidden;
  }
  
  .card-header {
    @apply px-6 py-4 border-b border-secondary-200 bg-secondary-50;
  }
  
  .card-body {
    @apply px-6 py-4;
  }
  
  .card-footer {
    @apply px-6 py-4 border-t border-secondary-200 bg-secondary-50;
  }
  
  /* Badges */
  .badge {
    @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
  }
  
  .badge-primary {
    @apply badge bg-primary-100 text-primary-800;
  }
  
  .badge-success {
    @apply badge bg-success-50 text-success-700;
  }
  
  .badge-warning {
    @apply badge bg-warning-50 text-warning-700;
  }
  
  .badge-danger {
    @apply badge bg-danger-50 text-danger-700;
  }
  
  .badge-secondary {
    @apply badge bg-secondary-100 text-secondary-700;
  }
  
  /* Alerts */
  .alert {
    @apply p-4 rounded-lg border;
  }
  
  .alert-success {
    @apply alert bg-success-50 border-success-500 text-success-700;
  }
  
  .alert-warning {
    @apply alert bg-warning-50 border-warning-500 text-warning-700;
  }
  
  .alert-danger {
    @apply alert bg-danger-50 border-danger-500 text-danger-700;
  }
  
  .alert-info {
    @apply alert bg-primary-50 border-primary-500 text-primary-700;
  }
}

/* Utility styles */
@layer utilities {
  /* Scrollbar styling */
  .scrollbar-thin {
    scrollbar-width: thin;
  }
  
  .scrollbar-thin::-webkit-scrollbar {
    width: 6px;
    height: 6px;
  }
  
  .scrollbar-thin::-webkit-scrollbar-track {
    @apply bg-secondary-100 rounded;
  }
  
  .scrollbar-thin::-webkit-scrollbar-thumb {
    @apply bg-secondary-300 rounded hover:bg-secondary-400;
  }
  
  /* Animation utilities */
  .animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
  }
  
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .animate-slide-up {
    animation: slideUp 0.3s ease-out;
  }
  
  @keyframes slideUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
}

/* Print styles */
@media print {
  .no-print {
    display: none !important;
  }
}
EOF
```

### Validation
```bash
cat assets/styles/app.css | head -5
# Doit afficher les imports Tailwind
```

---

## TASK 1.7: Créer le point d'entrée JavaScript

### Objectif
Créer le fichier d'entrée principal qui charge React

### Fichier à créer: `assets/app.js`

```bash
cat > assets/app.js << 'EOF'
/*
 * DocGen Africa - Application Frontend
 * Point d'entrée principal
 */

// Styles
import './styles/app.css';

// React Application
import './react/index.jsx';

// Console message
console.log('🌍 DocGen Africa - Application chargée');
EOF
```

### Validation
```bash
cat assets/app.js
# Doit afficher les imports
```

---

# PHASE 2: CRÉATION DE LA STRUCTURE REACT

## TASK 2.1: Créer l'index React

### Objectif
Créer le point d'entrée React avec les providers

### Fichier à créer: `assets/react/index.jsx`

```bash
mkdir -p assets/react

cat > assets/react/index.jsx << 'EOF'
import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { AuthProvider } from './contexts/AuthContext';
import { NotificationProvider } from './contexts/NotificationContext';
import App from './App';

// Configuration React Query
const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      retry: 1,
      refetchOnWindowFocus: false,
      staleTime: 5 * 60 * 1000, // 5 minutes
    },
  },
});

// Point de montage
const container = document.getElementById('root');

if (container) {
  const root = createRoot(container);
  
  root.render(
    <React.StrictMode>
      <QueryClientProvider client={queryClient}>
        <BrowserRouter>
          <AuthProvider>
            <NotificationProvider>
              <App />
            </NotificationProvider>
          </AuthProvider>
        </BrowserRouter>
      </QueryClientProvider>
    </React.StrictMode>
  );
}
EOF
```

### Validation
```bash
cat assets/react/index.jsx | grep createRoot
# Doit afficher l'import createRoot
```

---

## TASK 2.2: Créer le composant App principal

### Objectif
Créer le composant racine avec le routing

### Fichier à créer: `assets/react/App.jsx`

```bash
cat > assets/react/App.jsx << 'EOF'
import React, { Suspense } from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import { useAuth } from './hooks/useAuth';

// Layouts
import Layout from './components/layout/Layout';
import AuthLayout from './components/layout/AuthLayout';

// Pages (lazy loaded)
const Home = React.lazy(() => import('./pages/Home'));
const Login = React.lazy(() => import('./pages/Login'));
const Register = React.lazy(() => import('./pages/Register'));
const Dashboard = React.lazy(() => import('./pages/Dashboard'));
const Documents = React.lazy(() => import('./pages/Documents'));
const DocumentNew = React.lazy(() => import('./pages/DocumentNew'));
const DocumentView = React.lazy(() => import('./pages/DocumentView'));
const Profile = React.lazy(() => import('./pages/Profile'));
const NotFound = React.lazy(() => import('./pages/NotFound'));

// Loading fallback
const PageLoader = () => (
  <div className="min-h-screen flex items-center justify-center bg-secondary-50">
    <div className="text-center">
      <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto"></div>
      <p className="mt-4 text-secondary-600">Chargement...</p>
    </div>
  </div>
);

// Protected Route Component
const ProtectedRoute = ({ children }) => {
  const { isAuthenticated, isLoading } = useAuth();
  
  if (isLoading) {
    return <PageLoader />;
  }
  
  if (!isAuthenticated) {
    return <Navigate to="/login" replace />;
  }
  
  return children;
};

// Public Route (redirect if authenticated)
const PublicRoute = ({ children }) => {
  const { isAuthenticated, isLoading } = useAuth();
  
  if (isLoading) {
    return <PageLoader />;
  }
  
  if (isAuthenticated) {
    return <Navigate to="/dashboard" replace />;
  }
  
  return children;
};

function App() {
  return (
    <Suspense fallback={<PageLoader />}>
      <Routes>
        {/* Public routes */}
        <Route path="/" element={<Home />} />
        
        {/* Auth routes */}
        <Route element={<AuthLayout />}>
          <Route
            path="/login"
            element={
              <PublicRoute>
                <Login />
              </PublicRoute>
            }
          />
          <Route
            path="/register"
            element={
              <PublicRoute>
                <Register />
              </PublicRoute>
            }
          />
        </Route>
        
        {/* Protected routes */}
        <Route
          element={
            <ProtectedRoute>
              <Layout />
            </ProtectedRoute>
          }
        >
          <Route path="/dashboard" element={<Dashboard />} />
          <Route path="/documents" element={<Documents />} />
          <Route path="/documents/new" element={<DocumentNew />} />
          <Route path="/documents/:id" element={<DocumentView />} />
          <Route path="/profile" element={<Profile />} />
        </Route>
        
        {/* 404 */}
        <Route path="*" element={<NotFound />} />
      </Routes>
    </Suspense>
  );
}

export default App;
EOF
```

### Validation
```bash
cat assets/react/App.jsx | grep "Routes"
# Doit afficher l'import Routes
```

---

## TASK 2.3: Créer les utilitaires

### Objectif
Créer les fichiers utilitaires pour les constantes et helpers

### Fichier à créer: `assets/react/utils/constants.js`

```bash
mkdir -p assets/react/utils

cat > assets/react/utils/constants.js << 'EOF'
// API Base URL
export const API_BASE_URL = '/api';

// Storage Keys
export const STORAGE_KEYS = {
  TOKEN: 'docgen_token',
  REFRESH_TOKEN: 'docgen_refresh_token',
  USER: 'docgen_user',
};

// Document Status
export const DOCUMENT_STATUS = {
  DRAFT: 'draft',
  GENERATING: 'generating',
  COMPLETED: 'completed',
  FAILED: 'failed',
};

export const DOCUMENT_STATUS_LABELS = {
  draft: 'Brouillon',
  generating: 'En cours',
  completed: 'Terminé',
  failed: 'Échec',
};

export const DOCUMENT_STATUS_COLORS = {
  draft: 'secondary',
  generating: 'warning',
  completed: 'success',
  failed: 'danger',
};

// Subscription Types
export const SUBSCRIPTION_TYPES = {
  FREE: 'free',
  CITIZEN: 'citizen',
  PRO: 'pro',
  ENTERPRISE: 'enterprise',
};

export const SUBSCRIPTION_LABELS = {
  free: 'Gratuit',
  citizen: 'Citoyen',
  pro: 'Professionnel',
  enterprise: 'Entreprise',
};

// Tone Levels
export const TONE_LEVELS = {
  VERY_FORMAL: 'very_formal',
  FORMAL: 'formal',
  SIMPLE: 'simple',
};

export const TONE_LABELS = {
  very_formal: 'Très formel',
  formal: 'Formel standard',
  simple: 'Simple',
};

// Pagination
export const DEFAULT_PAGE_SIZE = 15;

// Validation
export const VALIDATION = {
  PASSWORD_MIN_LENGTH: 8,
  NAME_MAX_LENGTH: 255,
  TITLE_MAX_LENGTH: 255,
};
EOF
```

### Fichier à créer: `assets/react/utils/helpers.js`

```bash
cat > assets/react/utils/helpers.js << 'EOF'
import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';
import { format, formatDistanceToNow, parseISO } from 'date-fns';
import { fr } from 'date-fns/locale';

/**
 * Merge Tailwind classes intelligently
 */
export function cn(...inputs) {
  return twMerge(clsx(inputs));
}

/**
 * Format date to French locale
 */
export function formatDate(date, formatStr = 'dd/MM/yyyy') {
  if (!date) return '';
  const dateObj = typeof date === 'string' ? parseISO(date) : date;
  return format(dateObj, formatStr, { locale: fr });
}

/**
 * Format date relative (e.g., "il y a 2 heures")
 */
export function formatRelativeDate(date) {
  if (!date) return '';
  const dateObj = typeof date === 'string' ? parseISO(date) : date;
  return formatDistanceToNow(dateObj, { addSuffix: true, locale: fr });
}

/**
 * Format date and time
 */
export function formatDateTime(date) {
  return formatDate(date, "dd/MM/yyyy 'à' HH:mm");
}

/**
 * Truncate text with ellipsis
 */
export function truncate(text, maxLength = 100) {
  if (!text || text.length <= maxLength) return text;
  return text.slice(0, maxLength) + '...';
}

/**
 * Generate initials from name
 */
export function getInitials(name) {
  if (!name) return '?';
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2);
}

/**
 * Format file size
 */
export function formatFileSize(bytes) {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Debounce function
 */
export function debounce(func, wait = 300) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

/**
 * Copy text to clipboard
 */
export async function copyToClipboard(text) {
  try {
    await navigator.clipboard.writeText(text);
    return true;
  } catch (err) {
    console.error('Failed to copy:', err);
    return false;
  }
}

/**
 * Download file from blob
 */
export function downloadFile(blob, filename) {
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = filename;
  document.body.appendChild(a);
  a.click();
  window.URL.revokeObjectURL(url);
  document.body.removeChild(a);
}

/**
 * Get error message from API response
 */
export function getErrorMessage(error) {
  if (error.response?.data?.message) {
    return error.response.data.message;
  }
  if (error.response?.data?.['hydra:description']) {
    return error.response.data['hydra:description'];
  }
  if (error.message) {
    return error.message;
  }
  return 'Une erreur est survenue';
}

/**
 * Check if user can export DOCX
 */
export function canExportDocx(subscriptionType) {
  return subscriptionType !== 'free';
}
EOF
```

### Fichier à créer: `assets/react/utils/validators.js`

```bash
cat > assets/react/utils/validators.js << 'EOF'
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
    .regex(/^\+?[0-9]{8,15}$/, 'Numéro de téléphone invalide')
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
    .regex(/^\+?[0-9]{8,15}$/, 'Numéro de téléphone invalide')
    .optional()
    .or(z.literal('')),
});
EOF
```

### Validation
```bash
ls assets/react/utils/
# Doit afficher: constants.js helpers.js validators.js
```

---

## TASK 2.4: Créer le client API

### Objectif
Créer le client HTTP pour communiquer avec l'API

### Fichier à créer: `assets/react/api/client.js`

```bash
mkdir -p assets/react/api

cat > assets/react/api/client.js << 'EOF'
import axios from 'axios';
import { API_BASE_URL, STORAGE_KEYS } from '../utils/constants';

// Create axios instance
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/ld+json',
  },
});

// Request interceptor - Add auth token
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem(STORAGE_KEYS.TOKEN);
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor - Handle errors
apiClient.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;
    
    // Handle 401 - Token expired
    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;
      
      // Try to refresh token
      const refreshToken = localStorage.getItem(STORAGE_KEYS.REFRESH_TOKEN);
      if (refreshToken) {
        try {
          const response = await axios.post(`${API_BASE_URL}/auth/refresh`, {
            refresh_token: refreshToken,
          });
          
          const { token } = response.data;
          localStorage.setItem(STORAGE_KEYS.TOKEN, token);
          
          originalRequest.headers.Authorization = `Bearer ${token}`;
          return apiClient(originalRequest);
        } catch (refreshError) {
          // Refresh failed - logout
          localStorage.removeItem(STORAGE_KEYS.TOKEN);
          localStorage.removeItem(STORAGE_KEYS.REFRESH_TOKEN);
          localStorage.removeItem(STORAGE_KEYS.USER);
          window.location.href = '/login';
          return Promise.reject(refreshError);
        }
      }
    }
    
    return Promise.reject(error);
  }
);

// Helper to get data from Hydra response
export function getHydraData(response) {
  return response.data['hydra:member'] || response.data;
}

// Helper to get total items from Hydra response
export function getHydraTotalItems(response) {
  return response.data['hydra:totalItems'] || 0;
}

export default apiClient;
EOF
```

### Fichier à créer: `assets/react/api/auth.js`

```bash
cat > assets/react/api/auth.js << 'EOF'
import apiClient from './client';
import { STORAGE_KEYS } from '../utils/constants';

export const authApi = {
  /**
   * Login user
   */
  async login(email, password) {
    const response = await apiClient.post('/auth/login', { email, password });
    const { token, refresh_token } = response.data;
    
    // Store tokens
    localStorage.setItem(STORAGE_KEYS.TOKEN, token);
    if (refresh_token) {
      localStorage.setItem(STORAGE_KEYS.REFRESH_TOKEN, refresh_token);
    }
    
    // Fetch user profile
    const userResponse = await this.getProfile();
    return userResponse;
  },
  
  /**
   * Register new user
   */
  async register(data) {
    const response = await apiClient.post('/auth/register', {
      email: data.email,
      password: data.password,
      name: data.name,
      phone: data.phone || null,
      countryId: data.countryId,
    });
    return response.data;
  },
  
  /**
   * Get current user profile
   */
  async getProfile() {
    const response = await apiClient.get('/auth/me');
    const user = response.data;
    localStorage.setItem(STORAGE_KEYS.USER, JSON.stringify(user));
    return user;
  },
  
  /**
   * Logout user
   */
  logout() {
    localStorage.removeItem(STORAGE_KEYS.TOKEN);
    localStorage.removeItem(STORAGE_KEYS.REFRESH_TOKEN);
    localStorage.removeItem(STORAGE_KEYS.USER);
  },
  
  /**
   * Check if user is authenticated
   */
  isAuthenticated() {
    return !!localStorage.getItem(STORAGE_KEYS.TOKEN);
  },
  
  /**
   * Get stored user
   */
  getStoredUser() {
    const user = localStorage.getItem(STORAGE_KEYS.USER);
    return user ? JSON.parse(user) : null;
  },
};
EOF
```

### Fichier à créer: `assets/react/api/documents.js`

```bash
cat > assets/react/api/documents.js << 'EOF'
import apiClient, { getHydraData, getHydraTotalItems } from './client';
import { downloadFile } from '../utils/helpers';

export const documentsApi = {
  /**
   * Get all documents for current user
   */
  async getAll(params = {}) {
    const response = await apiClient.get('/documents', { params });
    return {
      data: getHydraData(response),
      total: getHydraTotalItems(response),
    };
  },
  
  /**
   * Get single document
   */
  async getOne(id) {
    const response = await apiClient.get(`/documents/${id}`);
    return response.data;
  },
  
  /**
   * Create new document
   */
  async create(data) {
    const response = await apiClient.post('/documents', {
      template: `/api/templates/${data.templateId}`,
      title: data.title,
      inputData: data.inputData,
    });
    return response.data;
  },
  
  /**
   * Update document
   */
  async update(id, data) {
    const response = await apiClient.put(`/documents/${id}`, {
      title: data.title,
      inputData: data.inputData,
    });
    return response.data;
  },
  
  /**
   * Delete document
   */
  async delete(id) {
    await apiClient.delete(`/documents/${id}`);
  },
  
  /**
   * Generate document content
   */
  async generate(id, regenerate = false) {
    const response = await apiClient.post(`/documents/${id}/generate`, {
      regenerate,
    });
    return response.data;
  },
  
  /**
   * Export document to PDF
   */
  async exportPdf(id, filename) {
    const response = await apiClient.get(`/documents/${id}/export/pdf`, {
      responseType: 'blob',
    });
    downloadFile(response.data, `${filename}.pdf`);
  },
  
  /**
   * Export document to DOCX
   */
  async exportDocx(id, filename) {
    const response = await apiClient.get(`/documents/${id}/export/docx`, {
      responseType: 'blob',
    });
    downloadFile(response.data, `${filename}.docx`);
  },
};
EOF
```

### Fichier à créer: `assets/react/api/templates.js`

```bash
cat > assets/react/api/templates.js << 'EOF'
import apiClient, { getHydraData } from './client';

export const templatesApi = {
  /**
   * Get all templates
   */
  async getAll(params = {}) {
    const response = await apiClient.get('/templates', { params });
    return getHydraData(response);
  },
  
  /**
   * Get templates by country
   */
  async getByCountry(countryId) {
    const response = await apiClient.get('/templates', {
      params: { 'country.id': countryId },
    });
    return getHydraData(response);
  },
  
  /**
   * Get single template with fields schema
   */
  async getOne(id) {
    const response = await apiClient.get(`/templates/${id}`);
    return response.data;
  },
};

export const countriesApi = {
  /**
   * Get all active countries
   */
  async getAll() {
    const response = await apiClient.get('/countries');
    return getHydraData(response);
  },
};

export const documentTypesApi = {
  /**
   * Get all document types
   */
  async getAll() {
    const response = await apiClient.get('/document_types');
    return getHydraData(response);
  },
};
EOF
```

### Validation
```bash
ls assets/react/api/
# Doit afficher: client.js auth.js documents.js templates.js
```

---

## TASK 2.5: Créer les Contexts React

### Objectif
Créer les contexts pour l'authentification et les notifications

### Fichier à créer: `assets/react/contexts/AuthContext.jsx`

```bash
mkdir -p assets/react/contexts

cat > assets/react/contexts/AuthContext.jsx << 'EOF'
import React, { createContext, useState, useEffect, useCallback } from 'react';
import { authApi } from '../api/auth';

export const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [isAuthenticated, setIsAuthenticated] = useState(false);

  // Initialize auth state
  useEffect(() => {
    const initAuth = async () => {
      try {
        if (authApi.isAuthenticated()) {
          const storedUser = authApi.getStoredUser();
          if (storedUser) {
            setUser(storedUser);
            setIsAuthenticated(true);
          } else {
            // Fetch fresh user data
            const userData = await authApi.getProfile();
            setUser(userData);
            setIsAuthenticated(true);
          }
        }
      } catch (error) {
        console.error('Auth init error:', error);
        authApi.logout();
      } finally {
        setIsLoading(false);
      }
    };

    initAuth();
  }, []);

  // Login
  const login = useCallback(async (email, password) => {
    setIsLoading(true);
    try {
      const userData = await authApi.login(email, password);
      setUser(userData);
      setIsAuthenticated(true);
      return { success: true };
    } catch (error) {
      return {
        success: false,
        error: error.response?.data?.message || 'Identifiants incorrects',
      };
    } finally {
      setIsLoading(false);
    }
  }, []);

  // Register
  const register = useCallback(async (data) => {
    setIsLoading(true);
    try {
      await authApi.register(data);
      return { success: true };
    } catch (error) {
      return {
        success: false,
        error: error.response?.data?.message || 'Erreur lors de l\'inscription',
      };
    } finally {
      setIsLoading(false);
    }
  }, []);

  // Logout
  const logout = useCallback(() => {
    authApi.logout();
    setUser(null);
    setIsAuthenticated(false);
  }, []);

  // Refresh user data
  const refreshUser = useCallback(async () => {
    try {
      const userData = await authApi.getProfile();
      setUser(userData);
    } catch (error) {
      console.error('Failed to refresh user:', error);
    }
  }, []);

  const value = {
    user,
    isLoading,
    isAuthenticated,
    login,
    register,
    logout,
    refreshUser,
  };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
}
EOF
```

### Fichier à créer: `assets/react/contexts/NotificationContext.jsx`

```bash
cat > assets/react/contexts/NotificationContext.jsx << 'EOF'
import React, { createContext, useState, useCallback } from 'react';

export const NotificationContext = createContext(null);

let notificationId = 0;

export function NotificationProvider({ children }) {
  const [notifications, setNotifications] = useState([]);

  // Add notification
  const addNotification = useCallback((notification) => {
    const id = ++notificationId;
    const newNotification = {
      id,
      type: notification.type || 'info',
      title: notification.title,
      message: notification.message,
      duration: notification.duration ?? 5000,
    };

    setNotifications((prev) => [...prev, newNotification]);

    // Auto remove after duration
    if (newNotification.duration > 0) {
      setTimeout(() => {
        removeNotification(id);
      }, newNotification.duration);
    }

    return id;
  }, []);

  // Remove notification
  const removeNotification = useCallback((id) => {
    setNotifications((prev) => prev.filter((n) => n.id !== id));
  }, []);

  // Helper methods
  const success = useCallback((title, message) => {
    return addNotification({ type: 'success', title, message });
  }, [addNotification]);

  const error = useCallback((title, message) => {
    return addNotification({ type: 'error', title, message, duration: 8000 });
  }, [addNotification]);

  const warning = useCallback((title, message) => {
    return addNotification({ type: 'warning', title, message });
  }, [addNotification]);

  const info = useCallback((title, message) => {
    return addNotification({ type: 'info', title, message });
  }, [addNotification]);

  const value = {
    notifications,
    addNotification,
    removeNotification,
    success,
    error,
    warning,
    info,
  };

  return (
    <NotificationContext.Provider value={value}>
      {children}
    </NotificationContext.Provider>
  );
}
EOF
```

### Validation
```bash
ls assets/react/contexts/
# Doit afficher: AuthContext.jsx NotificationContext.jsx
```

---

## TASK 2.6: Créer les Custom Hooks

### Objectif
Créer les hooks réutilisables

### Fichier à créer: `assets/react/hooks/useAuth.js`

```bash
mkdir -p assets/react/hooks

cat > assets/react/hooks/useAuth.js << 'EOF'
import { useContext } from 'react';
import { AuthContext } from '../contexts/AuthContext';

export function useAuth() {
  const context = useContext(AuthContext);
  
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  
  return context;
}
EOF
```

### Fichier à créer: `assets/react/hooks/useNotification.js`

```bash
cat > assets/react/hooks/useNotification.js << 'EOF'
import { useContext } from 'react';
import { NotificationContext } from '../contexts/NotificationContext';

export function useNotification() {
  const context = useContext(NotificationContext);
  
  if (!context) {
    throw new Error('useNotification must be used within a NotificationProvider');
  }
  
  return context;
}
EOF
```

### Fichier à créer: `assets/react/hooks/useDocuments.js`

```bash
cat > assets/react/hooks/useDocuments.js << 'EOF'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { documentsApi } from '../api/documents';
import { useNotification } from './useNotification';

export function useDocuments(params = {}) {
  return useQuery({
    queryKey: ['documents', params],
    queryFn: () => documentsApi.getAll(params),
  });
}

export function useDocument(id) {
  return useQuery({
    queryKey: ['document', id],
    queryFn: () => documentsApi.getOne(id),
    enabled: !!id,
  });
}

export function useCreateDocument() {
  const queryClient = useQueryClient();
  const { success, error } = useNotification();

  return useMutation({
    mutationFn: (data) => documentsApi.create(data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['documents'] });
      success('Document créé', 'Votre document a été créé avec succès');
    },
    onError: (err) => {
      error('Erreur', err.response?.data?.message || 'Impossible de créer le document');
    },
  });
}

export function useUpdateDocument() {
  const queryClient = useQueryClient();
  const { success, error } = useNotification();

  return useMutation({
    mutationFn: ({ id, data }) => documentsApi.update(id, data),
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: ['documents'] });
      queryClient.invalidateQueries({ queryKey: ['document', variables.id] });
      success('Document mis à jour', 'Les modifications ont été enregistrées');
    },
    onError: (err) => {
      error('Erreur', err.response?.data?.message || 'Impossible de mettre à jour le document');
    },
  });
}

export function useDeleteDocument() {
  const queryClient = useQueryClient();
  const { success, error } = useNotification();

  return useMutation({
    mutationFn: (id) => documentsApi.delete(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['documents'] });
      success('Document supprimé', 'Le document a été supprimé');
    },
    onError: (err) => {
      error('Erreur', err.response?.data?.message || 'Impossible de supprimer le document');
    },
  });
}

export function useGenerateDocument() {
  const queryClient = useQueryClient();
  const { success, error, info } = useNotification();

  return useMutation({
    mutationFn: ({ id, regenerate }) => documentsApi.generate(id, regenerate),
    onMutate: () => {
      info('Génération en cours', 'Votre document est en cours de génération...');
    },
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: ['documents'] });
      queryClient.invalidateQueries({ queryKey: ['document', variables.id] });
      success('Génération lancée', 'La génération du document a démarré');
    },
    onError: (err) => {
      error('Erreur', err.response?.data?.message || 'Impossible de générer le document');
    },
  });
}

export function useExportDocument() {
  const { success, error } = useNotification();

  return useMutation({
    mutationFn: async ({ id, format, filename }) => {
      if (format === 'pdf') {
        await documentsApi.exportPdf(id, filename);
      } else {
        await documentsApi.exportDocx(id, filename);
      }
    },
    onSuccess: (_, variables) => {
      success('Export réussi', `Le document a été exporté en ${variables.format.toUpperCase()}`);
    },
    onError: (err) => {
      error('Erreur', err.response?.data?.message || 'Impossible d\'exporter le document');
    },
  });
}
EOF
```

### Fichier à créer: `assets/react/hooks/useTemplates.js`

```bash
cat > assets/react/hooks/useTemplates.js << 'EOF'
import { useQuery } from '@tanstack/react-query';
import { templatesApi, countriesApi, documentTypesApi } from '../api/templates';

export function useTemplates(params = {}) {
  return useQuery({
    queryKey: ['templates', params],
    queryFn: () => templatesApi.getAll(params),
    staleTime: 10 * 60 * 1000, // 10 minutes
  });
}

export function useTemplatesByCountry(countryId) {
  return useQuery({
    queryKey: ['templates', 'country', countryId],
    queryFn: () => templatesApi.getByCountry(countryId),
    enabled: !!countryId,
    staleTime: 10 * 60 * 1000,
  });
}

export function useTemplate(id) {
  return useQuery({
    queryKey: ['template', id],
    queryFn: () => templatesApi.getOne(id),
    enabled: !!id,
    staleTime: 10 * 60 * 1000,
  });
}

export function useCountries() {
  return useQuery({
    queryKey: ['countries'],
    queryFn: () => countriesApi.getAll(),
    staleTime: 30 * 60 * 1000, // 30 minutes
  });
}

export function useDocumentTypes() {
  return useQuery({
    queryKey: ['documentTypes'],
    queryFn: () => documentTypesApi.getAll(),
    staleTime: 30 * 60 * 1000,
  });
}
EOF
```

### Fichier à créer: `assets/react/hooks/useLocalStorage.js`

```bash
cat > assets/react/hooks/useLocalStorage.js << 'EOF'
import { useState, useEffect } from 'react';

export function useLocalStorage(key, initialValue) {
  // Get from local storage or use initial value
  const [storedValue, setStoredValue] = useState(() => {
    try {
      const item = window.localStorage.getItem(key);
      return item ? JSON.parse(item) : initialValue;
    } catch (error) {
      console.error(`Error reading localStorage key "${key}":`, error);
      return initialValue;
    }
  });

  // Update local storage when value changes
  useEffect(() => {
    try {
      window.localStorage.setItem(key, JSON.stringify(storedValue));
    } catch (error) {
      console.error(`Error setting localStorage key "${key}":`, error);
    }
  }, [key, storedValue]);

  return [storedValue, setStoredValue];
}
EOF
```

### Validation
```bash
ls assets/react/hooks/
# Doit afficher tous les hooks créés
```

---

# PHASE 3: COMPOSANTS COMMUNS

## TASK 3.1: Créer les composants UI de base

### Objectif
Créer les composants réutilisables (Button, Input, etc.)

### Fichier à créer: `assets/react/components/common/Button.jsx`

```bash
mkdir -p assets/react/components/common

cat > assets/react/components/common/Button.jsx << 'EOF'
import React from 'react';
import { cn } from '../../utils/helpers';

const variants = {
  primary: 'btn-primary',
  secondary: 'btn-secondary',
  success: 'btn-success',
  danger: 'btn-danger',
  outline: 'btn-outline',
  ghost: 'btn-ghost',
};

const sizes = {
  sm: 'btn-sm',
  md: '',
  lg: 'btn-lg',
};

export function Button({
  children,
  variant = 'primary',
  size = 'md',
  className,
  isLoading = false,
  leftIcon,
  rightIcon,
  disabled,
  ...props
}) {
  return (
    <button
      className={cn(
        'btn',
        variants[variant],
        sizes[size],
        isLoading && 'opacity-70 cursor-wait',
        className
      )}
      disabled={disabled || isLoading}
      {...props}
    >
      {isLoading ? (
        <>
          <svg
            className="animate-spin -ml-1 mr-2 h-4 w-4"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              className="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              strokeWidth="4"
            />
            <path
              className="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            />
          </svg>
          Chargement...
        </>
      ) : (
        <>
          {leftIcon && <span className="mr-2">{leftIcon}</span>}
          {children}
          {rightIcon && <span className="ml-2">{rightIcon}</span>}
        </>
      )}
    </button>
  );
}
EOF
```

### Fichier à créer: `assets/react/components/common/Input.jsx`

```bash
cat > assets/react/components/common/Input.jsx << 'EOF'
import React, { forwardRef } from 'react';
import { cn } from '../../utils/helpers';

export const Input = forwardRef(function Input(
  { label, error, helpText, className, ...props },
  ref
) {
  return (
    <div className="w-full">
      {label && (
        <label className="form-label">
          {label}
          {props.required && <span className="text-danger-500 ml-1">*</span>}
        </label>
      )}
      <input
        ref={ref}
        className={cn(
          error ? 'form-input-error' : 'form-input',
          className
        )}
        {...props}
      />
      {error && <p className="form-error">{error}</p>}
      {helpText && !error && <p className="form-help">{helpText}</p>}
    </div>
  );
});
EOF
```

### Fichier à créer: `assets/react/components/common/Select.jsx`

```bash
cat > assets/react/components/common/Select.jsx << 'EOF'
import React, { forwardRef } from 'react';
import { cn } from '../../utils/helpers';

export const Select = forwardRef(function Select(
  { label, error, helpText, options = [], placeholder, className, ...props },
  ref
) {
  return (
    <div className="w-full">
      {label && (
        <label className="form-label">
          {label}
          {props.required && <span className="text-danger-500 ml-1">*</span>}
        </label>
      )}
      <select
        ref={ref}
        className={cn(
          error ? 'form-input-error' : 'form-input',
          'pr-10',
          className
        )}
        {...props}
      >
        {placeholder && (
          <option value="">{placeholder}</option>
        )}
        {options.map((option) => (
          <option key={option.value} value={option.value}>
            {option.label}
          </option>
        ))}
      </select>
      {error && <p className="form-error">{error}</p>}
      {helpText && !error && <p className="form-help">{helpText}</p>}
    </div>
  );
});
EOF
```

### Fichier à créer: `assets/react/components/common/Textarea.jsx`

```bash
cat > assets/react/components/common/Textarea.jsx << 'EOF'
import React, { forwardRef } from 'react';
import { cn } from '../../utils/helpers';

export const Textarea = forwardRef(function Textarea(
  { label, error, helpText, className, rows = 4, ...props },
  ref
) {
  return (
    <div className="w-full">
      {label && (
        <label className="form-label">
          {label}
          {props.required && <span className="text-danger-500 ml-1">*</span>}
        </label>
      )}
      <textarea
        ref={ref}
        rows={rows}
        className={cn(
          error ? 'form-input-error' : 'form-input',
          'resize-none',
          className
        )}
        {...props}
      />
      {error && <p className="form-error">{error}</p>}
      {helpText && !error && <p className="form-help">{helpText}</p>}
    </div>
  );
});
EOF
```

### Fichier à créer: `assets/react/components/common/Card.jsx`

```bash
cat > assets/react/components/common/Card.jsx << 'EOF'
import React from 'react';
import { cn } from '../../utils/helpers';

export function Card({ children, className, ...props }) {
  return (
    <div className={cn('card', className)} {...props}>
      {children}
    </div>
  );
}

export function CardHeader({ children, className, ...props }) {
  return (
    <div className={cn('card-header', className)} {...props}>
      {children}
    </div>
  );
}

export function CardBody({ children, className, ...props }) {
  return (
    <div className={cn('card-body', className)} {...props}>
      {children}
    </div>
  );
}

export function CardFooter({ children, className, ...props }) {
  return (
    <div className={cn('card-footer', className)} {...props}>
      {children}
    </div>
  );
}
EOF
```

### Fichier à créer: `assets/react/components/common/Badge.jsx`

```bash
cat > assets/react/components/common/Badge.jsx << 'EOF'
import React from 'react';
import { cn } from '../../utils/helpers';

const variants = {
  primary: 'badge-primary',
  secondary: 'badge-secondary',
  success: 'badge-success',
  warning: 'badge-warning',
  danger: 'badge-danger',
};

export function Badge({ children, variant = 'secondary', className, ...props }) {
  return (
    <span className={cn(variants[variant], className)} {...props}>
      {children}
    </span>
  );
}
EOF
```

### Fichier à créer: `assets/react/components/common/Spinner.jsx`

```bash
cat > assets/react/components/common/Spinner.jsx << 'EOF'
import React from 'react';
import { cn } from '../../utils/helpers';

const sizes = {
  sm: 'h-4 w-4',
  md: 'h-8 w-8',
  lg: 'h-12 w-12',
};

export function Spinner({ size = 'md', className }) {
  return (
    <svg
      className={cn('animate-spin text-primary-600', sizes[size], className)}
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle
        className="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        strokeWidth="4"
      />
      <path
        className="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
      />
    </svg>
  );
}
EOF
```

### Fichier à créer: `assets/react/components/common/Alert.jsx`

```bash
cat > assets/react/components/common/Alert.jsx << 'EOF'
import React from 'react';
import { cn } from '../../utils/helpers';
import {
  CheckCircleIcon,
  ExclamationTriangleIcon,
  XCircleIcon,
  InformationCircleIcon,
  XMarkIcon,
} from '@heroicons/react/24/outline';

const variants = {
  success: {
    className: 'alert-success',
    icon: CheckCircleIcon,
  },
  warning: {
    className: 'alert-warning',
    icon: ExclamationTriangleIcon,
  },
  danger: {
    className: 'alert-danger',
    icon: XCircleIcon,
  },
  info: {
    className: 'alert-info',
    icon: InformationCircleIcon,
  },
};

export function Alert({ 
  children, 
  variant = 'info', 
  title, 
  onClose, 
  className,
  ...props 
}) {
  const { className: variantClassName, icon: Icon } = variants[variant];

  return (
    <div className={cn(variantClassName, className)} role="alert" {...props}>
      <div className="flex">
        <Icon className="h-5 w-5 flex-shrink-0" />
        <div className="ml-3 flex-1">
          {title && <h3 className="font-medium">{title}</h3>}
          <div className={title ? 'mt-1' : ''}>{children}</div>
        </div>
        {onClose && (
          <button
            type="button"
            className="ml-3 flex-shrink-0 rounded hover:opacity-70"
            onClick={onClose}
          >
            <XMarkIcon className="h-5 w-5" />
          </button>
        )}
      </div>
    </div>
  );
}
EOF
```

### Fichier à créer: `assets/react/components/common/Modal.jsx`

```bash
cat > assets/react/components/common/Modal.jsx << 'EOF'
import React, { Fragment } from 'react';
import { Dialog, Transition } from '@headlessui/react';
import { XMarkIcon } from '@heroicons/react/24/outline';
import { cn } from '../../utils/helpers';

const sizes = {
  sm: 'max-w-md',
  md: 'max-w-lg',
  lg: 'max-w-2xl',
  xl: 'max-w-4xl',
  full: 'max-w-full mx-4',
};

export function Modal({
  isOpen,
  onClose,
  title,
  children,
  size = 'md',
  showCloseButton = true,
}) {
  return (
    <Transition appear show={isOpen} as={Fragment}>
      <Dialog as="div" className="relative z-50" onClose={onClose}>
        <Transition.Child
          as={Fragment}
          enter="ease-out duration-300"
          enterFrom="opacity-0"
          enterTo="opacity-100"
          leave="ease-in duration-200"
          leaveFrom="opacity-100"
          leaveTo="opacity-0"
        >
          <div className="fixed inset-0 bg-black/50" />
        </Transition.Child>

        <div className="fixed inset-0 overflow-y-auto">
          <div className="flex min-h-full items-center justify-center p-4">
            <Transition.Child
              as={Fragment}
              enter="ease-out duration-300"
              enterFrom="opacity-0 scale-95"
              enterTo="opacity-100 scale-100"
              leave="ease-in duration-200"
              leaveFrom="opacity-100 scale-100"
              leaveTo="opacity-0 scale-95"
            >
              <Dialog.Panel
                className={cn(
                  'w-full transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl transition-all',
                  sizes[size]
                )}
              >
                {(title || showCloseButton) && (
                  <div className="flex items-center justify-between mb-4">
                    {title && (
                      <Dialog.Title className="text-lg font-semibold text-secondary-900">
                        {title}
                      </Dialog.Title>
                    )}
                    {showCloseButton && (
                      <button
                        type="button"
                        className="rounded-lg p-1 text-secondary-400 hover:text-secondary-600 hover:bg-secondary-100"
                        onClick={onClose}
                      >
                        <XMarkIcon className="h-6 w-6" />
                      </button>
                    )}
                  </div>
                )}
                {children}
              </Dialog.Panel>
            </Transition.Child>
          </div>
        </div>
      </Dialog>
    </Transition>
  );
}
EOF
```

### Fichier à créer: `assets/react/components/common/index.js`

```bash
cat > assets/react/components/common/index.js << 'EOF'
export { Button } from './Button';
export { Input } from './Input';
export { Select } from './Select';
export { Textarea } from './Textarea';
export { Card, CardHeader, CardBody, CardFooter } from './Card';
export { Badge } from './Badge';
export { Spinner } from './Spinner';
export { Alert } from './Alert';
export { Modal } from './Modal';
EOF
```

### Validation
```bash
ls assets/react/components/common/
# Doit afficher tous les composants
```

---

## TASK 3.2: Créer les composants Layout

### Objectif
Créer les composants de mise en page

### Fichier à créer: `assets/react/components/layout/Header.jsx`

```bash
mkdir -p assets/react/components/layout

cat > assets/react/components/layout/Header.jsx << 'EOF'
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
EOF
```

### Fichier à créer: `assets/react/components/layout/Sidebar.jsx`

```bash
cat > assets/react/components/layout/Sidebar.jsx << 'EOF'
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
EOF
```

### Fichier à créer: `assets/react/components/layout/Layout.jsx`

```bash
cat > assets/react/components/layout/Layout.jsx << 'EOF'
import React, { useState } from 'react';
import { Outlet } from 'react-router-dom';
import { Header } from './Header';
import { Sidebar } from './Sidebar';
import { NotificationContainer } from './NotificationContainer';

export default function Layout() {
  const [sidebarOpen, setSidebarOpen] = useState(false);

  return (
    <div className="min-h-screen bg-secondary-50">
      <Sidebar isOpen={sidebarOpen} onClose={() => setSidebarOpen(false)} />
      
      <div className="lg:pl-64">
        <Header onMenuClick={() => setSidebarOpen(true)} />
        
        <main className="p-4 sm:p-6 lg:p-8">
          <Outlet />
        </main>
      </div>

      <NotificationContainer />
    </div>
  );
}
EOF
```

### Fichier à créer: `assets/react/components/layout/AuthLayout.jsx`

```bash
cat > assets/react/components/layout/AuthLayout.jsx << 'EOF'
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
EOF
```

### Fichier à créer: `assets/react/components/layout/NotificationContainer.jsx`

```bash
cat > assets/react/components/layout/NotificationContainer.jsx << 'EOF'
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
EOF
```

### Validation
```bash
ls assets/react/components/layout/
# Doit afficher tous les composants layout
```

---

# PHASE 4: PAGES DE L'APPLICATION

## TASK 4.1: Créer les pages d'authentification

### Objectif
Créer les pages Login et Register

### Fichier à créer: `assets/react/pages/Login.jsx`

```bash
mkdir -p assets/react/pages

cat > assets/react/pages/Login.jsx << 'EOF'
import React from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { useAuth } from '../hooks/useAuth';
import { loginSchema } from '../utils/validators';
import { Button, Input, Alert } from '../components/common';

export default function Login() {
  const navigate = useNavigate();
  const { login, isLoading } = useAuth();
  const [error, setError] = React.useState(null);

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm({
    resolver: zodResolver(loginSchema),
  });

  const onSubmit = async (data) => {
    setError(null);
    const result = await login(data.email, data.password);
    
    if (result.success) {
      navigate('/dashboard');
    } else {
      setError(result.error);
    }
  };

  return (
    <div className="space-y-6">
      <div className="text-center">
        <h1 className="text-2xl font-bold text-secondary-900">Connexion</h1>
        <p className="mt-2 text-secondary-600">
          Accédez à votre espace personnel
        </p>
      </div>

      {error && (
        <Alert variant="danger" onClose={() => setError(null)}>
          {error}
        </Alert>
      )}

      <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
        <Input
          label="Email"
          type="email"
          placeholder="votre@email.com"
          error={errors.email?.message}
          {...register('email')}
        />

        <Input
          label="Mot de passe"
          type="password"
          placeholder="••••••••"
          error={errors.password?.message}
          {...register('password')}
        />

        <div className="flex items-center justify-between">
          <label className="flex items-center">
            <input
              type="checkbox"
              className="rounded border-secondary-300 text-primary-600 focus:ring-primary-500"
            />
            <span className="ml-2 text-sm text-secondary-600">Se souvenir de moi</span>
          </label>
          <Link
            to="/forgot-password"
            className="text-sm text-primary-600 hover:text-primary-700"
          >
            Mot de passe oublié ?
          </Link>
        </div>

        <Button
          type="submit"
          className="w-full"
          size="lg"
          isLoading={isLoading}
        >
          Se connecter
        </Button>
      </form>

      <p className="text-center text-sm text-secondary-600">
        Pas encore de compte ?{' '}
        <Link
          to="/register"
          className="font-medium text-primary-600 hover:text-primary-700"
        >
          S'inscrire gratuitement
        </Link>
      </p>
    </div>
  );
}
EOF
```

### Fichier à créer: `assets/react/pages/Register.jsx`

```bash
cat > assets/react/pages/Register.jsx << 'EOF'
import React from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { useAuth } from '../hooks/useAuth';
import { useCountries } from '../hooks/useTemplates';
import { registerSchema } from '../utils/validators';
import { Button, Input, Select, Alert } from '../components/common';
import { useNotification } from '../hooks/useNotification';

export default function Register() {
  const navigate = useNavigate();
  const { register: registerUser, isLoading } = useAuth();
  const { data: countries, isLoading: countriesLoading } = useCountries();
  const { success } = useNotification();
  const [error, setError] = React.useState(null);

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm({
    resolver: zodResolver(registerSchema),
    defaultValues: {
      countryId: '',
    },
  });

  const onSubmit = async (data) => {
    setError(null);
    const result = await registerUser({
      ...data,
      countryId: parseInt(data.countryId),
    });
    
    if (result.success) {
      success('Compte créé !', 'Vous pouvez maintenant vous connecter');
      navigate('/login');
    } else {
      setError(result.error);
    }
  };

  const countryOptions = countries?.map((country) => ({
    value: country.id,
    label: country.name,
  })) || [];

  return (
    <div className="space-y-6">
      <div className="text-center">
        <h1 className="text-2xl font-bold text-secondary-900">Créer un compte</h1>
        <p className="mt-2 text-secondary-600">
          Commencez gratuitement avec 3 documents par mois
        </p>
      </div>

      {error && (
        <Alert variant="danger" onClose={() => setError(null)}>
          {error}
        </Alert>
      )}

      <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
        <Input
          label="Nom complet"
          type="text"
          placeholder="Aminata Diallo"
          error={errors.name?.message}
          required
          {...register('name')}
        />

        <Input
          label="Email"
          type="email"
          placeholder="votre@email.com"
          error={errors.email?.message}
          required
          {...register('email')}
        />

        <Input
          label="Téléphone"
          type="tel"
          placeholder="+221 77 123 45 67"
          error={errors.phone?.message}
          helpText="Format international recommandé"
          {...register('phone')}
        />

        <Select
          label="Pays"
          placeholder="Sélectionnez votre pays"
          options={countryOptions}
          error={errors.countryId?.message}
          disabled={countriesLoading}
          required
          {...register('countryId')}
        />

        <Input
          label="Mot de passe"
          type="password"
          placeholder="••••••••"
          error={errors.password?.message}
          helpText="Minimum 8 caractères"
          required
          {...register('password')}
        />

        <Input
          label="Confirmer le mot de passe"
          type="password"
          placeholder="••••••••"
          error={errors.confirmPassword?.message}
          required
          {...register('confirmPassword')}
        />

        <div className="flex items-start">
          <input
            type="checkbox"
            id="terms"
            className="mt-1 rounded border-secondary-300 text-primary-600 focus:ring-primary-500"
            required
          />
          <label htmlFor="terms" className="ml-2 text-sm text-secondary-600">
            J'accepte les{' '}
            <a href="#" className="text-primary-600 hover:underline">
              conditions d'utilisation
            </a>{' '}
            et la{' '}
            <a href="#" className="text-primary-600 hover:underline">
              politique de confidentialité
            </a>
          </label>
        </div>

        <Button
          type="submit"
          className="w-full"
          size="lg"
          isLoading={isLoading}
        >
          Créer mon compte
        </Button>
      </form>

      <p className="text-center text-sm text-secondary-600">
        Déjà inscrit ?{' '}
        <Link
          to="/login"
          className="font-medium text-primary-600 hover:text-primary-700"
        >
          Se connecter
        </Link>
      </p>
    </div>
  );
}
EOF
```

### Validation
```bash
ls assets/react/pages/
# Doit afficher Login.jsx et Register.jsx
```

---

## TASK 4.2: Créer les pages principales

### Fichier à créer: `assets/react/pages/Home.jsx`

```bash
cat > assets/react/pages/Home.jsx << 'EOF'
import React from 'react';
import { Link } from 'react-router-dom';
import {
  DocumentTextIcon,
  SparklesIcon,
  GlobeAltIcon,
  ShieldCheckIcon,
  ArrowRightIcon,
} from '@heroicons/react/24/outline';
import { Button } from '../components/common';
import { useAuth } from '../hooks/useAuth';

const features = [
  {
    icon: DocumentTextIcon,
    title: '10+ types de documents',
    description: 'Demandes d\'emploi, lettres de motivation, attestations et bien plus',
  },
  {
    icon: SparklesIcon,
    title: 'Génération par IA',
    description: 'Des documents professionnels générés en quelques secondes',
  },
  {
    icon: GlobeAltIcon,
    title: 'Adapté à l\'Afrique',
    description: 'Formats et formules conformes aux normes locales',
  },
  {
    icon: ShieldCheckIcon,
    title: 'Sécurisé',
    description: 'Vos données sont protégées et ne sont jamais partagées',
  },
];

export default function Home() {
  const { isAuthenticated } = useAuth();

  return (
    <div className="min-h-screen bg-white">
      {/* Header */}
      <header className="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-secondary-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between h-16">
            <div className="flex items-center">
              <span className="text-xl font-bold text-primary-600">DocGen</span>
              <span className="text-xl font-light text-secondary-600 ml-1">Africa</span>
            </div>
            
            <div className="flex items-center gap-4">
              {isAuthenticated ? (
                <Link to="/dashboard">
                  <Button>Tableau de bord</Button>
                </Link>
              ) : (
                <>
                  <Link to="/login">
                    <Button variant="ghost">Connexion</Button>
                  </Link>
                  <Link to="/register">
                    <Button>S'inscrire</Button>
                  </Link>
                </>
              )}
            </div>
          </div>
        </div>
      </header>

      {/* Hero */}
      <section className="pt-32 pb-20 px-4 sm:px-6 lg:px-8">
        <div className="max-w-4xl mx-auto text-center">
          <h1 className="text-4xl sm:text-5xl lg:text-6xl font-bold text-secondary-900 leading-tight">
            Générez vos documents administratifs{' '}
            <span className="text-primary-600">en quelques clics</span>
          </h1>
          <p className="mt-6 text-xl text-secondary-600 max-w-2xl mx-auto">
            Fini les modèles inadaptés et les erreurs de forme. DocGen Africa génère 
            des documents professionnels conformes aux normes de votre pays.
          </p>
          <div className="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <Link to="/register">
              <Button size="lg" rightIcon={<ArrowRightIcon className="h-5 w-5" />}>
                Commencer gratuitement
              </Button>
            </Link>
            <Link to="#features">
              <Button variant="outline" size="lg">
                En savoir plus
              </Button>
            </Link>
          </div>
          <p className="mt-4 text-sm text-secondary-500">
            3 documents gratuits par mois • Aucune carte bancaire requise
          </p>
        </div>
      </section>

      {/* Features */}
      <section id="features" className="py-20 bg-secondary-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl font-bold text-secondary-900">
              Pourquoi choisir DocGen Africa ?
            </h2>
            <p className="mt-4 text-lg text-secondary-600">
              Une solution conçue pour simplifier vos démarches administratives
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            {features.map((feature) => (
              <div
                key={feature.title}
                className="bg-white p-6 rounded-xl shadow-sm border border-secondary-200"
              >
                <div className="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-4">
                  <feature.icon className="h-6 w-6 text-primary-600" />
                </div>
                <h3 className="font-semibold text-secondary-900">{feature.title}</h3>
                <p className="mt-2 text-sm text-secondary-600">{feature.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-20 bg-gradient-to-r from-primary-600 to-primary-800">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-3xl font-bold text-white">
            Prêt à simplifier vos démarches ?
          </h2>
          <p className="mt-4 text-xl text-primary-100">
            Rejoignez des milliers d'utilisateurs qui gagnent du temps chaque jour
          </p>
          <div className="mt-8">
            <Link to="/register">
              <Button
                size="lg"
                variant="secondary"
                className="bg-white text-primary-600 hover:bg-primary-50"
              >
                Créer mon compte gratuit
              </Button>
            </Link>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="py-12 bg-secondary-900">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col md:flex-row items-center justify-between">
            <div className="flex items-center">
              <span className="text-xl font-bold text-white">DocGen</span>
              <span className="text-xl font-light text-secondary-400 ml-1">Africa</span>
            </div>
            <p className="mt-4 md:mt-0 text-secondary-400 text-sm">
              © 2026 DocGen Africa. Tous droits réservés.
            </p>
          </div>
        </div>
      </footer>
    </div>
  );
}
EOF
```

### Fichier à créer: `assets/react/pages/Dashboard.jsx`

```bash
cat > assets/react/pages/Dashboard.jsx << 'EOF'
import React from 'react';
import { Link } from 'react-router-dom';
import {
  DocumentTextIcon,
  PlusIcon,
  ClockIcon,
  CheckCircleIcon,
} from '@heroicons/react/24/outline';
import { useAuth } from '../hooks/useAuth';
import { useDocuments } from '../hooks/useDocuments';
import { Button, Card, CardBody, Badge, Spinner } from '../components/common';
import { formatRelativeDate } from '../utils/helpers';
import { DOCUMENT_STATUS_LABELS, DOCUMENT_STATUS_COLORS } from '../utils/constants';

export default function Dashboard() {
  const { user } = useAuth();
  const { data, isLoading } = useDocuments({ page: 1, itemsPerPage: 5 });

  const recentDocuments = data?.data || [];
  const stats = {
    total: data?.total || 0,
    completed: recentDocuments.filter((d) => d.status === 'completed').length,
    pending: recentDocuments.filter((d) => d.status === 'generating').length,
  };

  return (
    <div className="space-y-8">
      {/* Welcome */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-secondary-900">
            Bonjour, {user?.name?.split(' ')[0]} 👋
          </h1>
          <p className="mt-1 text-secondary-600">
            Bienvenue sur votre tableau de bord DocGen Africa
          </p>
        </div>
        <Link to="/documents/new">
          <Button leftIcon={<PlusIcon className="h-5 w-5" />}>
            Nouveau document
          </Button>
        </Link>
      </div>

      {/* Stats */}
      <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <Card>
          <CardBody className="flex items-center gap-4">
            <div className="p-3 bg-primary-100 rounded-lg">
              <DocumentTextIcon className="h-6 w-6 text-primary-600" />
            </div>
            <div>
              <p className="text-sm text-secondary-500">Total documents</p>
              <p className="text-2xl font-bold text-secondary-900">{stats.total}</p>
            </div>
          </CardBody>
        </Card>

        <Card>
          <CardBody className="flex items-center gap-4">
            <div className="p-3 bg-success-50 rounded-lg">
              <CheckCircleIcon className="h-6 w-6 text-success-500" />
            </div>
            <div>
              <p className="text-sm text-secondary-500">Complétés</p>
              <p className="text-2xl font-bold text-secondary-900">{stats.completed}</p>
            </div>
          </CardBody>
        </Card>

        <Card>
          <CardBody className="flex items-center gap-4">
            <div className="p-3 bg-warning-50 rounded-lg">
              <ClockIcon className="h-6 w-6 text-warning-500" />
            </div>
            <div>
              <p className="text-sm text-secondary-500">En cours</p>
              <p className="text-2xl font-bold text-secondary-900">{stats.pending}</p>
            </div>
          </CardBody>
        </Card>

        <Card>
          <CardBody className="flex items-center gap-4">
            <div className="p-3 bg-secondary-100 rounded-lg">
              <DocumentTextIcon className="h-6 w-6 text-secondary-600" />
            </div>
            <div>
              <p className="text-sm text-secondary-500">Quota restant</p>
              <p className="text-2xl font-bold text-secondary-900">
                {user?.quota?.remaining || 0}
              </p>
            </div>
          </CardBody>
        </Card>
      </div>

      {/* Recent documents */}
      <Card>
        <div className="px-6 py-4 border-b border-secondary-200 flex items-center justify-between">
          <h2 className="text-lg font-semibold text-secondary-900">
            Documents récents
          </h2>
          <Link
            to="/documents"
            className="text-sm text-primary-600 hover:text-primary-700"
          >
            Voir tout →
          </Link>
        </div>
        <CardBody className="p-0">
          {isLoading ? (
            <div className="flex justify-center py-8">
              <Spinner />
            </div>
          ) : recentDocuments.length === 0 ? (
            <div className="text-center py-12">
              <DocumentTextIcon className="h-12 w-12 text-secondary-300 mx-auto" />
              <p className="mt-4 text-secondary-500">Aucun document pour le moment</p>
              <Link to="/documents/new" className="mt-4 inline-block">
                <Button size="sm">Créer mon premier document</Button>
              </Link>
            </div>
          ) : (
            <ul className="divide-y divide-secondary-200">
              {recentDocuments.map((doc) => (
                <li key={doc.id}>
                  <Link
                    to={`/documents/${doc.id}`}
                    className="flex items-center justify-between px-6 py-4 hover:bg-secondary-50 transition-colors"
                  >
                    <div className="flex items-center gap-4">
                      <div className="p-2 bg-secondary-100 rounded-lg">
                        <DocumentTextIcon className="h-5 w-5 text-secondary-600" />
                      </div>
                      <div>
                        <p className="font-medium text-secondary-900">{doc.title}</p>
                        <p className="text-sm text-secondary-500">
                          {doc.template?.documentType?.name}
                        </p>
                      </div>
                    </div>
                    <div className="flex items-center gap-4">
                      <Badge variant={DOCUMENT_STATUS_COLORS[doc.status]}>
                        {DOCUMENT_STATUS_LABELS[doc.status]}
                      </Badge>
                      <span className="text-sm text-secondary-500">
                        {formatRelativeDate(doc.createdAt)}
                      </span>
                    </div>
                  </Link>
                </li>
              ))}
            </ul>
          )}
        </CardBody>
      </Card>
    </div>
  );
}
EOF
```

### Fichier à créer: `assets/react/pages/Documents.jsx`

```bash
cat > assets/react/pages/Documents.jsx << 'EOF'
import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import {
  PlusIcon,
  DocumentTextIcon,
  TrashIcon,
  EyeIcon,
  MagnifyingGlassIcon,
} from '@heroicons/react/24/outline';
import { useDocuments, useDeleteDocument } from '../hooks/useDocuments';
import {
  Button,
  Card,
  CardBody,
  Badge,
  Spinner,
  Input,
  Modal,
} from '../components/common';
import { formatDate, truncate } from '../utils/helpers';
import { DOCUMENT_STATUS_LABELS, DOCUMENT_STATUS_COLORS } from '../utils/constants';

export default function Documents() {
  const [search, setSearch] = useState('');
  const [deleteModal, setDeleteModal] = useState({ open: false, document: null });
  const { data, isLoading } = useDocuments({ title: search || undefined });
  const deleteDocument = useDeleteDocument();

  const documents = data?.data || [];

  const handleDelete = async () => {
    if (deleteModal.document) {
      await deleteDocument.mutateAsync(deleteModal.document.id);
      setDeleteModal({ open: false, document: null });
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-secondary-900">Mes documents</h1>
          <p className="mt-1 text-secondary-600">
            Gérez tous vos documents administratifs
          </p>
        </div>
        <Link to="/documents/new">
          <Button leftIcon={<PlusIcon className="h-5 w-5" />}>
            Nouveau document
          </Button>
        </Link>
      </div>

      {/* Search */}
      <div className="relative max-w-md">
        <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-secondary-400" />
        <Input
          type="search"
          placeholder="Rechercher un document..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="pl-10"
        />
      </div>

      {/* Documents list */}
      {isLoading ? (
        <div className="flex justify-center py-12">
          <Spinner size="lg" />
        </div>
      ) : documents.length === 0 ? (
        <Card>
          <CardBody className="text-center py-12">
            <DocumentTextIcon className="h-12 w-12 text-secondary-300 mx-auto" />
            <p className="mt-4 text-secondary-500">
              {search ? 'Aucun document trouvé' : 'Vous n\'avez pas encore de document'}
            </p>
            {!search && (
              <Link to="/documents/new" className="mt-4 inline-block">
                <Button>Créer mon premier document</Button>
              </Link>
            )}
          </CardBody>
        </Card>
      ) : (
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {documents.map((doc) => (
            <Card key={doc.id} className="hover:shadow-md transition-shadow">
              <CardBody>
                <div className="flex items-start justify-between">
                  <div className="flex-1 min-w-0">
                    <Badge variant={DOCUMENT_STATUS_COLORS[doc.status]} className="mb-2">
                      {DOCUMENT_STATUS_LABELS[doc.status]}
                    </Badge>
                    <h3 className="font-semibold text-secondary-900 truncate">
                      {doc.title}
                    </h3>
                    <p className="text-sm text-secondary-500 mt-1">
                      {doc.template?.documentType?.name}
                    </p>
                  </div>
                </div>

                <p className="mt-3 text-sm text-secondary-600">
                  {truncate(doc.generatedContent || 'Brouillon en attente de génération', 100)}
                </p>

                <div className="mt-4 pt-4 border-t border-secondary-200 flex items-center justify-between">
                  <span className="text-xs text-secondary-500">
                    {formatDate(doc.createdAt)}
                  </span>
                  <div className="flex items-center gap-2">
                    <Link to={`/documents/${doc.id}`}>
                      <Button variant="ghost" size="sm">
                        <EyeIcon className="h-4 w-4" />
                      </Button>
                    </Link>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => setDeleteModal({ open: true, document: doc })}
                    >
                      <TrashIcon className="h-4 w-4 text-danger-500" />
                    </Button>
                  </div>
                </div>
              </CardBody>
            </Card>
          ))}
        </div>
      )}

      {/* Delete confirmation modal */}
      <Modal
        isOpen={deleteModal.open}
        onClose={() => setDeleteModal({ open: false, document: null })}
        title="Supprimer le document"
        size="sm"
      >
        <p className="text-secondary-600">
          Êtes-vous sûr de vouloir supprimer "{deleteModal.document?.title}" ?
          Cette action est irréversible.
        </p>
        <div className="mt-6 flex justify-end gap-3">
          <Button
            variant="secondary"
            onClick={() => setDeleteModal({ open: false, document: null })}
          >
            Annuler
          </Button>
          <Button
            variant="danger"
            onClick={handleDelete}
            isLoading={deleteDocument.isPending}
          >
            Supprimer
          </Button>
        </div>
      </Modal>
    </div>
  );
}
EOF
```

### Fichier à créer: `assets/react/pages/DocumentNew.jsx`

```bash
cat > assets/react/pages/DocumentNew.jsx << 'EOF'
import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { ArrowLeftIcon, ArrowRightIcon } from '@heroicons/react/24/outline';
import { useAuth } from '../hooks/useAuth';
import { useCountries, useTemplatesByCountry, useTemplate } from '../hooks/useTemplates';
import { useCreateDocument } from '../hooks/useDocuments';
import { documentSchema } from '../utils/validators';
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  CardFooter,
  Input,
  Select,
  Textarea,
  Alert,
  Spinner,
} from '../components/common';

const STEPS = ['Sélection', 'Informations', 'Confirmation'];

export default function DocumentNew() {
  const navigate = useNavigate();
  const { user } = useAuth();
  const [step, setStep] = useState(0);
  const [selectedCountry, setSelectedCountry] = useState(user?.country?.id || '');
  const [selectedTemplate, setSelectedTemplate] = useState('');

  const { data: countries, isLoading: countriesLoading } = useCountries();
  const { data: templates, isLoading: templatesLoading } = useTemplatesByCountry(
    selectedCountry || undefined
  );
  const { data: template } = useTemplate(selectedTemplate || undefined);
  const createDocument = useCreateDocument();

  const {
    register,
    handleSubmit,
    watch,
    formState: { errors },
  } = useForm({
    resolver: zodResolver(documentSchema),
    defaultValues: {
      title: '',
      templateId: '',
      inputData: {},
    },
  });

  const countryOptions = countries?.map((c) => ({
    value: c.id,
    label: c.name,
  })) || [];

  const templateOptions = templates?.map((t) => ({
    value: t.id,
    label: `${t.documentType?.name} - ${t.toneLevel}`,
  })) || [];

  const fieldsSchema = template?.fieldsSchema || {};

  const onSubmit = async (data) => {
    const result = await createDocument.mutateAsync({
      templateId: parseInt(selectedTemplate),
      title: data.title,
      inputData: data.inputData,
    });
    
    if (result) {
      navigate(`/documents/${result.id}`);
    }
  };

  const canProceed = () => {
    if (step === 0) return selectedCountry && selectedTemplate;
    if (step === 1) return watch('title');
    return true;
  };

  return (
    <div className="max-w-3xl mx-auto">
      {/* Progress */}
      <div className="mb-8">
        <div className="flex items-center justify-between">
          {STEPS.map((label, index) => (
            <div key={label} className="flex items-center">
              <div
                className={`w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium ${
                  index <= step
                    ? 'bg-primary-600 text-white'
                    : 'bg-secondary-200 text-secondary-500'
                }`}
              >
                {index + 1}
              </div>
              <span
                className={`ml-2 text-sm ${
                  index <= step ? 'text-primary-600 font-medium' : 'text-secondary-500'
                }`}
              >
                {label}
              </span>
              {index < STEPS.length - 1 && (
                <div
                  className={`w-12 h-0.5 mx-4 ${
                    index < step ? 'bg-primary-600' : 'bg-secondary-200'
                  }`}
                />
              )}
            </div>
          ))}
        </div>
      </div>

      <form onSubmit={handleSubmit(onSubmit)}>
        {/* Step 1: Selection */}
        {step === 0 && (
          <Card>
            <CardHeader>
              <h2 className="text-lg font-semibold">Sélectionnez le type de document</h2>
            </CardHeader>
            <CardBody className="space-y-4">
              <Select
                label="Pays"
                placeholder="Sélectionnez votre pays"
                options={countryOptions}
                value={selectedCountry}
                onChange={(e) => {
                  setSelectedCountry(e.target.value);
                  setSelectedTemplate('');
                }}
                disabled={countriesLoading}
                required
              />

              {selectedCountry && (
                templatesLoading ? (
                  <div className="flex justify-center py-4">
                    <Spinner />
                  </div>
                ) : (
                  <Select
                    label="Type de document"
                    placeholder="Sélectionnez un type"
                    options={templateOptions}
                    value={selectedTemplate}
                    onChange={(e) => setSelectedTemplate(e.target.value)}
                    required
                  />
                )
              )}
            </CardBody>
          </Card>
        )}

        {/* Step 2: Form fields */}
        {step === 1 && (
          <Card>
            <CardHeader>
              <h2 className="text-lg font-semibold">Remplissez les informations</h2>
            </CardHeader>
            <CardBody className="space-y-4">
              <Input
                label="Titre du document"
                placeholder="Ex: Ma demande de stage chez ABC"
                error={errors.title?.message}
                required
                {...register('title')}
              />

              {Object.entries(fieldsSchema).map(([key, field]) => {
                const commonProps = {
                  key,
                  label: field.label,
                  placeholder: field.placeholder,
                  required: field.required,
                  ...register(`inputData.${key}`),
                };

                if (field.type === 'text') {
                  return <Textarea {...commonProps} rows={4} />;
                }
                return <Input {...commonProps} type={field.type || 'text'} />;
              })}
            </CardBody>
          </Card>
        )}

        {/* Step 3: Confirmation */}
        {step === 2 && (
          <Card>
            <CardHeader>
              <h2 className="text-lg font-semibold">Confirmer et créer</h2>
            </CardHeader>
            <CardBody>
              <Alert variant="info">
                Votre document sera créé en tant que brouillon. Vous pourrez le modifier
                avant de lancer la génération par IA.
              </Alert>

              <div className="mt-4 space-y-2">
                <p>
                  <strong>Type :</strong> {template?.documentType?.name}
                </p>
                <p>
                  <strong>Titre :</strong> {watch('title')}
                </p>
                <p>
                  <strong>Pays :</strong>{' '}
                  {countries?.find((c) => c.id == selectedCountry)?.name}
                </p>
              </div>
            </CardBody>
          </Card>
        )}

        {/* Navigation */}
        <div className="mt-6 flex items-center justify-between">
          <Button
            type="button"
            variant="secondary"
            onClick={() => (step > 0 ? setStep(step - 1) : navigate('/documents'))}
            leftIcon={<ArrowLeftIcon className="h-4 w-4" />}
          >
            {step > 0 ? 'Précédent' : 'Annuler'}
          </Button>

          {step < STEPS.length - 1 ? (
            <Button
              type="button"
              onClick={() => setStep(step + 1)}
              disabled={!canProceed()}
              rightIcon={<ArrowRightIcon className="h-4 w-4" />}
            >
              Suivant
            </Button>
          ) : (
            <Button
              type="submit"
              isLoading={createDocument.isPending}
            >
              Créer le document
            </Button>
          )}
        </div>
      </form>
    </div>
  );
}
EOF
```

### Fichier à créer: `assets/react/pages/DocumentView.jsx`

```bash
cat > assets/react/pages/DocumentView.jsx << 'EOF'
import React from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import {
  ArrowLeftIcon,
  ArrowDownTrayIcon,
  SparklesIcon,
  ArrowPathIcon,
  DocumentTextIcon,
} from '@heroicons/react/24/outline';
import { useDocument, useGenerateDocument, useExportDocument } from '../hooks/useDocuments';
import { useAuth } from '../hooks/useAuth';
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  Badge,
  Spinner,
  Alert,
} from '../components/common';
import { formatDateTime, canExportDocx } from '../utils/helpers';
import { DOCUMENT_STATUS_LABELS, DOCUMENT_STATUS_COLORS } from '../utils/constants';

export default function DocumentView() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { user } = useAuth();
  const { data: document, isLoading, isError } = useDocument(id);
  const generateDocument = useGenerateDocument();
  const exportDocument = useExportDocument();

  if (isLoading) {
    return (
      <div className="flex justify-center py-12">
        <Spinner size="lg" />
      </div>
    );
  }

  if (isError || !document) {
    return (
      <div className="text-center py-12">
        <Alert variant="danger">
          Document non trouvé ou vous n'avez pas les permissions nécessaires.
        </Alert>
        <Link to="/documents" className="mt-4 inline-block">
          <Button variant="secondary">Retour aux documents</Button>
        </Link>
      </div>
    );
  }

  const isCompleted = document.status === 'completed';
  const isGenerating = document.status === 'generating';
  const canGenerate = document.status === 'draft' || document.status === 'failed';

  const handleGenerate = () => {
    generateDocument.mutate({ id: document.id, regenerate: false });
  };

  const handleRegenerate = () => {
    generateDocument.mutate({ id: document.id, regenerate: true });
  };

  const handleExport = (format) => {
    exportDocument.mutate({
      id: document.id,
      format,
      filename: document.title,
    });
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center gap-4">
        <Button
          variant="ghost"
          onClick={() => navigate('/documents')}
          leftIcon={<ArrowLeftIcon className="h-4 w-4" />}
        >
          Retour
        </Button>
      </div>

      {/* Document info */}
      <Card>
        <CardHeader className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <Badge variant={DOCUMENT_STATUS_COLORS[document.status]} className="mb-2">
              {DOCUMENT_STATUS_LABELS[document.status]}
            </Badge>
            <h1 className="text-xl font-bold text-secondary-900">{document.title}</h1>
            <p className="text-sm text-secondary-500 mt-1">
              {document.template?.documentType?.name} • Créé le {formatDateTime(document.createdAt)}
            </p>
          </div>
          <div className="flex flex-wrap gap-2">
            {canGenerate && (
              <Button
                onClick={handleGenerate}
                isLoading={generateDocument.isPending}
                leftIcon={<SparklesIcon className="h-4 w-4" />}
              >
                Générer
              </Button>
            )}
            {isCompleted && (
              <>
                <Button
                  variant="secondary"
                  onClick={handleRegenerate}
                  isLoading={generateDocument.isPending}
                  leftIcon={<ArrowPathIcon className="h-4 w-4" />}
                >
                  Regénérer
                </Button>
                <Button
                  variant="success"
                  onClick={() => handleExport('pdf')}
                  isLoading={exportDocument.isPending}
                  leftIcon={<ArrowDownTrayIcon className="h-4 w-4" />}
                >
                  PDF
                </Button>
                {canExportDocx(user?.subscription?.type) && (
                  <Button
                    variant="outline"
                    onClick={() => handleExport('docx')}
                    isLoading={exportDocument.isPending}
                    leftIcon={<ArrowDownTrayIcon className="h-4 w-4" />}
                  >
                    DOCX
                  </Button>
                )}
              </>
            )}
          </div>
        </CardHeader>
      </Card>

      {/* Content */}
      <Card>
        <CardHeader>
          <h2 className="font-semibold text-secondary-900 flex items-center gap-2">
            <DocumentTextIcon className="h-5 w-5" />
            Contenu du document
          </h2>
        </CardHeader>
        <CardBody>
          {isGenerating && (
            <div className="text-center py-8">
              <Spinner size="lg" />
              <p className="mt-4 text-secondary-600">
                Génération en cours... Veuillez patienter.
              </p>
            </div>
          )}

          {canGenerate && !generateDocument.isPending && (
            <div className="text-center py-8">
              <SparklesIcon className="h-12 w-12 text-secondary-300 mx-auto" />
              <p className="mt-4 text-secondary-600">
                {document.status === 'failed'
                  ? 'La génération a échoué. Vous pouvez réessayer.'
                  : 'Cliquez sur "Générer" pour créer le contenu de votre document.'}
              </p>
            </div>
          )}

          {isCompleted && document.generatedContent && (
            <div className="prose prose-sm max-w-none">
              <pre className="whitespace-pre-wrap font-sans text-secondary-800 leading-relaxed">
                {document.generatedContent}
              </pre>
            </div>
          )}
        </CardBody>
      </Card>

      {/* Input data */}
      <Card>
        <CardHeader>
          <h2 className="font-semibold text-secondary-900">Informations saisies</h2>
        </CardHeader>
        <CardBody>
          <dl className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {Object.entries(document.inputData || {}).map(([key, value]) => (
              <div key={key}>
                <dt className="text-sm font-medium text-secondary-500">
                  {key.replace(/_/g, ' ')}
                </dt>
                <dd className="mt-1 text-sm text-secondary-900">{value || '-'}</dd>
              </div>
            ))}
          </dl>
        </CardBody>
      </Card>
    </div>
  );
}
EOF
```

### Fichier à créer: `assets/react/pages/Profile.jsx`

```bash
cat > assets/react/pages/Profile.jsx << 'EOF'
import React from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { useAuth } from '../hooks/useAuth';
import { profileSchema } from '../utils/validators';
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  CardFooter,
  Input,
  Badge,
} from '../components/common';
import { useNotification } from '../hooks/useNotification';
import { SUBSCRIPTION_LABELS } from '../utils/constants';
import { formatDate } from '../utils/helpers';

export default function Profile() {
  const { user, refreshUser } = useAuth();
  const { success } = useNotification();

  const {
    register,
    handleSubmit,
    formState: { errors, isDirty },
  } = useForm({
    resolver: zodResolver(profileSchema),
    defaultValues: {
      name: user?.name || '',
      phone: user?.phone || '',
    },
  });

  const onSubmit = async (data) => {
    // TODO: Implement profile update API
    console.log('Update profile:', data);
    success('Profil mis à jour', 'Vos informations ont été enregistrées');
    refreshUser();
  };

  return (
    <div className="max-w-2xl mx-auto space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-secondary-900">Mon profil</h1>
        <p className="mt-1 text-secondary-600">
          Gérez vos informations personnelles
        </p>
      </div>

      {/* Profile info */}
      <Card>
        <CardHeader>
          <h2 className="font-semibold">Informations personnelles</h2>
        </CardHeader>
        <form onSubmit={handleSubmit(onSubmit)}>
          <CardBody className="space-y-4">
            <Input
              label="Nom complet"
              error={errors.name?.message}
              {...register('name')}
            />
            <Input
              label="Email"
              type="email"
              value={user?.email || ''}
              disabled
              helpText="L'email ne peut pas être modifié"
            />
            <Input
              label="Téléphone"
              error={errors.phone?.message}
              {...register('phone')}
            />
            <Input
              label="Pays"
              value={user?.country?.name || ''}
              disabled
            />
          </CardBody>
          <CardFooter className="flex justify-end">
            <Button type="submit" disabled={!isDirty}>
              Enregistrer
            </Button>
          </CardFooter>
        </form>
      </Card>

      {/* Subscription */}
      <Card>
        <CardHeader>
          <h2 className="font-semibold">Abonnement</h2>
        </CardHeader>
        <CardBody>
          <div className="flex items-center justify-between">
            <div>
              <p className="font-medium text-secondary-900">
                {SUBSCRIPTION_LABELS[user?.subscription?.type] || 'Gratuit'}
              </p>
              {user?.subscription?.endsAt && (
                <p className="text-sm text-secondary-500">
                  Expire le {formatDate(user.subscription.endsAt)}
                </p>
              )}
            </div>
            <Badge variant={user?.subscription?.type === 'free' ? 'secondary' : 'success'}>
              {user?.subscription?.type === 'free' ? 'Gratuit' : 'Actif'}
            </Badge>
          </div>

          <div className="mt-6 p-4 bg-secondary-50 rounded-lg">
            <p className="text-sm font-medium text-secondary-900">
              Utilisation ce mois-ci
            </p>
            <div className="mt-2 flex items-baseline gap-2">
              <span className="text-3xl font-bold text-primary-600">
                {user?.quota?.used || 0}
              </span>
              <span className="text-secondary-500">
                / {user?.quota?.limit || 3} documents
              </span>
            </div>
            <div className="mt-2 h-2 bg-secondary-200 rounded-full overflow-hidden">
              <div
                className="h-full bg-primary-600 rounded-full"
                style={{
                  width: `${Math.min(
                    100,
                    ((user?.quota?.used || 0) / (user?.quota?.limit || 3)) * 100
                  )}%`,
                }}
              />
            </div>
          </div>

          {user?.subscription?.type === 'free' && (
            <div className="mt-4">
              <Button variant="outline" className="w-full">
                Passer à Premium
              </Button>
            </div>
          )}
        </CardBody>
      </Card>
    </div>
  );
}
EOF
```

### Fichier à créer: `assets/react/pages/NotFound.jsx`

```bash
cat > assets/react/pages/NotFound.jsx << 'EOF'
import React from 'react';
import { Link } from 'react-router-dom';
import { HomeIcon } from '@heroicons/react/24/outline';
import { Button } from '../components/common';

export default function NotFound() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-secondary-50 px-4">
      <div className="text-center">
        <h1 className="text-9xl font-bold text-primary-600">404</h1>
        <h2 className="mt-4 text-2xl font-semibold text-secondary-900">
          Page non trouvée
        </h2>
        <p className="mt-2 text-secondary-600">
          Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
        </p>
        <div className="mt-8">
          <Link to="/">
            <Button leftIcon={<HomeIcon className="h-5 w-5" />}>
              Retour à l'accueil
            </Button>
          </Link>
        </div>
      </div>
    </div>
  );
}
EOF
```

### Validation
```bash
ls assets/react/pages/
# Doit afficher toutes les pages
```

---

# PHASE 5: CONFIGURATION SYMFONY

## TASK 5.1: Créer le template Twig principal

### Objectif
Créer le template Twig qui charge l'application React

### Fichier à créer: `templates/base.html.twig`

```bash
cat > templates/base.html.twig << 'EOF'
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DocGen Africa - Générateur de documents administratifs africains">
    <title>{% block title %}DocGen Africa{% endblock %}</title>
    
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    
    {% block stylesheets %}
        {{ encore_entry_link_tags('app') }}
    {% endblock %}
</head>
<body>
    {% block body %}{% endblock %}
    
    {% block javascripts %}
        {{ encore_entry_script_tags('app') }}
    {% endblock %}
</body>
</html>
EOF
```

### Fichier à créer: `templates/spa.html.twig`

```bash
cat > templates/spa.html.twig << 'EOF'
{% extends 'base.html.twig' %}

{% block body %}
    <div id="root"></div>
    <noscript>
        <div style="padding: 2rem; text-align: center; font-family: system-ui, sans-serif;">
            <h1>JavaScript requis</h1>
            <p>Cette application nécessite JavaScript pour fonctionner.</p>
            <p>Veuillez activer JavaScript dans votre navigateur.</p>
        </div>
    </noscript>
{% endblock %}
EOF
```

### Validation
```bash
ls templates/
# Doit afficher base.html.twig et spa.html.twig
```

---

## TASK 5.2: Créer le Controller SPA

### Objectif
Créer le controller qui sert l'application React

### Fichier à créer: `src/Controller/SpaController.php`

```bash
cat > src/Controller/SpaController.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SpaController extends AbstractController
{
    /**
     * Catch-all route for the SPA
     * This must be loaded LAST to not override API routes
     */
    #[Route(
        '/{reactRouting}',
        name: 'spa',
        requirements: ['reactRouting' => '^(?!api|_).*'],
        defaults: ['reactRouting' => null],
        priority: -1
    )]
    public function index(): Response
    {
        return $this->render('spa.html.twig');
    }
}
EOF
```

### Validation
```bash
php bin/console debug:router spa
# Doit afficher la route SPA
```

---

## TASK 5.3: Compiler les assets

### Objectif
Compiler les assets avec Webpack Encore

### Commandes à exécuter

```bash
# Développement (avec watch)
npm run watch

# OU Production
npm run build
```

### Validation
```bash
ls public/build/
# Doit afficher les fichiers compilés (app.js, app.css, etc.)
```

---

# PHASE 6: LANCEMENT ET TEST

## TASK 6.1: Lancer l'application

### Objectif
Démarrer l'application complète

### Commandes à exécuter

```bash
# Terminal 1: Serveur Symfony
symfony serve

# Terminal 2: Webpack watch (développement)
npm run watch

# Terminal 3: Messenger worker (si nécessaire)
php bin/console messenger:consume async -vv
```

### Validation

Ouvrir dans le navigateur:
- http://127.0.0.1:8000 → Page d'accueil
- http://127.0.0.1:8000/login → Page de connexion
- http://127.0.0.1:8000/api/docs → Documentation API

---

## RÉCAPITULATIF DE LA STRUCTURE FINALE

```
docgen-africa-api/
├── assets/
│   ├── app.js
│   ├── styles/
│   │   └── app.css
│   └── react/
│       ├── index.jsx
│       ├── App.jsx
│       ├── api/
│       │   ├── client.js
│       │   ├── auth.js
│       │   ├── documents.js
│       │   └── templates.js
│       ├── components/
│       │   ├── common/
│       │   │   ├── Button.jsx
│       │   │   ├── Input.jsx
│       │   │   ├── Select.jsx
│       │   │   ├── Textarea.jsx
│       │   │   ├── Card.jsx
│       │   │   ├── Badge.jsx
│       │   │   ├── Spinner.jsx
│       │   │   ├── Alert.jsx
│       │   │   ├── Modal.jsx
│       │   │   └── index.js
│       │   └── layout/
│       │       ├── Header.jsx
│       │       ├── Sidebar.jsx
│       │       ├── Layout.jsx
│       │       ├── AuthLayout.jsx
│       │       └── NotificationContainer.jsx
│       ├── contexts/
│       │   ├── AuthContext.jsx
│       │   └── NotificationContext.jsx
│       ├── hooks/
│       │   ├── useAuth.js
│       │   ├── useNotification.js
│       │   ├── useDocuments.js
│       │   ├── useTemplates.js
│       │   └── useLocalStorage.js
│       ├── pages/
│       │   ├── Home.jsx
│       │   ├── Login.jsx
│       │   ├── Register.jsx
│       │   ├── Dashboard.jsx
│       │   ├── Documents.jsx
│       │   ├── DocumentNew.jsx
│       │   ├── DocumentView.jsx
│       │   ├── Profile.jsx
│       │   └── NotFound.jsx
│       └── utils/
│           ├── constants.js
│           ├── helpers.js
│           └── validators.js
├── config/
├── public/
│   └── build/          # Fichiers compilés
├── src/
│   └── Controller/
│       └── SpaController.php
├── templates/
│   ├── base.html.twig
│   └── spa.html.twig
├── package.json
├── webpack.config.js
├── tailwind.config.js
└── postcss.config.js
```

---

# FIN DU GUIDE FRONTEND REACT + TAILWINDCSS
