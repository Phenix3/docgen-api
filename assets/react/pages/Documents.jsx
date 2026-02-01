import React from 'react';
import { Link } from 'react-router-dom';
import { 
  DocumentTextIcon, 
  PlusIcon,
  PencilIcon,
  TrashIcon,
  EyeIcon
} from '@heroicons/react/24/outline';
import { useDocuments, useDeleteDocument } from '../hooks/useDocuments';
import { Card, CardHeader, CardBody, Button, Spinner, Badge, Alert } from '../components/common';
import { DOCUMENT_STATUS_COLORS, DOCUMENT_STATUS_LABELS } from '../utils/constants';
import { formatDate } from '../utils/helpers';

export default function Documents() {
  const { data: documents, isLoading, isError } = useDocuments({ 
    page: 1, 
    order: { createdAt: 'desc' } 
  });
  
  const deleteMutation = useDeleteDocument();

  const handleDelete = async (id) => {
    if (window.confirm('Êtes-vous sûr de vouloir supprimer ce document ?')) {
      await deleteMutation.mutateAsync(id);
    }
  };

  if (isLoading) {
    return (
      <div className="flex justify-center py-12">
        <Spinner size="lg" />
      </div>
    );
  }

  if (isError) {
    return (
      <Alert variant="danger" title="Erreur">
        Impossible de charger vos documents. Veuillez réessayer plus tard.
      </Alert>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-secondary-900">Mes documents</h1>
          <p className="text-secondary-600">
            Gérez tous vos documents créés
          </p>
        </div>
        <Link to="/documents/new">
          <Button leftIcon={<PlusIcon className="h-5 w-5" />}>
            Nouveau document
          </Button>
        </Link>
      </div>

      <Card>
        <div className="overflow-x-auto">
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="bg-secondary-50 border-b border-secondary-200 text-xs uppercase text-secondary-500 font-medium">
                <th className="px-6 py-4">Document</th>
                <th className="px-6 py-4">Modèle</th>
                <th className="px-6 py-4">Statut</th>
                <th className="px-6 py-4">Date de création</th>
                <th className="px-6 py-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-secondary-200">
              {documents?.data?.length > 0 ? (
                documents.data.map((doc) => (
                  <tr key={doc.id} className="hover:bg-secondary-50/50 transition-colors">
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        <div className="h-8 w-8 rounded bg-primary-100 text-primary-600 flex items-center justify-center">
                          <DocumentTextIcon className="h-5 w-5" />
                        </div>
                        <Link 
                          to={`/documents/${doc.id}`}
                          className="font-medium text-secondary-900 hover:text-primary-600"
                        >
                          {doc.title}
                        </Link>
                      </div>
                    </td>
                    <td className="px-6 py-4 text-secondary-600">
                      {doc.template?.name}
                    </td>
                    <td className="px-6 py-4">
                      <Badge variant={DOCUMENT_STATUS_COLORS[doc.status]}>
                        {DOCUMENT_STATUS_LABELS[doc.status]}
                      </Badge>
                    </td>
                    <td className="px-6 py-4 text-secondary-600 text-sm">
                      {formatDate(doc.createdAt)}
                    </td>
                    <td className="px-6 py-4 text-right">
                      <div className="flex items-center justify-end gap-2">
                        <Link to={`/documents/${doc.id}`}>
                          <Button size="sm" variant="ghost" className="p-2">
                            <EyeIcon className="h-4 w-4" />
                          </Button>
                        </Link>
                        <Link to={`/documents/${doc.id}/edit`}>
                          <Button size="sm" variant="ghost" className="p-2">
                            <PencilIcon className="h-4 w-4" />
                          </Button>
                        </Link>
                        <Button 
                          size="sm" 
                          variant="ghost" 
                          className="p-2 text-danger-600 hover:bg-danger-50"
                          onClick={() => handleDelete(doc.id)}
                        >
                          <TrashIcon className="h-4 w-4" />
                        </Button>
                      </div>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan="5" className="px-6 py-12 text-center text-secondary-500">
                    <DocumentTextIcon className="h-12 w-12 mx-auto mb-3 text-secondary-300" />
                    <p>Aucun document trouvé</p>
                    <Link to="/documents/new" className="mt-4 inline-block">
                      <Button size="sm">Créer mon premier document</Button>
                    </Link>
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </Card>
    </div>
  );
}
