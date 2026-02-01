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
