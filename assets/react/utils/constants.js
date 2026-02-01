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
