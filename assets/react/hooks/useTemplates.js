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
