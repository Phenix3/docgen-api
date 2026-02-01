import React from 'react';
import { Link } from 'react-router-dom';
import { 
  DocumentTextIcon, 
  PlusIcon, 
  ArrowRightIcon 
} from '@heroicons/react/24/outline';
import { useAuth } from '../hooks/useAuth';
import { useDocuments } from '../hooks/useDocuments';
import { useTemplates } from '../hooks/useTemplates';
import { Card, CardHeader, CardBody, Button, Spinner, Badge } from '../components/common';
import { DOCUMENT_STATUS_COLORS, DOCUMENT_STATUS_LABELS } from '../utils/constants';
import { formatDate } from '../utils/helpers';

export default function Dashboard() {
  const { user } = useAuth();
  const { data: recentDocs, isLoading: loadingDocs } = useDocuments({ 
    page: 1, 
    itemsPerPage: 5,
    order: { createdAt: 'desc' } 
  });
  const { data: popularTemplates, isLoading: loadingTemplates } = useTemplates({
    page: 1,
    itemsPerPage: 3
  });

  return (
    <div className="space-y-6">
      {/* Welcome Section */}
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-secondary-900">
            Bonjour, {user?.name?.split(' ')[0]} 👋
          </h1>
          <p className="text-secondary-600">
            Voici ce qui se passe sur votre compte aujourd'hui.
          </p>
        </div>
        <Link to="/documents/new">
          <Button leftIcon={<PlusIcon className="h-5 w-5" />}>
            Nouveau document
          </Button>
        </Link>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Card className="bg-gradient-to-br from-primary-500 to-primary-600 text-white border-none">
          <CardBody>
            <p className="text-primary-100 font-medium">Documents créés</p>
            <p className="text-3xl font-bold mt-2">{user?.stats?.documentsCount || 0}</p>
          </CardBody>
        </Card>
        
        <Card>
          <CardBody>
            <p className="text-secondary-500 font-medium">Quota mensuel</p>
            <div className="flex items-end gap-2 mt-2">
              <p className="text-3xl font-bold text-secondary-900">{user?.quota?.used || 0}</p>
              <p className="text-secondary-500 mb-1">/ {user?.quota?.limit || 3}</p>
            </div>
            <div className="mt-4 h-2 bg-secondary-100 rounded-full overflow-hidden">
              <div 
                className="h-full bg-primary-600 rounded-full"
                style={{ width: `${Math.min(100, ((user?.quota?.used || 0) / (user?.quota?.limit || 3)) * 100)}%` }}
              />
            </div>
          </CardBody>
        </Card>

        <Card>
          <CardBody>
            <p className="text-secondary-500 font-medium">Abonnement</p>
            <div className="flex items-center justify-between mt-2">
              <p className="text-3xl font-bold text-secondary-900 capitalize">
                {user?.subscriptionType || 'Gratuit'}
              </p>
              {user?.subscriptionType === 'free' && (
                <Button size="sm" variant="outline">Mettre à niveau</Button>
              )}
            </div>
          </CardBody>
        </Card>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Recent Documents */}
        <div className="lg:col-span-2">
          <Card className="h-full">
            <CardHeader className="flex items-center justify-between">
              <h2 className="text-lg font-semibold text-secondary-900">Documents récents</h2>
              <Link to="/documents" className="text-sm text-primary-600 hover:text-primary-700 font-medium">
                Voir tout
              </Link>
            </CardHeader>
            <CardBody>
              {loadingDocs ? (
                <div className="flex justify-center py-8">
                  <Spinner />
                </div>
              ) : recentDocs?.data?.length > 0 ? (
                <div className="space-y-4">
                  {recentDocs.data.map((doc) => (
                    <div key={doc.id} className="flex items-center justify-between p-4 bg-secondary-50 rounded-lg group hover:bg-secondary-100 transition-colors">
                      <div className="flex items-center gap-4">
                        <div className="h-10 w-10 rounded-lg bg-white flex items-center justify-center text-primary-600 shadow-sm">
                          <DocumentTextIcon className="h-6 w-6" />
                        </div>
                        <div>
                          <h3 className="font-medium text-secondary-900 group-hover:text-primary-600 transition-colors">
                            <Link to={`/documents/${doc.id}`}>{doc.title}</Link>
                          </h3>
                          <p className="text-sm text-secondary-500">
                            {doc.template?.name} • {formatDate(doc.createdAt)}
                          </p>
                        </div>
                      </div>
                      <Badge variant={DOCUMENT_STATUS_COLORS[doc.status]}>
                        {DOCUMENT_STATUS_LABELS[doc.status]}
                      </Badge>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="text-center py-8">
                  <DocumentTextIcon className="h-12 w-12 text-secondary-300 mx-auto mb-3" />
                  <p className="text-secondary-500">Aucun document pour le moment</p>
                  <Link to="/documents/new" className="mt-4 inline-block">
                    <Button size="sm">Créer mon premier document</Button>
                  </Link>
                </div>
              )}
            </CardBody>
          </Card>
        </div>

        {/* Popular Templates */}
        <div>
          <Card className="h-full">
            <CardHeader>
              <h2 className="text-lg font-semibold text-secondary-900">Modèles populaires</h2>
            </CardHeader>
            <CardBody>
              {loadingTemplates ? (
                <div className="flex justify-center py-8">
                  <Spinner />
                </div>
              ) : (
                <div className="space-y-4">
                  {popularTemplates?.map((template) => (
                    <Link 
                      key={template.id} 
                      to={`/documents/new?template=${template.id}`}
                      className="block p-4 border border-secondary-200 rounded-lg hover:border-primary-500 hover:shadow-sm transition-all"
                    >
                      <h3 className="font-medium text-secondary-900">{template.name}</h3>
                      <p className="text-sm text-secondary-500 mt-1 line-clamp-2">
                        {template.description}
                      </p>
                      <div className="mt-3 flex items-center text-sm text-primary-600 font-medium">
                        Utiliser ce modèle <ArrowRightIcon className="h-4 w-4 ml-1" />
                      </div>
                    </Link>
                  ))}
                </div>
              )}
            </CardBody>
          </Card>
        </div>
      </div>
    </div>
  );
}
